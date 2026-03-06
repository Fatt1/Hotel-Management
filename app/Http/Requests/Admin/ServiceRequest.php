<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'       => ['required', 'string', 'max:255'],
            'group_id'   => ['required', 'integer', 'exists:service_groups,id'],
            'unit_price' => ['required', 'numeric', 'min:0'],
            'unit'       => ['required', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'       => 'Vui lòng nhập tên dịch vụ.',
            'group_id.required'   => 'Vui lòng chọn nhóm dịch vụ.',
            'group_id.exists'     => 'Nhóm dịch vụ không hợp lệ.',
            'unit_price.required' => 'Vui lòng nhập đơn giá.',
            'unit_price.numeric'  => 'Đơn giá phải là số.',
            'unit_price.min'      => 'Đơn giá không được âm.',
            'unit.required'       => 'Vui lòng nhập đơn vị tính.',
        ];
    }
}
