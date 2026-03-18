<?php

declare(strict_types=1);

namespace App\Enums;

enum MaintenanceTicketStatus: string
{
	case PENDING = 'pending';
	case IN_PROGRESS = 'in_progress';
	case COMPLETED = 'completed';
	case CANCELLED = 'cancelled';

	public function label(): string
	{
		return match ($this) {
			self::PENDING => 'Đang chờ',
			self::IN_PROGRESS => 'Đang sửa',
			self::COMPLETED => 'Hoàn thành',
			self::CANCELLED => 'Đã hủy',
		};
	}

	public static function labelOf(string $value): string
	{
		return self::tryFrom($value)?->label() ?? ucfirst(str_replace('_', ' ', $value));
	}

	public static function options(): array
	{
		return collect(self::cases())
			->mapWithKeys(fn (self $status) => [$status->value => $status->label()])
			->toArray();
	}

	public static function values(): array
	{
		return array_map(fn (self $status) => $status->value, self::cases());
	}
}
