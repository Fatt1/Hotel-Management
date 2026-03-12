<?php

namespace App\Actions\Bookings;

use App\Models\Booking;
use App\Models\BookingDetail;
use App\Models\Room;
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

            // Create booking detail
            $bookingDetail = BookingDetail::create([
                'booking_id'    => $bookingId,
                'room_id'       => $room->id,
                'checkin_date'  => $data['checkin_date'],
                'checkout_date' => $data['checkout_date'],
                'hourly_price'  => $room->roomType->hourly_price,
                'daily_price'   => $room->roomType->daily_price,
            ]);

            // Recalculate booking amounts after adding room
            $this->recalculateBookingAmountsAction->execute($bookingId);

            return $bookingDetail->load('room.roomType', 'serviceUsages.service');
        });
    }
}
