<?php

namespace App\Data;

use DateTime;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;

class BookingData extends Data
{
    public function __construct(
        public string $email,
        public string $phone_number,
        public string $first_name,
        public string $last_name,
        public string $country,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d H:i:s')]
        public DateTime $booking_date,
        public array $booking_details,
        public string $status,
        public ?array $payment = null,
    ) {}

    
}