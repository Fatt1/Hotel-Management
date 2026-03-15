<?php

declare(strict_types=1);

namespace App\Enums;

enum RoomLayoutStatus: string
{
    case AVAILABLE = "available";        // Trống
    case RESERVED = "reserved";          // Đã đặt (chưa tới)
    case ARRIVING = "arriving";          // Sắp đến (trong vài giờ tới)
    case OCCUPIED = "occupied";          // Có khách (đang ở)
    case LATE_CHECKOUT = "late_checkout"; // Chưa đi (quá giờ checkout)
    case DIRTY = "dirty";                // Bẩn (cần dọn dẹp)
    
    public function getLabel(): string
    {
        return match($this) {
            self::AVAILABLE => 'Trống',
            self::RESERVED => 'Đã đặt',
            self::ARRIVING => 'Sắp đến',
            self::OCCUPIED => 'Có khách',
            self::LATE_CHECKOUT => 'Chưa đi',
            self::DIRTY => 'Bẩn',
        };
    }
    
    public function getColor(): string
    {
        return match($this) {
            self::AVAILABLE => 'green',
            self::RESERVED => 'blue',
            self::ARRIVING => 'purple',
            self::OCCUPIED => 'red',
            self::LATE_CHECKOUT => 'orange',
            self::DIRTY => 'gray',
        };
    }
}
