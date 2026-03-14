<?php

namespace App\Actions\Bookings;

use App\Models\Booking;
use App\Models\BookingDetail;
use App\Models\Room;
use DateTime;
use Illuminate\Support\Facades\DB;

class UpdateRoomDatesAction
{
    public function __construct(private RecalculateBookingAmountsAction $recalculateBookingAmountsAction) {}
    public function execute(int $bookingId, int $roomId, array $data): BookingDetail
    {
        return DB::transaction(function () use ($bookingId, $roomId, $data) {
            $booking = Booking::findOrFail($bookingId);

            // Check if booking is completed
            if ($booking->status === 'Hoàn thất') {
                throw new \Exception('Không thể cập nhật ngày cho booking đã hoàn thất');
            }

            $bookingDetail = BookingDetail::where('booking_id', $bookingId)
                ->where('room_id', $roomId)
                ->firstOrFail();


            // Check if already checked out
            if ($bookingDetail->checkout_status) {
                throw new \Exception('Không thể cập nhật phòng đã checkout');
            }

            // If booking is occupied (Đang ở), only allow updating checkout_date
            if ($booking->status === 'Đang ở') {
                $bookingDetail->update([
                    'checkout_date' => $data['checkout_date'],
                ]);
            } else {
                $room = Room::with('roomType')->findOrFail($data['room_id']);
                $days = max((new DateTime($data['checkin_date']))->diff(new DateTime($data['checkout_date']))->days, 1);
                $roomAmount = $room->roomType->daily_price * $days;
                // Otherwise, allow updating both dates
                $bookingDetail->update([
                    'checkin_date'  => $data['checkin_date'],
                    'checkout_date' => $data['checkout_date'],
                    'room_amount'   => $roomAmount,
                ]);
            }

            // Recalculate booking amounts after updating dates (affects room amount)
            $this->recalculateBookingAmountsAction->execute($bookingId);

            return $bookingDetail->fresh();
        });
    }
}
