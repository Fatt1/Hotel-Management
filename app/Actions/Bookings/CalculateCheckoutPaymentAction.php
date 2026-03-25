<?php

namespace App\Actions\Bookings;

use App\Enums\PolicyType;
use App\Models\Booking;
use App\Models\BookingDetail;
use App\Models\SurchargePolicy;
use App\Models\SystemSetting;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class CalculateCheckoutPaymentAction
{
    private int $standardCheckinHour;
    private int $standardCheckoutHour;
    private int $roundingTime;

    private Collection $surchargePolicies;
    private Carbon $now;

    public function __construct()
    {
        // Load cấu hình ở runtime (PHP const không thể chứa query DB).
        $this->standardCheckinHour = (int) (SystemSetting::where('setting_key', 'checkin_time')->value('setting_value') ?? 14);
        $this->standardCheckoutHour = (int) (SystemSetting::where('setting_key', 'checkout_time')->value('setting_value') ?? 12);
        $this->roundingTime = (int) (SystemSetting::where('setting_key', 'rounding_time')->value('setting_value') ?? 15);
    }

    public function execute(array $input): array
    {
        $booking = Booking::with([
            'bookingDetails.room.roomType',
            'bookingDetails.serviceUsages.service',
            'payments.staff',
        ])->findOrFail($input['booking_id']);

        $selectedRoomIds = array_map('intval', $input['room_ids']);
        $this->now = Carbon::now();
        $this->surchargePolicies = SurchargePolicy::all();

        $roomBreakdowns = [];
        $totalRoomAmount = 0;
        $totalServiceAmount = 0;
        $totalSurchargeAmount = 0;

        foreach ($booking->bookingDetails as $detail) {
            if (!in_array((int) $detail->room_id, $selectedRoomIds)) {
                continue;
            }

            $breakdown = $this->calculateRoomBreakdown($detail);
            
            $totalRoomAmount += $breakdown['room_amount'];
            $totalServiceAmount += $breakdown['service_amount'];
            $totalSurchargeAmount += $breakdown['surcharge_amount'];
            
            $roomBreakdowns[] = $breakdown;
        }

        $sumTotal = $totalRoomAmount + $totalServiceAmount + $totalSurchargeAmount;
        $alreadyPaid = (float) $booking->payments->sum('amount');
        $remaining = $sumTotal - $alreadyPaid;

        return [
            'booking_id'           => $booking->id,
            'rooms'                => $roomBreakdowns,
            'total_room_amount'    => $totalRoomAmount,
            'total_service_amount' => $totalServiceAmount,
            'total_surcharge'      => $totalSurchargeAmount,
            'sum_total'            => $sumTotal,
            'already_paid'         => $alreadyPaid,
            'remaining'            => $remaining,
        ];
    }

    private function calculateRoomBreakdown(BookingDetail $detail): array
    {
        $checkinDate = Carbon::parse($detail->checkin_date);
        $checkoutDate = Carbon::parse($detail->checkout_date);

        // Tính tiền phòng
        $roomCost = $this->calculateRoomAmount($detail, $checkinDate, $checkoutDate);
        
        // Tính tiền dịch vụ
        $serviceData = $this->calculateServiceAmount($detail);
        
        // Tính phụ thu check-in sớm
        $earlyCheckinData = $this->calculateEarlyCheckinSurcharge($checkinDate);
        
        // Tính phụ thu checkout muộn
        $lateCheckoutData = $this->calculateLateCheckoutSurcharge($checkoutDate);
        
        $totalSurcharge = $earlyCheckinData['amount'] + $lateCheckoutData['amount'];

        return [
            'room_id'            => $detail->room_id,
            'room_name'          => $detail->room?->name,
            'room_type'          => $detail->room?->roomType?->name,
            'checkin_date'       => $checkinDate->toDateTimeString(),
            'checkout_date'      => $checkoutDate->toDateTimeString(),
            'actual_checkout'    => $this->now->toDateTimeString(),
            'charge_by_hour'     => $roomCost['charge_by_hour'],
            'hours_stayed'       => $roomCost['hours_stayed'],
            'days'               => $roomCost['days'],
            'hourly_price'       => (float) $detail->hourly_price,
            'daily_price'        => (float) $detail->daily_price,
            'room_amount'        => $roomCost['amount'],
            'services'           => $serviceData['list'],
            'service_amount'     => $serviceData['amount'],
            'early_checkin'      => $earlyCheckinData['info'],
            'late_checkout'      => $lateCheckoutData['info'],
            'surcharge_amount'   => $totalSurcharge,
            'room_total'         => $roomCost['amount'] + $serviceData['amount'] + $totalSurcharge,
        ];
    }

    private function calculateRoomAmount(BookingDetail $detail, Carbon $checkinDate, Carbon $checkoutDate): array
    {
        // Tổng số giờ từ checkin đến hiện tại (thực tế khách đang ở)
        $totalHoursStayed = $checkinDate->diffInMinutes($this->now) / 60.0;

        // Lấy hour_mark cao nhất trong policy CHECKOUT_LATE làm ngưỡng chuyển sang tính ngày
        $switchToDailyThreshold = $this->surchargePolicies
            ->filter(fn ($p) => $p->policy_type === PolicyType::CHECKOUT_LATE->value)
            ->sortByDesc('hour_mark')
            ->first();

        $chargeByHour = false;
        $hoursStayed = null;
        $days = null;
        $amount = 0;

        if ($switchToDailyThreshold && $totalHoursStayed <= (float) $switchToDailyThreshold->hour_mark) {
            // Số giờ ≤ ngưỡng → tính theo giá giờ
            $chargeByHour = true;
            $hoursStayed = round($totalHoursStayed, 2);

            $chargedHours = (int) floor($hoursStayed);
            $fraction = $hoursStayed - $chargedHours;
            if( $fraction > 0.25) {
                $chargedHours++;
            }
            // Tối thiểu tính 1 giờ nếu khách ở chưa đến 1 giờ
            if($chargedHours < 1) {
                $chargedHours = 1; // Tối thiểu tính 1 giờ
            }
            $amount = (float) $detail->hourly_price * $chargedHours;
        } else {
            $seconds = max($this->now->getTimestamp() - $checkinDate->getTimestamp(), 0);
            $days = max((int) ceil($seconds / 86400), 1);
            $amount = $detail->room_amount;
        }

        return [
            'charge_by_hour' => $chargeByHour,
            'hours_stayed'   => $hoursStayed,
            'days'           => $days,
            'amount'         => $amount,
        ];
    }

    private function calculateServiceAmount(BookingDetail $detail): array
    {
        $serviceList = [];
        $totalAmount = 0;

        foreach ($detail->serviceUsages as $usage) {
            $lineTotal = $usage->quantity * (float) $usage->unit_price;
            $totalAmount += $lineTotal;

            $serviceList[] = [
                'name'       => $usage->service?->name,
                'group'      => $usage->service?->group?->name,
                'quantity'   => $usage->quantity,
                'unit_price' => (float) $usage->unit_price,
                'total'      => $lineTotal,
            ];
        }

        return [
            'list'   => $serviceList,
            'amount' => $totalAmount,
        ];
    }

    private function calculateEarlyCheckinSurcharge(Carbon $checkinDate): array
    {
        $surcharge = 0;
        $info = null;

        // Giờ check-in tiêu chuẩn của NGÀY check-in
        $standardCheckin = $checkinDate->copy()->setTime($this->standardCheckinHour, 0, 0);
        
        // Chỉ tính phụ thu nếu checkin trước giờ tiêu chuẩn
        if ($checkinDate->lt($standardCheckin)) {
            // Fix: Đảo ngược thứ tự để được số dương
            $hoursEarly = $checkinDate->diffInMinutes($standardCheckin) / 60.0;
            
            // Tìm policy phù hợp: hour_mark <= hoursEarly, lấy cao nhất
            $policy = $this->surchargePolicies
                ->filter(fn ($p) => $p->policy_type === PolicyType::CHECKIN_EARLY->value
                                 && (float) $p->hour_mark <= $hoursEarly)
                ->sortByDesc('hour_mark')
                ->first();

            if ($policy) {
                $surcharge = (float) $policy->price;
                $info = [
                    'actual_checkin'    => $checkinDate->toDateTimeString(),
                    'standard_checkin'  => $standardCheckin->toDateTimeString(),
                    'hours_early'       => round($hoursEarly, 2),
                    'policy_hour_mark'  => (float) $policy->hour_mark,
                    'surcharge'         => $surcharge,
                ];
            }
        }

        return [
            'amount' => $surcharge,
            'info'   => $info,
        ];
    }

    private function calculateLateCheckoutSurcharge(Carbon $checkoutDate): array
    {
        $surcharge = 0;
        $info = null;

        // Mốc checkout tiêu chuẩn: ngày checkout_date lúc 12:00
        $standardCheckout = $checkoutDate->copy()->setTime($this->standardCheckoutHour, 0, 0);
        
        // Chỉ tính phụ thu nếu hiện tại sau giờ tiêu chuẩn.
        if ($this->now->gt($standardCheckout)) {
            $lateMinutes = max(0, $standardCheckout->diffInMinutes($this->now, false));
            $roundingThresholdMinutes = max(0, min(59, $this->roundingTime));
            $lateWholeHours = intdiv($lateMinutes, 60);
            $lateRemainderMinutes = $lateMinutes % 60;
            $hoursLate = (float) ($lateWholeHours + (
                $lateRemainderMinutes > 0 && $lateRemainderMinutes >= $roundingThresholdMinutes ? 1 : 0
            ));

            // Tìm policy phù hợp: hour_mark <= hoursLate (đã làm tròn), lấy cao nhất.
            $policy = $this->surchargePolicies
                ->filter(fn ($p) => $p->policy_type === PolicyType::CHECKOUT_LATE->value
                                 && (float) $p->hour_mark <= $hoursLate)
                ->sortByDesc('hour_mark')
                ->first();

            if ($policy) {
                $surcharge = (float) $policy->price;
                $info = [
                    'actual_checkout'    => $this->now->toDateTimeString(),
                    'standard_checkout'  => $standardCheckout->toDateTimeString(),
                    'hours_late'         => round($hoursLate, 2),
                    'policy_hour_mark'   => (float) $policy->hour_mark,
                    'surcharge'          => $surcharge,
                ];
            }
        }

        return [
            'amount' => $surcharge,
            'info'   => $info,
        ];
    }
}