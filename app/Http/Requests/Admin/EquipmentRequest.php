<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class EquipmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'equipment_category_id' => 'required|exists:equipment_categories,id',
            'import_price' => 'nullable|numeric|min:0',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
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
