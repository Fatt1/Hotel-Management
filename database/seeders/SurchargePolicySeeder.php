<?php

namespace Database\Seeders;

use App\Enums\PolicyType;
use App\Models\SurchargePolicy;
use Illuminate\Database\Seeder;

class SurchargePolicySeeder extends Seeder
{
    public function run(): void
    {
        $policies = [
            // ── Checkin sớm ─────────────────────────────────────────────────
            // Checkin trước 14h trong vòng 2 giờ (12h–14h)
            [
                'policy_type' => PolicyType::CHECKIN_EARLY->value,
                'hour_mark'   => 2.00,
                'price'       => 100000,
            ],
            // Checkin trước 14h từ 2–6 giờ (8h–12h)
            [
                'policy_type' => PolicyType::CHECKIN_EARLY->value,
                'hour_mark'   => 3.00,
                'price'       => 200000,
            ],
            // Checkin rất sớm, trước 8h (> 6 giờ)
            [
                'policy_type' => PolicyType::CHECKIN_EARLY->value,
                'hour_mark'   => 4.00,
                'price'       => 350000,
            ],

            // ── Checkout muộn ────────────────────────────────────────────────
            // Checkout muộn trong vòng 2 giờ sau 12h (12h–14h) → tính theo giờ
            [
                'policy_type' => PolicyType::CHECKOUT_LATE->value,
                'hour_mark'   => 2.00,
                'price'       => 100000,
            ],
            // Checkout muộn 2–6 giờ sau 12h (14h–18h) → tính theo giờ
            [
                'policy_type' => PolicyType::CHECKOUT_LATE->value,
                'hour_mark'   => 3.00,
                'price'       => 200000,
            ],
            // Checkout muộn > 6 giờ sau 12h (sau 18h) → tính theo ngày
            [
                'policy_type' => PolicyType::CHECKOUT_LATE->value,
                'hour_mark'   => 4.00,
                'price'       => 350000,
            ],
        ];

        foreach ($policies as $policy) {
            SurchargePolicy::create($policy);
        }
    }
}
