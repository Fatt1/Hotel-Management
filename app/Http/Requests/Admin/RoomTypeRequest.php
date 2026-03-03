<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RoomTypeRequest extends FormRequest
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
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:100|unique:room_types,code' . ($this->room_type ? ',' . $this->room_type->id : ''),
            'adult_quantity' => 'required|integer|min:1|max:10',
            'child_quantity' => 'required|integer|min:0|max:10',
            'single_bed_quantity' => 'required|integer|min:0|max:10',
            'double_bed_quantity' => 'required|integer|min:0|max:10',
            'description' => 'nullable|string|max:200',
            'width' => 'required|numeric|min:1|max:999.99',
            'height' => 'required|numeric|min:1|max:999.99',
            'hourly_price' => 'required|numeric|min:0|max:999999.99',
            'daily_price' => 'required|numeric|min:0|max:999999.99',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
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
        ];
    }
}
