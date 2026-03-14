<?php

namespace App\Actions\Bookings;
use App\Models\Booking;

class RecalculateBookingAmountsAction
{
    /**
     * Recalculate all amounts for a booking:
     * - Service amount for each booking detail
     * - Total room amount
     * - Total service amount  
     * - Total surcharge amount
     * - Final amount
     */
    public function execute(int $bookingId): void
    {
        $booking = Booking::with('bookingDetails')->findOrFail($bookingId);
        
        $totalRoomAmount = 0;
        $totalServiceAmount = 0;
        $totalSurchargeAmount = 0;
       
        foreach ($booking->bookingDetails as $detail) {
            $totalRoomAmount += $detail->room_amount;
            $totalServiceAmount += $detail->service_amount;
            $totalSurchargeAmount += $detail->surcharge_amount;
        }

        // Update booking totals
        $booking->update([
            'total_room_amount' => $totalRoomAmount,
            'total_service_amount' => $totalServiceAmount,
            'surcharge_amount' => $totalSurchargeAmount,
            'final_amount' => $totalRoomAmount + $totalServiceAmount + $totalSurchargeAmount,
        ]);
    }
}
