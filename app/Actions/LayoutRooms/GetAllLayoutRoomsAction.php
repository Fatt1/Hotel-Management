<?php

namespace App\Actions\LayoutRooms;

use App\Enums\RoomLayoutStatus;
use App\Enums\RoomStatus;
use App\Models\Floor;
use App\Models\Room;
use App\ViewModels\RoomLayoutViewModel;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class GetAllLayoutRoomsAction
{
    /**
     * Lấy tất cả phòng với trạng thái layout trong ngày được chọn
     * 
     * @param string|null $filterDate - Ngày cần xem (Y-m-d), mặc định là hôm nay
     * @param string|null $statusFilter - Lọc theo trạng thái: available, reserved, arriving, occupied, late_checkout, dirty
     * @param string $groupBy - Group theo: type, floor, room
     * @return Collection<string, Collection<RoomLayoutViewModel>>
     */
    public function execute(
        ?string $filterDate = null,
        ?string $statusFilter = null,
        string $groupBy = 'type'
    ): Collection {
        $date = $filterDate ? Carbon::parse($filterDate) : Carbon::today();
        $dateStart = $date->copy()->startOfDay();
        $dateEnd = $date->copy()->endOfDay();
        $now = Carbon::now();

        // Load tất cả phòng với room type, floor và booking details trong ngày
        $rooms = Room::with([
            'roomType:id,name,code',
            'floor:id,name',
            'bookingDetails' => function ($query) use ($dateStart, $dateEnd) {
                $query->where(function ($q) use ($dateStart, $dateEnd) {
                    // Booking overlaps với ngày được chọn
                    $q->where('checkin_date', '<=', $dateEnd)
                      ->where('checkout_date', '>=', $dateStart);
                })
                ->with('booking.customer:id,first_name,last_name')
                ->orderBy('checkin_date');
            }
        ])->orderBy('name')->get();

        $roomLayouts = [];
        foreach ($rooms as $room) {
            $viewModel = $this->determineRoomStatus($room, $now, $dateStart, $dateEnd);
            
            // Thêm thông tin floor cho grouping
            $viewModel->floorName = $room->floor->name ?? 'N/A';
            $viewModel->floorId = $room->floor->id ?? 0;
            
            $roomLayouts[] = $viewModel;
        }

        $collection = collect($roomLayouts);

        // Lọc theo trạng thái nếu có
        if ($statusFilter && $statusFilter !== 'all') {
            $collection = $collection->filter(function ($room) use ($statusFilter) {
                return $room->status->value === $statusFilter;
            });
        }

        // Group theo yêu cầu
        return match ($groupBy) {
            'floor' => $collection->groupBy('floorName'),
            'room' => $collection->groupBy(fn($r) => 'Tất cả phòng'),
            default => $collection->groupBy('roomTypeCode'),
        };
    }

    /**
     * Xác định trạng thái phòng
     * @param \App\Models\Room|object $room
     */
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

    /**
     * Lấy số lượng phòng theo từng trạng thái
     */
    public function getStatusCounts(?string $filterDate = null): array
    {
        $rooms = $this->execute($filterDate, null, 'room')->flatten();
        
        $counts = [
            'available' => 0,
            'reserved' => 0,
            'arriving' => 0,
            'occupied' => 0,
            'late_checkout' => 0,
            'dirty' => 0,
        ];

        foreach ($rooms as $room) {
            $counts[$room->status->value]++;
        }

        return $counts;
    }

    /**
     * Lấy danh sách tầng
     */
    public function getAllFloors(): Collection
    {
        return Floor::orderBy('name')->get(['id', 'name']);
    }

    /**
     * Xử lý trường hợp phòng có nhiều booking trong cùng ngày
     */
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
