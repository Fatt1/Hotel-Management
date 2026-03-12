<?php
namespace App\Actions\Bookings; 

use App\Models\Payment;

class CreatePaymentAction
{
    public function execute(array $input)
    {
        $payment = Payment::create([
            'booking_id' => $input['booking_id'],
            'amount' => $input['amount'],
            'payment_method' => $input['payment_method'],
            'staff_id' => auth('staff')->user()->id,
        ]);
        return $payment;
      
    }
}