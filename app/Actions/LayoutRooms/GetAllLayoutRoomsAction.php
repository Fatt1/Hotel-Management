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
    public function execute(
        ?string $filterDate = null,
        ?string $filterStatus = null,
        string $groupBy = 'type'
    ): LayoutRoomIndexViewModel {
        $date = $filterDate ? Carbon::parse($filterDate) : Carbon::today();
        $dateStart = $date->copy()->startOfDay();
        $dateEnd = $date->copy()->endOfDay();
        $now = Carbon::now();
        $dateStr = $date->format('Y-m-d');

        $rooms = Room::with([
            'roomType:id,name,code',
            'floor:id,name',
            'bookingDetails' => function ($query) use ($dateStart, $dateEnd) {
                $query->where(function ($q) use ($dateStart, $dateEnd) {
                    $q->where('checkin_date', '<=', $dateEnd)
                      ->where('checkout_date', '>=', $dateStart);
                })
                ->with('booking.customer:id,first_name,last_name')
                ->orderBy('checkin_date');
            }
        ])->orderBy('name')->get();

        $allRooms = collect();
        foreach ($rooms as $room) {
            $viewModel = $this->determineRoomStatus($room, $now, $dateStart, $dateEnd);
            $viewModel->floorName = $room->floor->name ?? 'N/A';
            $viewModel->floorId = $room->floor->id ?? 0;
            $allRooms->push($viewModel);
        }

        return new LayoutRoomIndexViewModel($allRooms, $dateStr, $filterStatus, $groupBy);
    }

    private function determineRoomStatus(
        $room,
        Carbon $now,
        Carbon $dateStart,
        Carbon $dateEnd
    ): RoomLayoutViewModel {
        $viewModel = new RoomLayoutViewModel(
            $room->id,
            $room->name,
            $room->roomType->code ?? 'N/A',
            RoomLayoutStatus::AVAILABLE
        );

        // Kiểm tra status của phòng trước (ưu tiên cao nhất cho "Bẩn")
        if ($room->status === RoomStatus::CLEANING->value) {
            $viewModel->status = RoomLayoutStatus::DIRTY;
            return $viewModel;
        }

        // Kiểm tra booking trong ngày
        $bookingDetails = $room->bookingDetails;
        
        if ($bookingDetails->isEmpty()) {
            // Không có booking → Trống
            $viewModel->status = RoomLayoutStatus::AVAILABLE;
            return $viewModel;
        }

        // Nếu có nhiều booking trong ngày (checkout/checkin cùng ngày)
        if ($bookingDetails->count() > 1) {
            return $this->handleMultipleBookings($viewModel, $bookingDetails, $now);
        }

        // Xử lý trường hợp 1 booking
        $activeBooking = $bookingDetails->first();
        $booking = $activeBooking->booking;
        $checkinTime = $activeBooking->checkin_date;
        $checkoutTime = $activeBooking->checkout_date;

        // Xác định trạng thái dựa trên booking
        if ($booking->status === 'Đang ở') {
            // Đang có khách
            if ($now->greaterThan($checkoutTime)) {
                // Quá giờ checkout → Chưa đi
                $viewModel->status = RoomLayoutStatus::LATE_CHECKOUT;
            } else {
                // Vẫn trong thời gian ở → Có khách
                $viewModel->status = RoomLayoutStatus::OCCUPIED;
            }
            $viewModel->withBookingInfo($activeBooking, $booking);
        } elseif ($booking->status === 'Đã đặt' || $booking->status === 'Đã xác nhận') {
            // Đã đặt nhưng chưa check-in
            $hoursUntilCheckin = $now->diffInHours($checkinTime, false);
            
            if ($hoursUntilCheckin <= 4 && $hoursUntilCheckin >= 0) {
                // Sắp đến (trong 4 giờ tới)
                $viewModel->status = RoomLayoutStatus::ARRIVING;
            } else {
                // Đã đặt
                $viewModel->status = RoomLayoutStatus::RESERVED;
            }
            $viewModel->withBookingInfo($activeBooking, $booking);
        }

        return $viewModel;
    }

    private function handleMultipleBookings(
        RoomLayoutViewModel $viewModel,
        $bookingDetails,
        Carbon $now
    ): RoomLayoutViewModel {
        // Sắp xếp theo thời gian checkin
        $sortedBookings = $bookingDetails->sortBy('checkin_date');
        
        // Tìm booking đang ở (Đang ở) và booking đã đặt (Đã đặt)
        $occupiedBooking = $sortedBookings->first(fn($bd) => $bd->booking->status === 'Đang ở');
        $reservedBooking = $sortedBookings->first(fn($bd) => in_array($bd->booking->status, ['Đã đặt', 'Đã xác nhận']));
        
        // Nếu có booking đang ở, set làm primary
        if ($occupiedBooking) {
            $viewModel->status = RoomLayoutStatus::OCCUPIED;
            $viewModel->withBookingInfo($occupiedBooking, $occupiedBooking->booking);
            
            // Nếu có booking đã đặt, set làm secondary
            if ($reservedBooking) {
                $viewModel->withSecondaryBooking($reservedBooking, $reservedBooking->booking);
            }
        } else {
            // Không có người đang ở, lấy booking sớm nhất làm primary
            $firstBooking = $sortedBookings->first();
            $secondBooking = $sortedBookings->skip(1)->first();
            
            // Xác định status cho booking đầu tiên
            $hoursUntilCheckin = $now->diffInHours($firstBooking->checkin_date, false);
            if ($hoursUntilCheckin <= 4 && $hoursUntilCheckin >= 0) {
                $viewModel->status = RoomLayoutStatus::ARRIVING;
            } else {
                $viewModel->status = RoomLayoutStatus::RESERVED;
            }
            
            $viewModel->withBookingInfo($firstBooking, $firstBooking->booking);
            
            if ($secondBooking) {
                $viewModel->withSecondaryBooking($secondBooking, $secondBooking->booking);
            }
        }
        
        return $viewModel;
    }
}
