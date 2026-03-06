<?php

namespace App\Data;

use DateTime;
use Spatie\LaravelData\Data;

class BookingData extends Data
{
    public function __construct(
        public string $email,
        public string $phone_number,
        public string $first_name,
        public string $last_name,
        public string $country,
        public DateTime $booking_date,
        public DateTime $checkout_date,
        public DateTime $checkout_time,
        public array $booking_details,
    ) {}
}