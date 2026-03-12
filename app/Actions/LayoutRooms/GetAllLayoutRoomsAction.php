<?php

namespace App\Actions\LayoutRooms;

use App\Enums\RoomLayoutStatus;
use App\Enums\RoomStatus;
use App\Models\Room;
use App\ViewModels\LayoutRoomIndexViewModel;
use App\ViewModels\RoomLayoutViewModel;
use Carbon\Carbon;

class GetAllLayoutRoomsAction
{
    private const ARRIVING_THRESHOLD_HOURS = 4;
    
    public function execute(
        ?string $filterDate = null,
        ?string $filterStatus = null,
        string $groupBy = 'type'
    ): LayoutRoomIndexViewModel {
        $date = $filterDate ? Carbon::parse($filterDate) : Carbon::today();
        $dateStart = $date->copy()->startOfDay();
        $dateEnd = $date->copy()->endOfDay();
        $now = Carbon::now();

        $rooms = Room::with([
            'roomType:id,name,code',
            'floor:id,name',
            'bookingDetails' => function ($query) use ($dateStart, $dateEnd) {
                $query->where('checkin_date', '<=', $dateEnd)
                    ->where('checkout_date', '>=', $dateStart)
                    ->whereHas('booking', function ($q) {
                        $q->whereNotIn('status', ['Hoàn tất', 'Đã hủy']);
                    })
                    ->with('booking.customer:id,first_name,last_name')
                    ->orderBy('checkin_date');
            }
        ])->orderBy('name')->get();

        $allRooms = $rooms->map(function ($room) use ($now) {
            $viewModel = $this->determineRoomStatus($room, $now);
            $viewModel->floorName = $room->floor->name ?? 'N/A';
            $viewModel->floorId = $room->floor->id ?? 0;
            return $viewModel;
        });

        return new LayoutRoomIndexViewModel(
            $allRooms, 
            $date->format('Y-m-d'), 
            $filterStatus, 
            $groupBy
        );
    }

    private function determineRoomStatus(Room $room, Carbon $now): RoomLayoutViewModel
    {
        $viewModel = new RoomLayoutViewModel(
            $room->id,
            $room->name,
            $room->roomType->code ?? 'N/A',
            RoomLayoutStatus::AVAILABLE
        );

        // Priority 1: Room is dirty/cleaning
        if ($room->status === RoomStatus::CLEANING->value) {
            $viewModel->status = RoomLayoutStatus::DIRTY;
            return $viewModel;
        }

        $bookingDetails = $room->bookingDetails;
        
        // Priority 2: No booking - Available
        if ($bookingDetails->isEmpty()) {
            return $viewModel;
        }

        // Priority 3: Multiple bookings in the day
        if ($bookingDetails->count() > 1) {
            return $this->handleMultipleBookings($viewModel, $bookingDetails, $now);
        }

        // Priority 4: Single booking
        return $this->handleSingleBooking($viewModel, $bookingDetails->first(), $now);
    }

    private function handleSingleBooking(
        RoomLayoutViewModel $viewModel, 
        $bookingDetail, 
        Carbon $now
    ): RoomLayoutViewModel {
        $booking = $bookingDetail->booking;

        if ($booking->status === 'Đang ở') {
            $viewModel->status = $now->greaterThan($bookingDetail->checkout_date) 
                ? RoomLayoutStatus::LATE_CHECKOUT 
                : RoomLayoutStatus::OCCUPIED;
            $viewModel->withBookingInfo($bookingDetail, $booking);
        } elseif (in_array($booking->status, ['Đã đặt', 'Đã xác nhận'])) {
            $viewModel->status = $this->getReservedStatus($bookingDetail->checkin_date, $now);
            $viewModel->withBookingInfo($bookingDetail, $booking);
        }

        return $viewModel;
    }

    private function handleMultipleBookings(
        RoomLayoutViewModel $viewModel,
        $bookingDetails,
        Carbon $now
    ): RoomLayoutViewModel {
        $sortedBookings = $bookingDetails->sortBy('checkin_date');
        
        // Find occupied booking (priority)
        $occupiedBooking = $sortedBookings->first(
            fn($bd) => $bd->booking->status === 'Đang ở'
        );
        
        if ($occupiedBooking) {
            $viewModel->status = RoomLayoutStatus::OCCUPIED;
            $viewModel->withBookingInfo($occupiedBooking, $occupiedBooking->booking);
            
            // Find next reserved booking
            $reservedBooking = $sortedBookings->first(
                fn($bd) => $bd->id !== $occupiedBooking->id && 
                           in_array($bd->booking->status, ['Đã đặt', 'Đã xác nhận'])
            );
            
            if ($reservedBooking) {
                $viewModel->withSecondaryBooking($reservedBooking, $reservedBooking->booking);
            }
            
            return $viewModel;
        }

        // No occupied booking - use earliest reserved
        $firstBooking = $sortedBookings->first();
       
        $viewModel->status = $this->getReservedStatus($firstBooking->checkin_date, $now);
        $viewModel->withBookingInfo($firstBooking, $firstBooking->booking);
        
    
        return $viewModel;
    }

    private function getReservedStatus(string $checkinDate, Carbon $now): RoomLayoutStatus
    {
        $hoursUntilCheckin = $now->diffInHours($checkinDate, false);
        
        return ($hoursUntilCheckin <= self::ARRIVING_THRESHOLD_HOURS && $hoursUntilCheckin >= 0)
            ? RoomLayoutStatus::ARRIVING
            : RoomLayoutStatus::RESERVED;
    }
}
