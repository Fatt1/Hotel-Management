<?php

declare(strict_types=1);

namespace null\HotelManagement\App\Enums;

enum RoomStatus: string
{
    case READY = "ready";
    case MAINTENANCE = "maintenance";
    case CLEANING = "cleaning";

}
