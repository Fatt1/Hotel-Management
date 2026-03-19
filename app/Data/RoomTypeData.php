<?php

declare(strict_types=1);

namespace App\Data;

use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Data;

class RoomTypeData extends Data
{
    public function __construct(
        #[Required]
        #[StringType]
        #[Max(100)]
        public string $name,

        #[Required]
        #[StringType]
        #[Max(100)]
        public string $code,

        #[Required]
        #[IntegerType]
        #[Min(1)]
        #[Max(10)]
        public int $adult_quantity,

        #[Required]
        #[IntegerType]
        #[Min(0)]
        #[Max(10)]
        public int $child_quantity,

        #[Required]
        #[IntegerType]
        #[Min(0)]
        #[Max(10)]
        public int $single_bed_quantity,

        #[Required]
        #[IntegerType]
        #[Min(0)]
        #[Max(10)]
        public int $double_bed_quantity,

        #[Required]
        #[Numeric]
        #[Min(1)]
        #[Max(1000)]
        public float $width,

        #[Required]
        #[Numeric]
        #[Min(1)]
        #[Max(1000)]
        public float $height,

        #[Required]
        #[Numeric]
        #[Min(0)]
        #[Max(1000000000)]
        public float $hourly_price,

        #[Required]
        #[Numeric]
        #[Min(0)]
        #[Max(1000000000)]
        public float $daily_price,

        #[StringType]
        #[Max(200)]
        public ?string $description = null,

        #[Required]
        #[IntegerType]
        #[Min(0)]
        #[Max(2)]
        public int $is_active = 1,
    ) {}

    public static function messages(...$args): array
    {
        return [
            'name.required' => 'Tên loại phòng là bắt buộc',
            'name.max' => 'Tên loại phòng không được vượt quá 100 ký tự',
            'code.required' => 'Mã loại phòng là bắt buộc',
            'code.unique' => 'Mã loại phòng đã tồn tại',
            'code.max' => 'Mã loại phòng không được vượt quá 100 ký tự',
            'adult_quantity.required' => 'Số người lớn là bắt buộc',
            'adult_quantity.integer' => 'Số người lớn phải là số nguyên',
            'adult_quantity.min' => 'Số người lớn phải ít nhất là 1',
            'child_quantity.required' => 'Số trẻ em là bắt buộc',
            'child_quantity.integer' => 'Số trẻ em phải là số nguyên',
            'single_bed_quantity.required' => 'Số giường đơn là bắt buộc',
            'single_bed_quantity.integer' => 'Số giường đơn phải là số nguyên',
            'double_bed_quantity.required' => 'Số giường đôi là bắt buộc',
            'double_bed_quantity.integer' => 'Số giường đôi phải là số nguyên',
            'description.max' => 'Mô tả không được vượt quá 200 ký tự',
            'width.required' => 'Chiều rộng là bắt buộc',
            'width.numeric' => 'Chiều rộng phải là số',
            'width.min' => 'Chiều rộng phải lớn hơn 0',
            'height.required' => 'Chiều dài/cao là bắt buộc',
            'height.numeric' => 'Chiều dài/cao phải là số',
            'height.min' => 'Chiều dài/cao phải lớn hơn 0',
            'hourly_price.required' => 'Giá theo giờ là bắt buộc',
            'hourly_price.numeric' => 'Giá theo giờ phải là số',
            'hourly_price.min' => 'Giá theo giờ không được âm',
            'daily_price.required' => 'Giá theo ngày là bắt buộc',
            'daily_price.numeric' => 'Giá theo ngày phải là số',
            'daily_price.min' => 'Giá theo ngày không được âm',
            'is_active.required' => 'Trạng thái là bắt buộc',
        ];
    }
}
