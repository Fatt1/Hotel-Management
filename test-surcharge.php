<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\SurchargePolicy;
use App\Enums\PolicyType;
use Carbon\Carbon;

$policies = SurchargePolicy::all();
$checkinDate = Carbon::parse('2026-03-12 09:15:00');
$standardCheckin = $checkinDate->copy()->setTime(14, 0, 0);

echo "=== DEBUG EARLY CHECKIN SURCHARGE ===" . PHP_EOL;
echo "Checkin: " . $checkinDate->toDateTimeString() . PHP_EOL;
echo "Standard: " . $standardCheckin->toDateTimeString() . PHP_EOL;
echo "Is Early: " . ($checkinDate->lt($standardCheckin) ? 'YES' : 'NO') . PHP_EOL;

if ($checkinDate->lt($standardCheckin)) {
    // Fix: Đảo ngược thứ tự để được số dương
    $hoursEarly = $checkinDate->diffInMinutes($standardCheckin) / 60.0;
    echo "Hours Early: " . $hoursEarly . PHP_EOL . PHP_EOL;
    
    echo "All policies:" . PHP_EOL;
    foreach ($policies as $p) {
        echo "  - type={$p->policy_type}, hour_mark={$p->hour_mark}, price={$p->price}" . PHP_EOL;
    }
    echo PHP_EOL;
    
    $filtered = $policies->filter(function($p) use ($hoursEarly) {
        $matches = $p->policy_type === PolicyType::CHECKIN_EARLY->value && (float) $p->hour_mark <= $hoursEarly;
        if ($matches) {
            echo "  ✓ Policy hour_mark={$p->hour_mark} matches (hour_mark <= {$hoursEarly})" . PHP_EOL;
        }
        return $matches;
    });
    
    echo "Filtered policies: " . $filtered->count() . PHP_EOL . PHP_EOL;
    
    $policy = $filtered->sortByDesc('hour_mark')->first();
    if ($policy) {
        echo "✓ Selected policy: hour_mark=" . $policy->hour_mark . ", price=" . $policy->price . PHP_EOL;
    } else {
        echo "✗ No policy found!" . PHP_EOL;
    }
}
