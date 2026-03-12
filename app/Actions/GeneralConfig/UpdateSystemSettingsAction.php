<?php

namespace App\Actions\GeneralConfig;

use App\Models\SystemSetting;

class UpdateSystemSettingsAction
{
    public function handle(string $checkinTime, string $checkoutTime, int $roundingTime): void
    {
        $setting = [
            'checkin_time' => $checkinTime,
            'checkout_time' => $checkoutTime,
            'rounding_time' => $roundingTime,
        ];
        foreach ($setting as $key => $value) {
            SystemSetting::where('setting_key', $key)->update(['setting_value' => $value]);
        }
    }
}