<?php

namespace App\Actions\Bookings;

use App\Models\Booking;
use App\Models\BookingDetail;
use App\Models\ServiceUsage;
use Illuminate\Support\Facades\DB;

class RemoveRoomFromBookingAction
{
    public function __construct(private RecalculateBookingAmountsAction $recalculateBookingAmountsAction)
    {
    }
    public function execute(int $bookingId, int $roomId): void
    {
        DB::transaction(function () use ($bookingId, $roomId) {
            $booking = Booking::findOrFail($bookingId);
            
            // Check if booking is completed
            if ($booking->status === 'Hoàn thất') {
                throw new \Exception('Không thể xóa phòng khỏi booking đã hoàn thất');
            }
            
            $bookingDetail = BookingDetail::where('booking_id', $bookingId)
                ->where('room_id', $roomId)
                ->firstOrFail();
            
            // Check if already checked out
            if ($bookingDetail->checkout_status) {
                throw new \Exception('Không thể xóa phòng đã checkout');
            }

            // Delete associated service usages first
            ServiceUsage::where('booking_detail_id', $bookingDetail->id)->delete();
            
            // Delete booking detail
            $bookingDetail->delete();
            
            // Recalculate booking amounts after removing room
            $this->recalculateBookingAmountsAction->execute($bookingId);
        });
    }
}
