<?php

namespace App\Actions\Bookings;

use App\Models\Booking;
use Carbon\Carbon;

class GetBookingConfirmationDataAction
{
    public function execute(Booking $booking): array
    {
        $rooms = [];
        $subtotal = 0;
        $checkInDate = now();
        $checkOutDate = now()->addDay();

        if ($booking->bookingDetails->isNotEmpty()) {
            foreach ($booking->bookingDetails as $detail) {
                // Ensure whole integer comparison for days to avoid float nights like 2.916666
                $start = Carbon::parse($detail->checkin_date)->startOfDay();
                $end = Carbon::parse($detail->checkout_date)->startOfDay();
                $detailNights = max(1, (int) $start->diffInDays($end));

                $lineTotal = (float) ($detail->room_amount ?? 0);
                if ($lineTotal <= 0) {
                    $lineTotal = (float) (($detail->daily_price ?? 0) * $detailNights);
                }

                $subtotal += $lineTotal;
                $rooms[] = [
                    'name' => $detail->room?->roomType?->name ?? ('Phong #' . $detail->room_id),
                    'qty' => 1,
                    'line_total' => $lineTotal,
                    'width' => $detail->room?->roomType?->width ?? 0,
                    'image_url' => $detail->room?->roomType?->images?->first()?->image_url ?? '',
                ];
            }

            // Get absolute min and max for the whole booking
            $checkInDate = Carbon::parse($booking->bookingDetails->min('checkin_date'));
            $checkOutDate = Carbon::parse($booking->bookingDetails->max('checkout_date'));
        }

        // Night calculation fixing for the entire booking
        $cIn = $checkInDate->copy()->startOfDay();
        $cOut = $checkOutDate->copy()->startOfDay();
        $totalNights = max(1, (int) $cIn->diffInDays($cOut));

        $bookingRef = 'UL-' . str_pad((string) $booking->id, 6, '0', STR_PAD_LEFT);
        
        $bookingData = [
            'booking_ref'  => $bookingRef,
            'check_in'     => $checkInDate->format('Y-m-d H:i:s'),
            'check_out'    => $checkOutDate->format('Y-m-d H:i:s'),
            'checkInDate'  => $checkInDate,
            'checkOutDate' => $checkOutDate,
            'adults'       => 2,
            'children'     => 0,
            'nights'       => $totalNights,
            'rooms'        => $rooms,
            'subtotal'     => $subtotal,
            'guest_name'   => trim(($booking->customer?->first_name ?? '') . ' ' . ($booking->customer?->last_name ?? '')),
            'guest_email'  => $booking->customer?->email ?? '',
            'confirmed_at' => $booking->created_at ? $booking->created_at->format('d/m/Y H:i') : now()->format('d/m/Y H:i'),
        ];

        return $bookingData;
    }
}
