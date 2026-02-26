<?php

namespace App\Data;

use DateTime;
use Ramsey\Uuid\Type\Decimal;
use Spatie\LaravelData\Data;

class BookingDetailData extends Data
{
    public function __construct(
        public ?int $booking_id = null,
        public int $room_id,    
        public DateTime $checkin_date,
        public DateTime $checkout_date,
        public string $status,
        public Decimal $hourly_price,
        public Decimal $daily_price,
        public Decimal $service_amount,
        public Decimal $surcharge_amount,
    ) {}
}