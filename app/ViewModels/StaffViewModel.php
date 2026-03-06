<?php

namespace App\ViewModels;

use App\Models\Role;
use App\Models\Staff;
use Illuminate\Support\Collection;

class StaffViewModel
{
    private ?Staff $staff;

    public function __construct(Staff $staff = null)
    {
        $this->staff = $staff;
    }

    /**
     * Trả về staff (mới hoặc existing)
     */
    public function staff(): Staff
    {
        return $this->staff ?? new Staff();
    }

    /**
     * Danh sách vai trò
     */
    public function roles(): Collection
    {
        return Role::select('id', 'name')
            ->orderBy('name')
            ->get();
    }

    /**
     * Kiểm tra là tạo mới hay cập nhật
     */
    public function isEditing(): bool
    {
        return $this->staff !== null && $this->staff->exists;
    }

    /**
     * Tiêu đề form
     */
    public function formTitle(): string
    {
        return $this->isEditing() ? 'Chỉnh sửa thông tin nhân viên' : 'Thêm nhân viên mới';
    }

    /**
     * Mô tả form
     */
    public function formDescription(): string
    {
        return $this->isEditing()
            ? 'Cập nhật thông tin nhân viên dưới đây.'
            : 'Điền thông tin cơ bản dưới đây để tạo tài khoản nhân viên mới vào hệ thống.';
    }

    /**
     * URL form action
     */
    public function formAction(): string
    {
        return $this->isEditing()
            ? route('admin.staffs.update', $this->staff->id)
            : route('admin.staffs.store');
    }

    /**
     * HTTP method
     */
    public function formMethod(): string
    {
        return $this->isEditing() ? 'PUT' : 'POST';
    }

    /**
     * Text nút submit
     */
    public function submitButtonText(): string
    {
        return $this->isEditing() ? 'Cập nhật' : 'Tạo nhân viên';
    }

    /**
     * Icon nút submit
     */
    public function submitButtonIcon(): string
    {
        return $this->isEditing() ? 'edit' : 'add_circle';
    }
}
