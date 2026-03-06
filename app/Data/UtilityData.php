<?php

namespace App\Data;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class UtilityData extends Data
{
    public function __construct(
        #[Required]
        #[StringType]
        #[Max(255)]
        public ?string $name,

        #[Required]
        #[StringType]
        #[Max(255)]
        public ?string $icon = null,
    ) {}

    public static function messages(): array
    {
        return [
            'name.required' => 'Tên tiện ích không được để trống',
            'name.max' => 'Tên tiện ích tối đa 255 ký tự',
            'icon.required' => 'Vui lòng chọn một biểu tượng',
        ];
    }
}
