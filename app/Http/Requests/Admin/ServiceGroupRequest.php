<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ServiceGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'service_name' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'service_name.required' => 'Vui lòng nhập tên loại dịch vụ.',
            'service_name.max'      => 'Tên loại dịch vụ không được vượt quá 255 ký tự.',
        ];
    }
}
