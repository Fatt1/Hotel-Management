<?php

declare(strict_types=1);

namespace App\Enums;

enum RoomTypeStatus: int
{
    case INACTIVE = 0;      // Không hoạt động
    case ACTIVE = 1;        // Đang hoạt động

    public function label(): string
    {
        return match ($this) {
            self::INACTIVE => 'Không hoạt động',
            self::ACTIVE => 'Đang hoạt động',
        };
    }

    public static function options(): array
    {
        return array_map(fn($case) => [
            'value' => $case->value,
            'label' => $case->label(),
        ], self::cases());
    }
}
