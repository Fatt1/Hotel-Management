<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Staff;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StaffSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the staff table with sample data.
     */
    public function run(): void
    {
        // Tạo hoặc lấy các role
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $managerRole = Role::firstOrCreate(['name' => 'Quản lý']);
        $receptionistRole = Role::firstOrCreate(['name' => 'Nhân viên lễ tân']);
        $houseKeepingRole = Role::firstOrCreate(['name' => 'Nhân viên vệ sinh']);
        $maintenanceRole = Role::firstOrCreate(['name' => 'Nhân viên bảo trì']);

        // Dữ liệu nhân viên mẫu
        $staffData = [
            // Tài khoản Admin chính
            [
                'first_name' => 'Admin',
                'last_name' => 'System',
                'email' => 'admin@gmail.com',
                'phone_number' => '0000000000',
                'role_id' => $adminRole->id,
                'is_active' => true,
                'password' => 'admin',
            ],
            [
                'first_name' => 'Nguyễn',
                'last_name' => 'Văn Phương',
                'email' => 'phuong.ng@urbanluze.com',
                'phone_number' => '0901234567',
                'role_id' => $adminRole->id,
                'is_active' => true,
                'password' => 'password123',
            ],
            [
                'first_name' => 'Trần',
                'last_name' => 'Thị Hoa',
                'email' => 'hoa.tt@urbanluze.com',
                'phone_number' => '0912345678',
                'role_id' => $managerRole->id,
                'is_active' => true,
                'password' => 'password123',
            ],
            [
                'first_name' => 'Lê',
                'last_name' => 'Văn Minh',
                'email' => 'minh.lv@urbanluze.com',
                'phone_number' => '0923456789',
                'role_id' => $receptionistRole->id,
                'is_active' => true,
                'password' => 'password123',
            ],
            [
                'first_name' => 'Phạm',
                'last_name' => 'Thu Thảo',
                'email' => 'thao.pt@urbanluze.com',
                'phone_number' => '0934567890',
                'role_id' => $receptionistRole->id,
                'is_active' => true,
                'password' => 'password123',
            ],
            [
                'first_name' => 'Đỗ',
                'last_name' => 'Bằng Khoa',
                'email' => 'khoa.db@urbanluze.com',
                'phone_number' => '0945678901',
                'role_id' => $houseKeepingRole->id,
                'is_active' => true,
                'password' => 'password123',
            ],
            [
                'first_name' => 'Vũ',
                'last_name' => 'Minh Huy',
                'email' => 'huy.vm@urbanluze.com',
                'phone_number' => '0956789012',
                'role_id' => $houseKeepingRole->id,
                'is_active' => true,
                'password' => 'password123',
            ],
            [
                'first_name' => 'Hoàng',
                'last_name' => 'Danh Long',
                'email' => 'long.hd@urbanluze.com',
                'phone_number' => '0967890123',
                'role_id' => $maintenanceRole->id,
                'is_active' => true,
                'password' => 'password123',
            ],
            [
                'first_name' => 'Đinh',
                'last_name' => 'Anh Tuấn',
                'email' => 'tuan.da@urbanluze.com',
                'phone_number' => '0978901234',
                'role_id' => $maintenanceRole->id,
                'is_active' => false,
                'password' => 'password123',
            ],
            [
                'first_name' => 'Ngô',
                'last_name' => 'Quốc Hùng',
                'email' => 'hung.nq@urbanluze.com',
                'phone_number' => '0989012345',
                'role_id' => $receptionistRole->id,
                'is_active' => true,
                'password' => 'password123',
            ],
            [
                'first_name' => 'Bùi',
                'last_name' => 'Thị Linh',
                'email' => 'linh.bt@urbanluze.com',
                'phone_number' => '0990123456',
                'role_id' => $houseKeepingRole->id,
                'is_active' => true,
                'password' => 'password123',
            ],
            [
                'first_name' => 'Tạ',
                'last_name' => 'Văn Hợp',
                'email' => 'hop.tv@urbanluze.com',
                'phone_number' => '0901112223',
                'role_id' => $maintenanceRole->id,
                'is_active' => true,
                'password' => 'password123',
            ],
            [
                'first_name' => 'Võ',
                'last_name' => 'Thanh Tâm',
                'email' => 'tam.vt@urbanluze.com',
                'phone_number' => '0912223334',
                'role_id' => $receptionistRole->id,
                'is_active' => true,
                'password' => 'password123',
            ],
        ];

        // Tạo các nhân viên
        foreach ($staffData as $data) {
            Staff::updateOrCreate(
                ['email' => $data['email']],
                $data
            );
        }
    }
}
