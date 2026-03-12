<?php

namespace App\Actions\Bookings;

use App\Enums\PolicyType;
use App\Models\Booking;
use App\Models\BookingDetail;
use App\Models\SurchargePolicy;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CheckoutBookingAction
{
    public function __construct(private RecalculateBookingAmountsAction $recalculateBookingAmountsAction)
    {
    }
    
    // Giờ check-in / checkout tiêu chuẩn
    private const STANDARD_CHECKIN_HOUR  = 14; // 14:00
    private const STANDARD_CHECKOUT_HOUR = 12; // 12:00

    public function execute(array $bookingDetailIds, $bookingId): void
    {
        DB::transaction(function () use ($bookingDetailIds, $bookingId) {
        $booking = Booking::with('bookingDetails')->findOrFail($bookingId);
        $bookingDetails = BookingDetail::with(['room', 'serviceUsages'])->whereIn('id', $bookingDetailIds)->get();
        
        $now = Carbon::now();
        $surchargePolicies = SurchargePolicy::all();
        
        // Lấy ngưỡng chuyển từ giờ sang ngày (hour_mark lớn nhất của CHECKOUT_LATE)
        $switchToDailyThreshold = $surchargePolicies
            ->filter(fn ($p) => $p->policy_type === PolicyType::CHECKOUT_LATE->value)
            ->sortByDesc('hour_mark')
            ->first();
        
        // Cập nhật checkout_status và surcharge_amount cho từng phòng
        foreach($bookingDetails as $detail) {
          
            $checkinDate = Carbon::parse($detail->checkin_date);
            $checkoutDate = Carbon::parse($detail->checkout_date);
            
            // ─── 1. Tính tổng số giờ đã ở (từ checkin đến hiện tại) ───────
            $totalHoursStayed = $checkinDate->diffInMinutes($now) / 60.0;
            
            // ─── 2. Quyết định tính theo giờ hay ngày ──────────────────────
            // Nếu số giờ <= ngưỡng → tính giờ, nếu > ngưỡng → tính ngày
            $chargeByHour = false;
            if ($switchToDailyThreshold && $totalHoursStayed <= (float) $switchToDailyThreshold->hour_mark) {
                $chargeByHour = true;
            }
            
        
            
            // ─── 4. Kiểm tra checkin sớm ───────────────────────────────────
            $earlyCheckinSurcharge = 0;
            $standardCheckin = $checkinDate->copy()->setTime(self::STANDARD_CHECKIN_HOUR, 0, 0);
            
            if ($checkinDate->lt($standardCheckin)) {
                // Fix: Đảo ngược thứ tự để được số dương
                $hoursEarly = $checkinDate->diffInMinutes($standardCheckin) / 60.0;
                
                // Tìm policy phù hợp (hour_mark <= hoursEarly), lấy cao nhất
                $policy = $surchargePolicies
                    ->filter(fn ($p) => $p->policy_type === PolicyType::CHECKIN_EARLY->value
                                     && (float) $p->hour_mark <= $hoursEarly)
                    ->sortByDesc('hour_mark')
                    ->first();
                
                if ($policy) {
                    $earlyCheckinSurcharge = (float) $policy->price;
                }
            }
            
            // ─── 5. Kiểm tra checkout muộn ─────────────────────────────────
            $lateCheckoutSurcharge = 0;
            $standardCheckout = $checkoutDate->copy()->setTime(self::STANDARD_CHECKOUT_HOUR, 0, 0);
            
            if ($now->gt($standardCheckout)) {
               
                $hoursLate = $standardCheckout->diffInMinutes($now) / 60.0;
                
                // Tìm policy phù hợp (hour_mark <= hoursLate), lấy cao nhất
                $policy = $surchargePolicies
                    ->filter(fn ($p) => $p->policy_type === PolicyType::CHECKOUT_LATE->value
                                     && (float) $p->hour_mark <= $hoursLate)
                    ->sortByDesc('hour_mark')
                    ->first();
                
                if ($policy) {
                    $lateCheckoutSurcharge = (float) $policy->price;
                }
            }
            
            // ─── 6. Tổng phụ thu ────────────────────────────────────────────
            $totalSurcharge = $earlyCheckinSurcharge + $lateCheckoutSurcharge;
            
            // ─── 7. Cập nhật vào database ──────────────────────────────────
            $detail->update([
                'checkout_status' => true,
                'checkout_date' => $now,
                'surcharge_amount' => $totalSurcharge,
            ]);
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
        
            // Recalculate all amounts (including surcharges that were just set)
            $this->recalculateBookingAmountsAction->execute($bookingId);
        });
    }
}
