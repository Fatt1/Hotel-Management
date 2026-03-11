<?php

namespace App\Actions\Bookings;

use App\Enums\PolicyType;
use App\Models\Booking;
use App\Models\SurchargePolicy;
use Carbon\Carbon;

class CalculateCheckoutPaymentAction
{
    // Giờ check-in / checkout tiêu chuẩn
    private const STANDARD_CHECKIN_HOUR  = 14; // 14:00
    private const STANDARD_CHECKOUT_HOUR = 12; // 12:00

    public function execute(array $input): array
    {
        $booking = Booking::with([
            'bookingDetails.room.roomType',
            'bookingDetails.serviceUsages.service',
            'payments.staff',
        ])->findOrFail($input['booking_id']);

        $selectedRoomIds    = array_map('intval', $input['room_ids']);
        $now                = Carbon::now();
        $surchargePolicies  = SurchargePolicy::all();

        $roomBreakdowns       = [];
        $totalRoomAmount      = 0;
        $totalServiceAmount   = 0;
        $totalSurchargeAmount = 0;

        foreach ($booking->bookingDetails as $detail) {
            if (! in_array((int) $detail->room_id, $selectedRoomIds)) {
                continue;
            }

            $checkinDate  = Carbon::parse($detail->checkin_date);
            $checkoutDate = Carbon::parse($detail->checkout_date);

            // ── Tiền phòng: giờ hay ngày? ────────────────────────────────────
            // Tổng số giờ từ checkin đến hiện tại (thực tế khách đang ở)
            $totalHoursStayed = $checkinDate->diffInMinutes($now) / 60.0;

            // Lấy hour_mark cao nhất trong policy CHECKOUT_LATE làm ngưỡng chuyển sang tính ngày
            $switchToDailyThreshold = $surchargePolicies
                ->filter(fn ($p) => $p->policy_type === PolicyType::CHECKOUT_LATE->value)
                ->sortByDesc('hour_mark')
                ->first();

            $chargeByHour  = false;
            $hoursStayed   = null;
            $roomAmount    = 0;

            if ($switchToDailyThreshold && $totalHoursStayed <= (float) $switchToDailyThreshold->hour_mark) {
                // Số giờ ≤ ngưỡng → tính theo giá giờ
                $chargeByHour = true;
                $hoursStayed  = round($totalHoursStayed, 2);
                $roomAmount   = (float) $detail->hourly_price * $totalHoursStayed;
            } else {
                // Số giờ > ngưỡng (hoặc không có policy) → tính theo giá ngày
                $days       = max((int) $checkinDate->diffInDays($checkoutDate), 1);
                $roomAmount = (float) $detail->daily_price * $days;
            }

            $totalRoomAmount += $roomAmount;

            // ── Tiền dịch vụ ────────────────────────────────────────────────
            $serviceList   = [];
            $serviceAmount = 0;

            foreach ($detail->serviceUsages as $usage) {
                $lineTotal      = $usage->quantity * (float) $usage->unit_price;
                $serviceAmount += $lineTotal;

                $serviceList[] = [
                    'name'       => $usage->service?->name,
                    'group'      => $usage->service?->group?->name,
                    'quantity'   => $usage->quantity,
                    'unit_price' => (float) $usage->unit_price,
                    'total'      => $lineTotal,
                ];
            }
            $totalServiceAmount += $serviceAmount;

            // ── Phụ thu checkin sớm ─────────────────────────────────────────
            $earlyCheckinSurcharge = 0;
            $earlyCheckinInfo      = null;

            $standardCheckin = $checkinDate->copy()->setTime(self::STANDARD_CHECKIN_HOUR, 0, 0);
            if ($checkinDate->lt($standardCheckin)) {
                $hoursEarly = $standardCheckin->diffInMinutes($checkinDate) / 60.0;
                $policy     = $surchargePolicies
                    ->filter(fn ($p) => $p->policy_type === PolicyType::CHECKIN_EARLY->value
                                     && (float) $p->hour_mark <= $hoursEarly)
                    ->sortByDesc('hour_mark')
                    ->first();

                if ($policy) {
                    $earlyCheckinSurcharge = (float) $policy->price;
                    $earlyCheckinInfo      = [
                        'actual_checkin'    => $checkinDate->toDateTimeString(),
                        'standard_checkin'  => $standardCheckin->toDateTimeString(),
                        'hours_early'       => round($hoursEarly, 2),
                        'policy_hour_mark'  => (float) $policy->hour_mark,
                        'surcharge'         => $earlyCheckinSurcharge,
                    ];
                }
            }

            // ── Phụ thu checkout muộn ───────────────────────────────────────
            $lateCheckoutSurcharge = 0;
            $lateCheckoutInfo      = null;

            // Mốc checkout tiêu chuẩn: ngày checkout_date lúc 12:00
            $standardCheckout = $checkoutDate->copy()->setTime(self::STANDARD_CHECKOUT_HOUR, 0, 0);
            if ($now->gt($standardCheckout)) {
                $hoursLate = $standardCheckout->diffInMinutes($now) / 60.0;
                $policy    = $surchargePolicies
                    ->filter(fn ($p) => $p->policy_type === PolicyType::CHECKOUT_LATE->value
                                     && (float) $p->hour_mark <= $hoursLate)
                    ->sortByDesc('hour_mark')
                    ->first();

                if ($policy) {
                    $lateCheckoutSurcharge = (float) $policy->price;
                    $lateCheckoutInfo      = [
                        'actual_checkout'    => $now->toDateTimeString(),
                        'standard_checkout'  => $standardCheckout->toDateTimeString(),
                        'hours_late'         => round($hoursLate, 2),
                        'policy_hour_mark'   => (float) $policy->hour_mark,
                        'surcharge'          => $lateCheckoutSurcharge,
                    ];
                }
            }

            $roomSurcharge         = $earlyCheckinSurcharge + $lateCheckoutSurcharge;
            $totalSurchargeAmount += $roomSurcharge;

            $roomBreakdowns[] = [
                'room_id'            => $detail->room_id,
                'room_name'          => $detail->room?->name,
                'room_type'          => $detail->room?->roomType?->name,
                'checkin_date'       => $checkinDate->toDateTimeString(),
                'checkout_date'      => $checkoutDate->toDateTimeString(),
                'actual_checkout'    => $now->toDateTimeString(),
                'charge_by_hour'     => $chargeByHour,
                'hours_stayed'       => $hoursStayed,           // null nếu tính theo ngày
                'days'               => $chargeByHour ? null : max((int) $checkinDate->diffInDays($checkoutDate), 1),
                'hourly_price'       => (float) $detail->hourly_price,
                'daily_price'        => (float) $detail->daily_price,
                'room_amount'        => $roomAmount,
                'services'           => $serviceList,
                'service_amount'     => $serviceAmount,
                'early_checkin'      => $earlyCheckinInfo,
                'late_checkout'      => $lateCheckoutInfo,
                'surcharge_amount'   => $roomSurcharge,
                'room_total'         => $roomAmount + $serviceAmount + $roomSurcharge,
            ];
        }

        $sumTotal   = $totalRoomAmount + $totalServiceAmount + $totalSurchargeAmount;
        $alreadyPaid = (float) $booking->payments->sum('amount');
        $remaining   = $sumTotal - $alreadyPaid;

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
}