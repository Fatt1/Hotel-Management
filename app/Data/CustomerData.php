<?php
declare(strict_types=1);
namespace App\Data;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Data;

class CustomerData extends Data
{
    public function __construct(
        public string $first_name,
        public string $last_name,
        public string $phone_number,
        public string $country,
        public string $email,
        public ?string $password = null,
        public ?string $password_confirmation = null,
        public ?int $id = null,
    ) {
    }
    public static function messages(...$args): array
    {
        return [
            'first_name.required' => 'Vui lòng nhập tên',
            'last_name.required' => 'Vui lòng nhập họ',
            'phone_number.required' => 'Vui lòng nhập số điện thoại',
            'country.required' => 'Vui lòng nhập quốc gia',
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Email không hợp lệ',
            'email.unique' => 'Email đã tồn tại',
            'password.required' => 'Vui lòng nhập mật khẩu',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp',
        ];
    }
    public static function rules(): array
    {
        $customerId = $context->payload['id'] ?? null;
        $isUpdate = $customerId !== null;
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'country' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('customers', 'email')->ignore($customerId),
            ],
            'password' => $isUpdate ? 'nullable|string|min:6|confirmed' : 'required|string|min:6|confirmed',
        ];
    }
        
}


