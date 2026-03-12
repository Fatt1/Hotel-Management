<?php

namespace App\Actions\Bookings;

use App\Models\Booking;
use Carbon\Carbon;

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
        $booking = Booking::with('bookingDetails.serviceUsages')->findOrFail($bookingId);
        
        $totalRoomAmount = 0;
        $totalServiceAmount = 0;
        $totalSurchargeAmount = 0;

        foreach ($booking->bookingDetails as $detail) {
            // Calculate room amount for this detail
            $checkinDate = Carbon::parse($detail->checkin_date);
            $checkoutDate = Carbon::parse($detail->checkout_date);
            $days = (int) max($checkinDate->diffInDays($checkoutDate), 1);
            $roomAmount = $days * $detail->daily_price;
            
            // Calculate service amount for this detail
            $serviceAmount = 0;
            foreach ($detail->serviceUsages as $serviceUsage) {
                $serviceAmount += $serviceUsage->quantity * $serviceUsage->unit_price;
            }
            
            // Update booking detail amounts
            $detail->update([
                'service_amount' => $serviceAmount,
                // surcharge_amount is updated separately during checkout
            ]);
            
            // Accumulate totals
            $totalRoomAmount += $roomAmount;
            $totalServiceAmount += $serviceAmount;
            $totalSurchargeAmount += $detail->surcharge_amount ?? 0;
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
