<?php

namespace App\Actions\Bookings;

use App\Actions\Bookings\RecalculateBookingAmountsAction;
use App\Models\Booking;
use App\Models\BookingDetail;
use App\Models\ServiceUsage;
use Illuminate\Support\Facades\DB;


class RemoveServiceAction
{
    public function __construct(private RecalculateBookingAmountsAction $recalculateBookingAmountsAction)
    {
        
    }
    public function execute(int $bookingId, int $roomId, int $serviceId): void
    {
        DB::transaction(function () use ($bookingId, $roomId, $serviceId) {
            $booking = Booking::findOrFail($bookingId);
            
            // Check if booking is completed
            if ($booking->status === 'Hoàn thất') {
                throw new \Exception('Không thể xóa dịch vụ khỏi booking đã hoàn thất');
            }
            
            $bookingDetail = BookingDetail::where('booking_id', $bookingId)
                ->where('room_id', $roomId)
                ->firstOrFail();
            
            // Check if already checked out
            if ($bookingDetail->checkout_status) {
                throw new \Exception('Không thể xóa dịch vụ của phòng đã checkout');
            }

            ServiceUsage::where('booking_detail_id', $bookingDetail->id)
                ->where('service_id', $serviceId)
                ->delete();
            
            $totalServiceAmount = 0;
            foreach($bookingDetail->serviceUsages as $usage) {
                    $totalServiceAmount += $usage->quantity * $usage->unit_price;
            }
            $bookingDetail->update([
                'service_amount' => $totalServiceAmount,
            ]);
            $this->recalculateBookingAmountsAction->execute($booking->id);
        });
    }
}
