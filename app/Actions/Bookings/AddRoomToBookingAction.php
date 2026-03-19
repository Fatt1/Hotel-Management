<?php

namespace App\Actions\Bookings;

use App\Models\Booking;
use App\Models\BookingDetail;
use App\Models\Room;
use DateTime;
use Illuminate\Support\Facades\DB;

class AddRoomToBookingAction
{
    public function __construct(private RecalculateBookingAmountsAction $recalculateBookingAmountsAction)
    {
    }
    public function execute(int $bookingId, array $data): BookingDetail
    {
        return DB::transaction(function () use ($bookingId, $data) {
            $booking = Booking::findOrFail($bookingId);
            
            // Check if booking is completed
            if ($booking->status === 'Hoàn thất') {
                throw new \Exception('Không thể thêm phòng vào booking đã hoàn thất');
            }
            
            $room = Room::with('roomType')->findOrFail($data['room_id']);
            $chargedDays = $this->calculateChargedDays(
                new DateTime($data['checkin_date']),
                new DateTime($data['checkout_date']),
            );
            $roomAmount = $room->roomType->daily_price * $chargedDays;
            // Create booking detail
            $bookingDetail = BookingDetail::create([
                'booking_id'    => $bookingId,
                'room_id'       => $room->id,
                'checkin_date'  => $data['checkin_date'],
                'checkout_date' => $data['checkout_date'],
                'room_amount'   => $roomAmount,
                'hourly_price'  => $room->roomType->hourly_price,
                'daily_price'   => $room->roomType->daily_price,
            ]);

            // Recalculate booking amounts after adding room
            $this->recalculateBookingAmountsAction->execute($bookingId);

            return $bookingDetail->load('room.roomType', 'serviceUsages.service');
        });
    }

    private function calculateChargedDays(DateTime $checkinDate, DateTime $checkoutDate): int
    {
        $seconds = max($checkoutDate->getTimestamp() - $checkinDate->getTimestamp(), 0);

        return max((int) ceil($seconds / 86400), 1);
    }
}
