<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class EquipmentCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $equipmentCategory = $this->route('equipment_category');
        $excludeId = $equipmentCategory ? $equipmentCategory->id : null;
        
        return [
            'name' => 'required|string|max:100|unique:equipment_categories,name' . ($excludeId ? ',' . $excludeId . ',id' : ''),
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Tên loại thiết bị là bắt buộc',
            'name.string' => 'Tên loại thiết bị phải là chuỗi ký tự',
            'name.max' => 'Tên loại thiết bị không được vượt quá 100 ký tự',
            'name.unique' => 'Tên loại thiết bị đã tồn tại',
        ];
    }
}
