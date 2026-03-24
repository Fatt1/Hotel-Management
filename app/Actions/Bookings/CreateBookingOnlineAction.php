<?php

namespace App\Actions\Bookings;

use App\Data\CreateBookingOnlineData;
use App\Data\BookingData;
use App\Models\Room;
use App\Models\BookingDetail;
use App\Models\Payment;
use App\Models\Booking;
use Exception;
use Illuminate\Support\Facades\DB;

class CreateBookingOnlineAction
{
    public function __construct(
        private CreateBookingAction $createBookingAction
    ) {}

    public function execute(CreateBookingOnlineData $data): Booking
    {
        return DB::transaction(function () use ($data) {
            // Tìm danh sách phòng đã được đặt trong khoảng thời gian này
            $bookedRoomIds = BookingDetail::join('bookings', 'booking_details.booking_id', '=', 'bookings.id')
                ->whereIn('bookings.status', ['Đã đặt', 'Đang ở'])
                ->where('booking_details.checkin_date', '<', $data->checkout_date)
                ->where('booking_details.checkout_date', '>', $data->checkin_date)
                ->pluck('booking_details.room_id')
                ->toArray();

            $bookingDetailsPayload = [];
            $totalAmount = 0;

            foreach ($data->booking_details as $detail) {
                // detail = ['room_type_id' => ..., 'quantity' => ...]
                // Lấy phòng trống theo room_type_id
                $availableRooms = Room::with('roomType')->where('room_type_id', $detail['room_type_id'])
                    ->where('status', 'ready')
                    ->whereNotIn('id', $bookedRoomIds)
                    ->limit($detail['quantity'])
                    ->get();

                if ($availableRooms->count() < $detail['quantity']) {
                    throw new Exception("Không đủ phòng trống cho loại phòng bạn chọn (ID: " . $detail['room_type_id'] . "). Vui lòng chọn ngày khác hoặc giảm số lượng.");
                }

                $checkin = new \DateTime($data->checkin_date);
                $checkout = new \DateTime($data->checkout_date);
                $seconds = max($checkout->getTimestamp() - $checkin->getTimestamp(), 0);
                $chargedDays = max((int) ceil($seconds / 86400), 1);

                foreach ($availableRooms as $room) {
                    $bookingDetailsPayload[] = [
                        'room_id' => $room->id,
                        'checkin_date' => $data->checkin_date,
                        'checkout_date' => $data->checkout_date,
                        'services' => [] // Online không đặt kèm service ban đầu ở luồng này
                    ];

                    if ($room->roomType) {
                        $totalAmount += $room->roomType->daily_price * $chargedDays;
                    }
                }
            }

            // Map qua DTO cũ để tái sử dụng logic lưu DB
            $bookingData = new BookingData(
                email: $data->email,
                phone_number: $data->phone_number,
                first_name: $data->first_name,
                last_name: $data->last_name,
                country: $data->country,
                booking_date: new \DateTime($data->booking_date),
                booking_details: $bookingDetailsPayload,
                status: $data->status,
                payment: [
                    'amount' => $totalAmount,
                    'method' => 'cash'
                ]
            );

            $booking = $this->createBookingAction->execute($bookingData);

            return $booking;
        });
    }
}
