<?php

declare(strict_types=1);

namespace App\Enums;

enum RoomTypeStatus: int
{
    case INACTIVE = 0;      // Không hoạt động
    case ACTIVE = 1;        // Đang hoạt động
    case COMING_SOON = 2;   // Sắp ra mắt

    public function label(): string
    {
        return match ($this) {
            self::INACTIVE => 'Không hoạt động',
            self::ACTIVE => 'Đang hoạt động',
            self::COMING_SOON => 'Sắp ra mắt',
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
