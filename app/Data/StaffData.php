<?php

declare(strict_types=1);

namespace App\Data;

use Illuminate\Validation\Rule;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;

class StaffData extends Data
{
    public function __construct(
        public string $first_name,
        public string $last_name,
        public string $email,
        public string $phone_number,
        public int $role_id,
        public ?string $password = null,
        public ?bool $is_active = true,
        public ?int $id = null,
    ) {
    }

    public static function messages(...$args): array
    {
        return [
            'first_name.required' => 'Vui lòng nhập họ của nhân viên',
            'first_name.regex' => 'Họ không được chứa số',
            'last_name.required' => 'Vui lòng nhập tên của nhân viên',
            'last_name.regex' => 'Tên không được chứa số',
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Email không hợp lệ',
            'email.unique' => 'Email đã tồn tại trong hệ thống',
            'phone_number.required' => 'Vui lòng nhập số điện thoại',
            'phone_number.regex' => 'Số điện thoại phải có đúng 10 chữ số',
            'role_id.required' => 'Vui lòng chọn vai trò',
            'role_id.exists' => 'Vai trò không tồn tại',
            'password.required' => 'Vui lòng nhập mật khẩu',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự',
            'password_confirmation.required' => 'Vui lòng xác nhận mật khẩu',
            'password_confirmation.same' => 'Mật khẩu xác nhận không khớp',
            'password_confirmation.required_with' => 'Vui lòng xác nhận mật khẩu khi thay đổi mật khẩu',
        ];
    }

    public static function rules(ValidationContext|null $context = null): array
    {
        $id = $context?->payload['id'] ?? null;
        
        return [
            'first_name' => ['required', 'string', 'max:255', 'regex:/^[^\d]+$/'],
            'last_name' => ['required', 'string', 'max:255', 'regex:/^[^\d]+$/'], 
            'email' => [
                'required',
                'email:rfc,dns',
                Rule::unique('staffs', 'email')->ignore($id),
            ],
            'phone_number' => ['required', 'regex:/^\d{10}$/'],
            'role_id' => 'required|exists:roles,id',
            'password' => $id ? 'nullable|string|min:8' : 'required|string|min:8',
            'password_confirmation' => $id ? 'nullable|required_with:password|same:password' : 'required|same:password',
            'is_active' => 'boolean',
        ];
    }
}
