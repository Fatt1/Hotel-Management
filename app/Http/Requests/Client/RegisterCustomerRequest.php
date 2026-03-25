<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class RegisterCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'last_name' => ['required', 'string', 'max:255', 'regex:/^\p{L}+(?:\s+\p{L}+)*$/u'],
            'first_name' => ['required', 'string', 'max:255', 'regex:/^\p{L}+(?:\s+\p{L}+)*$/u'],
            'phone_number' => ['required', 'regex:/^[0-9]{10}$/'],
            'country' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:customers,email'],
        ];
    }

    public function messages(): array
    {
        return [
            'last_name.required' => 'Vui lòng nhập họ.',
            'last_name.regex' => 'Họ chỉ được chứa chữ cái và khoảng trắng, không chứa số hoặc ký tự đặc biệt.',
            'first_name.required' => 'Vui lòng nhập tên.',
            'first_name.regex' => 'Tên chỉ được chứa chữ cái và khoảng trắng, không chứa số hoặc ký tự đặc biệt.',
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không hợp lệ.',
            'email.unique' => 'Email đã tồn tại.',
            'phone_number.required' => 'Vui lòng nhập số điện thoại.',
            'phone_number.regex' => 'Số điện thoại phải gồm đúng 10 chữ số.',
            'country.required' => 'Vui lòng chọn quốc gia.',
        ];
    }
}
