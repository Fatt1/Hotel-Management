<?php

namespace App\Actions\Bookings;

use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class RecordPaymentAction
{
    public function execute(int $bookingId, array $data): Payment
    {
        return DB::transaction(function () use ($bookingId, $data) {
            $amount = (float) $data['amount'];
            
            // If refund, make amount negative
            if ($data['payment_type'] === 'refund') {
                $amount = -$amount;
            }

            $payment = Payment::create([
                'booking_id'     => $bookingId,
                'amount'         => $amount,
                'payment_method' => $data['payment_method'],
                'staff_id'       => auth('staff')->id(),
            ]);

            return $payment;
        });
    }
}
