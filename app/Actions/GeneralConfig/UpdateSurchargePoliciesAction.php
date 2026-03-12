<?php

namespace App\Actions\GeneralConfig;

use App\Models\SurchargePolicy;
use Illuminate\Support\Facades\DB;

class UpdateSurchargePoliciesAction
{
    /**
     * @param string $policyType 'checkin_early' | 'checkout_late'
     * @param array  $rows       [['hour_mark' => int, 'price' => numeric], ...]
     */
    public function handle(string $policyType, array $rows): void
    {
        DB::transaction(function () use ($policyType, $rows) {
            // Xóa các chính sách cũ của loại này
            SurchargePolicy::where('policy_type', $policyType)->delete();

            // Tạo mới các chính sách từ dữ liệu đã cho
            foreach ($rows as $row) {
                $hourMark = (int) ($row['hour_mark'] ?? 0);
                $price = (float) ($row['price'] ?? 0);
                if($hourMark <= 0 || $price < 0) {
                    continue; // Bỏ qua các dòng không hợp lệ
                }
                SurchargePolicy::create([
                    'policy_type' => $policyType,
                    'hour_mark' => $hourMark,
                    'price' => $price,
                ]);
            }
        });
    }
}