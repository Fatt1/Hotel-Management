<?php

namespace App\Actions\Bookings;

use App\Enums\PolicyType;
use App\Enums\RoomStatus;
use App\Actions\Rooms\UpdateRoomStatusAction;
use App\Models\Booking;
use App\Models\BookingDetail;
use App\Models\SurchargePolicy;
use App\Models\SystemSetting;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class CheckoutBookingAction
{
    private int $standardCheckinHour;
    private int $standardCheckinMinute;
    private int $standardCheckoutHour;
    private int $standardCheckoutMinute;
    private int $roundingTime;

    public function __construct(
        private RecalculateBookingAmountsAction $recalculateBookingAmountsAction,
        private UpdateRoomStatusAction $updateRoomStatusAction,
    )
    {
        // Load cấu hình ở runtime vì PHP const không hỗ trợ query DB.
        [$this->standardCheckinHour, $this->standardCheckinMinute] = $this->parseSettingTime(
            (string) (SystemSetting::where('setting_key', 'checkin_time')->value('setting_value') ?? '14:00')
        );
        [$this->standardCheckoutHour, $this->standardCheckoutMinute] = $this->parseSettingTime(
            (string) (SystemSetting::where('setting_key', 'checkout_time')->value('setting_value') ?? '12:00')
        );
        $this->roundingTime = (int) (SystemSetting::where('setting_key', 'rounding_time')->value('setting_value') ?? 15);
    }

    public function execute(array $bookingDetailIds, $bookingId): void
    {
        DB::transaction(function () use ($bookingDetailIds, $bookingId) {
        $booking = Booking::findOrFail($bookingId);
        $bookingDetails = BookingDetail::with(['room', 'serviceUsages'])
            ->where('booking_id', $bookingId)
            ->whereIn('id', $bookingDetailIds)
            ->get();
        
        $now = Carbon::now();
        $surchargePolicies = SurchargePolicy::all();
        
        // Cập nhật checkout_status và surcharge_amount cho từng phòng
        foreach($bookingDetails as $detail) {
            /** @var BookingDetail $detail */
            $roomAmount = $this->calculateRoomAmount($detail, $now, $surchargePolicies);

            $totalSurcharge = $this->calculateSurchargeAmount($detail, $now, $surchargePolicies);
            
            // ─── 7. Cập nhật vào database ──────────────────────────────────
            $detail->update([
                'checkout_status' => true,
                'checkout_date' => $now,
                'room_amount' => $roomAmount,
                'surcharge_amount' => $totalSurcharge,
            ]);

            // Checkout thành công -> chuyển phòng sang trạng thái cần dọn (dirty)
            $this->updateRoomStatusAction->execute((int) $detail->room_id, RoomStatus::CLEANING->value);
        }
        
        // ─── 8. Kiểm tra xem tất cả phòng đã checkout chưa ────────────────
        $hasUncheckedRooms = BookingDetail::where('checkout_status', false)
            ->where('booking_id', $bookingId)
            ->exists();
        
        if (!$hasUncheckedRooms) {
            // Tất cả phòng đã checkout → Cập nhật trạng thái và tính lại amounts
            $booking->update([
                'status' => 'Hoàn tất',
            ]);
        }
            // Tính lại tổng tiền phòng và dịch vụ sau khi checkout (phụ thu có thể ảnh hưởng đến tổng tiền phòng)
            $this->recalculateBookingAmountsAction->execute($bookingId); 
        });
    }

    private function calculateSurchargeAmount($detail, Carbon $now, Collection $surchargePolicies): float
    {
        $checkinDate = Carbon::parse($detail->checkin_date);
        $actualCheckoutDate = $now->copy();
        $scheduledCheckoutDate = Carbon::parse($detail->checkout_date);
        
        $standardCheckin = $checkinDate->copy()->setTime($this->standardCheckinHour, $this->standardCheckinMinute, 0);
        $standardCheckout = $scheduledCheckoutDate->copy()->setTime($this->standardCheckoutHour, $this->standardCheckoutMinute, 0);

        // Chỉ tính check-in sớm khi giờ vào thực tế trước giờ check-in chuẩn.
        $checkinEarlyMinutes = max(0, $checkinDate->diffInMinutes($standardCheckin, false));
        $checkinEarlyHours = $checkinEarlyMinutes / 60.0;

        // Tính checkout muộn dựa trên thời điểm checkout thực tế (now).
        // ROUNDING_TIME là ngưỡng phút để làm tròn lên 1 giờ kế tiếp.
        // Ví dụ ROUNDING_TIME=15: trễ 15p => 1h, trễ 2h15 => 3h.
        $checkoutLateMinutes = max(0, $standardCheckout->diffInMinutes($actualCheckoutDate, false));
        $roundingThresholdMinutes = max(0, min(59, $this->roundingTime));
        $lateWholeHours = intdiv($checkoutLateMinutes, 60);
        $lateRemainderMinutes = $checkoutLateMinutes % 60;
        $checkoutLateHours = (float) ($lateWholeHours + (
            $lateRemainderMinutes > 0 && $lateRemainderMinutes >= $roundingThresholdMinutes ? 1 : 0
        ));

        $earlyCheckinSurcharge = 0;
        $lateCheckoutSurcharge = 0;
        
        
        // Tính phụ thu check-in sớm
        $checkinEarlyPolicy = $surchargePolicies
            ->filter(fn ($p) => $p->policy_type === PolicyType::CHECKIN_EARLY->value && (float) $p->hour_mark <= $checkinEarlyHours)
            ->sortByDesc('hour_mark')
            ->first();

        // Tính phụ thu check-out muộn
        $checkoutLatePolicy = $surchargePolicies
            ->filter(fn ($p) => $p->policy_type === PolicyType::CHECKOUT_LATE->value && (float) $p->hour_mark <= $checkoutLateHours)
            ->sortByDesc('hour_mark')
            ->first();
        
        if($checkinEarlyPolicy) {
            $earlyCheckinSurcharge = (float) $checkinEarlyPolicy->price;
        }
        if($checkoutLatePolicy) {
            $lateCheckoutSurcharge = (float) $checkoutLatePolicy->price;
        }
        return $earlyCheckinSurcharge + $lateCheckoutSurcharge;


    }  
     

    private function calculateRoomAmount($detail, Carbon $now, Collection $surchargePolicies): float
    {
        $checkinDate = Carbon::parse($detail->checkin_date);
        $checkoutDate = Carbon::parse($detail->checkout_date);
        
        // Tính tổng số giờ đã ở (từ checkin đến hiện tại)
        $totalHoursStayed = $checkinDate->diffInMinutes($now) / 60.0;
        $totalDaysStayed = (int) $checkinDate->diffInDays($now);
        
        // Lấy ngưỡng chuyển từ giờ sang ngày (hour_mark lớn nhất của CHECKOUT_LATE)
        $switchToDailyThreshold = $surchargePolicies
            ->filter(fn ($p) => $p->policy_type === PolicyType::CHECKOUT_LATE->value)
            ->sortByDesc('hour_mark')
            ->first();
        
        // Quyết định tính theo giờ hay ngày
        if ($switchToDailyThreshold && $totalHoursStayed <= (float) $switchToDailyThreshold->hour_mark) {
            // Tính theo giờ
            return $this->calculateHourlySurcharge($detail, $totalHoursStayed);
        } else {
            // Tính theo ngày
            return $this->calculateDailySurcharge($detail, $totalDaysStayed);
        }
    }



    private function calculateHourlySurcharge(BookingDetail $detail, float $totalHoursStayed):float {
        $chargedHours = (int) floor($totalHoursStayed);
            $fraction = $totalHoursStayed - $chargedHours;
            if( $fraction > $this->roundingTime / 60) {
                $chargedHours++;
            }
            // Tối thiểu tính 1 giờ nếu khách ở chưa đến 1 giờ
            if($chargedHours < 1) {
                $chargedHours = 1; // Tối thiểu tính 1 giờ
            }
        return $detail->hourly_price * $chargedHours;
    }
            
    private function calculateDailySurcharge(BookingDetail $detail, int $totalDaysStayed):float {
        return $detail->daily_price * $totalDaysStayed;
    }

    private function parseSettingTime(string $time): array
    {
        if (preg_match('/^(\d{1,2}):(\d{1,2})(?::\d{1,2})?$/', trim($time), $matches)) {
            $hour = max(0, min(23, (int) $matches[1]));
            $minute = max(0, min(59, (int) $matches[2]));

            return [$hour, $minute];
        }

        return [0, 0];
    }
}
