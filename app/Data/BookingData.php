<?php

namespace App\Data;

use DateTime;
use Ramsey\Uuid\Type\Decimal;
use Spatie\LaravelData\Data;

class BookingData extends Data
{
    public function __construct(
        public int $customer_id,
        public DateTime $booking_date,
        public Decimal $total_service_amount,
        public Decimal $total_room_amount,
        public Decimal $surcharge_amount,
        public Decimal $final_amount,
        public array $booking_details,
    ) {}
}