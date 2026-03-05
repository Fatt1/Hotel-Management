<?php

declare(strict_types=1);

namespace App\ViewModels;

use App\Models\Customer;
use Illuminate\Support\Collection;

class CustomerViewModel
{
    private ?Customer $customer;
    public function __construct(
        ?Customer $customer = null
    ) {
        $this->customer = $customer;
    }

    public function customer(): Customer
    {
        return $this->customer ?? new Customer();
    }

    public function countries(): array
    {
        return ["Việt Nam", "Mỹ", "Anh", "Pháp", "Đức", "Nhật Bản", "Hàn Quốc", "Trung Quốc", "Thái Lan", "Malaysia"];
    }

    public function bookingHistory(): Collection
    {
        if(!$this->customer || !$this->customer->exists) {
            return collect();
        }
        return $this->customer->bookings->map( function($booking) {
            $details = $booking->bookingDetails;
            $checkinDate = $details->min('checkin_date');
            $checkoutDate = $details->max('checkout_date');
            return [
                'code'                  => '#BK-' . $booking->id,
                'checkin_date'          => $checkinDate
                    ? \Carbon\Carbon::parse($checkinDate)->format('d/m/Y')
                    : '—',
                'checkout_date'         => $checkoutDate
                    ? \Carbon\Carbon::parse($checkoutDate)->format('d/m/Y')
                    : '—',
                'total_room_amount'     => $booking->total_room_amount,
                'total_service_amount'  => $booking->total_service_amount,
                'final_amount'          => $booking->final_amount,
            ];
        }); 
    }
    public function formatCustomerId(): string
    {
        if (!$this->customer || !$this->customer->exists) {
            return "-";
        }
        return "CUS-" . str_pad((string)$this->customer->id, 4, '0', STR_PAD_LEFT);
    }
    public function formatAccountId(): string
    {
        if (!$this->customer || !$this->customer->exists){
            return "-";
        }
        return "ACC-" .str_pad((string)$this->customer->id,4,'0', STR_PAD_LEFT);
    }
}