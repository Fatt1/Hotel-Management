<?php
declare(strict_types=1);

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;

class SurchargePoliciesData extends Data
{
    public function __construct(
        public ?array $checkin_early = null,
        public ?array $checkout_late = null,
    ) {
    }

    public static function messages(...$args): array
    {
        return [
            'checkin_early.*.hour_mark.required' => 'Vui lòng nhập số giờ check-in sớm',
            'checkin_early.*.hour_mark.integer' => 'Số giờ check-in sớm phải là số nguyên',
            'checkin_early.*.hour_mark.min' => 'Số giờ check-in sớm tối thiểu là 1',
            'checkin_early.*.hour_mark.max' => 'Số giờ check-in sớm tối đa là 24',
            'checkin_early.*.price.required' => 'Vui lòng nhập mức phí check-in sớm',
            'checkin_early.*.price.numeric' => 'Mức phí check-in sớm phải là số',
            'checkin_early.*.price.min' => 'Mức phí check-in sớm không được âm',
            'checkout_late.*.hour_mark.required' => 'Vui lòng nhập số giờ check-out muộn',
            'checkout_late.*.hour_mark.integer' => 'Số giờ check-out muộn phải là số nguyên',
            'checkout_late.*.hour_mark.min' => 'Số giờ check-out muộn tối thiểu là 1',
            'checkout_late.*.hour_mark.max' => 'Số giờ check-out muộn tối đa là 24',
            'checkout_late.*.price.required' => 'Vui lòng nhập mức phí check-out muộn',
            'checkout_late.*.price.numeric' => 'Mức phí check-out muộn phải là số',
            'checkout_late.*.price.min' => 'Mức phí check-out muộn không được âm',
        ];
    }

    public static function rules(ValidationContext|null $context = null): array
    {
        return [
            'checkin_early' => ['nullable', 'array'],
            'checkin_early.*.hour_mark' => ['required', 'integer', 'min:1', 'max:24'],
            'checkin_early.*.price' => ['required', 'numeric', 'min:0'],
            'checkout_late' => ['nullable', 'array'],
            'checkout_late.*.hour_mark' => ['required', 'integer', 'min:1', 'max:24'],
            'checkout_late.*.price' => ['required', 'numeric', 'min:0'],
        ];
    }
}
