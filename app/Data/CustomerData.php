<?php
declare(strict_types=1);
namespace App\Data;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;

class CustomerData extends Data
{
    public function __construct(
        public string $last_name,
        public string $first_name,
        public string $phone_number,
        public string $country,
        public string $email,
        public ?int $id = null,
    ) {
    }
    public static function messages(...$args): array
    {
        return [
            'last_name.required' => 'Vui lòng nhập họ',
            'first_name.required' => 'Vui lòng nhập tên',
            'phone_number.required' => 'Vui lòng nhập số điện thoại',
            'country.required' => 'Vui lòng nhập quốc gia',
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Email không hợp lệ',
            'email.unique' => 'Email đã tồn tại',
        ];
    }
    public static function rules(ValidationContext|null $context = null): array
    {
        $customerId = $context->payload['id'] ?? null;
        $isUpdate = $customerId !== null;
        return [
            'last_name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'country' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('customers', 'email')->ignore($customerId),
            ],
        ];
    }
        
}


