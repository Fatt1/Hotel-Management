<?php
declare(strict_types=1);

namespace App\Data;

use Spatie\LaravelData\Data;

class SystemSettingsData extends Data
{
    public function __construct(
        public string $checkin_time,
        public string $checkout_time,
        public int $rounding_time,
    ) {
    }

    public static function messages(...$args): array
    {
        return [
            'checkin_time.required' => 'Vui lòng nhập thời gian check-in',
            'checkin_time.date_format' => 'Thời gian check-in không hợp lệ phải có định dạng HH:mm',
            'checkout_time.required' => 'Vui lòng nhập thời gian check-out',
            'checkout_time.date_format' => 'Thời gian check-out không hợp lệ phải có định dạng HH:mm',
            'rounding_time.required' => 'Vui lòng nhập số phút làm tròn',
            'rounding_time.integer' => 'Số phút làm tròn phải là số nguyên',
            'rounding_time.min' => 'Số phút làm tròn tối thiểu là 1',
            'rounding_time.max' => 'Số phút làm tròn tối đa là 59',
        ];
    }

    public static function rules(): array
    {
        return [
            'checkin_time' => ['required', 'date_format:H:i,H:i:s'],
            'checkout_time' => ['required', 'date_format:H:i,H:i:s'],
            'rounding_time' => ['required', 'integer', 'min:1', 'max:59'],
        ];
    }
}
