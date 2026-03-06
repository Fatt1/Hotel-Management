<?php

declare(strict_types=1);

namespace App\Data;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Data;

class EquipmentCategoryData extends Data
{
    public function __construct(
        #[Required]
        #[StringType]
        #[Max(100)]
        #[Unique('equipment_categories', 'name')]
        public string $name,
    ) {}

    public static function messages(): array
    {
        return [
            'name.required' => 'Tên loại thiết bị là bắt buộc',
            'name.string' => 'Tên loại thiết bị phải là chuỗi ký tự',
            'name.max' => 'Tên loại thiết bị không được vượt quá 100 ký tự',
            'name.unique' => 'Tên loại thiết bị đã tồn tại',
        ];
    }
}
