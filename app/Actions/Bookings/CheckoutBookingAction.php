<?php

namespace App\Actions\Bookings;

use App\Enums\PolicyType;
use App\Models\Booking;
use App\Models\BookingDetail;
use App\Models\SurchargePolicy;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
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
        
        // Cập nhật checkout_status và surcharge_amount cho từng phòng
        foreach($bookingDetails as $detail) {
            $roomAmount = $this->calculateRoomAmount($detail, $now, $surchargePolicies);

            $totalSurcharge = $this->calculateSurchargeAmount($detail, $now, $surchargePolicies);
            
            // ─── 7. Cập nhật vào database ──────────────────────────────────
            $detail->update([
                'checkout_status' => true,
                'checkout_date' => $now,
                'room_amount' => $roomAmount,
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
            // Tính lại tổng tiền phòng và dịch vụ sau khi checkout (phụ thu có thể ảnh hưởng đến tổng tiền phòng)
            $this->recalculateBookingAmountsAction->execute($bookingId); 
        });
    }

    private function calculateSurchargeAmount($detail, Carbon $now, Collection $surchargePolicies): float
    {
        $checkinDate = Carbon::parse($detail->checkin_date);
        $checkoutDate = Carbon::parse($detail->checkout_date);
        
        $standardCheckin = $checkinDate->copy()->setTime(self::STANDARD_CHECKIN_HOUR, 0, 0);
        $standardCheckout = $checkoutDate->copy()->setTime(self::STANDARD_CHECKOUT_HOUR, 0, 0);

        $checkinEarlyHours =  $checkinDate->diffInMinutes($standardCheckin) / 60.0;
        $checkoutLateHours = $checkoutDate->diffInMinutes($standardCheckout) / 60.0;

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
            if( $fraction > 0.25) {
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
}
