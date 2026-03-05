<?php

namespace App\Data;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Data;

class EquipmentData extends Data
{
    public function __construct(
        #[Required]
        #[StringType]
        #[Max(255)]
        public string $name,

        #[Required]
        #[Exists('equipment_categories', 'id')]
        public int $equipment_category_id,

        #[Numeric]
        #[Min(0)]
        public ?float $import_price = null,
    ) {}

    public static function messages(): array
    {
        return [
            'name.required' => 'Tên thiết bị không được để trống.',
            'name.string' => 'Tên thiết bị phải là chuỗi ký tự.',
            'name.max' => 'Tên thiết bị không được vượt quá 255 ký tự.',
            'equipment_category_id.required' => 'Vui lòng chọn loại thiết bị.',
            'equipment_category_id.exists' => 'Loại thiết bị không tồn tại.',
            'import_price.numeric' => 'Giá nhập phải là con số.',
            'import_price.min' => 'Giá nhập không được âm.',
        ];
    }
}
