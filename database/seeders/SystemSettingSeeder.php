<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SystemSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            [
                'setting_key'   => 'checkin_time',
                'setting_value' => '14:00',
                'description'   => 'Thời gian check-in tiêu chuẩn',
            ],
            [
                'setting_key'   => 'checkout_time',
                'setting_value' => '12:00',
                'description'   => 'Thời gian check-out tiêu chuẩn', 
            ],
            [
                'setting_key'   => 'rounding_time',
                'setting_value' => '15',
                'description'   => 'Thời gian làm tròn thành 1 giờ khi tính phí check-in sớm hoặc check-out muộn',
            ],
        ];

        foreach ($settings as $setting) {
            DB::table('system_settings')->updateOrInsert(
                ['setting_key' => $setting['setting_key']],
                $setting
            );
        }
    }
}