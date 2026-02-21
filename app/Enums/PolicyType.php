<?php

namespace App\Enums;
enum PolicyType:string
{
  case CHECKOUT_LATE = 'checkout_late';
  case CHECKIN_EARLY= 'checkin_early';

  public function label(): string
    {
        return match($this) {
            self::CHECKOUT_LATE => 'Checkout muộn',
            self::CHECKIN_EARLY => 'Checkin sớm',
        };
    }
}
