<?php

namespace App\Actions\Bookings;

use App\Models\Booking;
use App\Models\BookingDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateClientBookingDatesAction
{
    public function __construct(private RecalculateBookingAmountsAction $recalculateBookingAmountsAction)
    {
    }

    public function execute(int $customerId, int $bookingId, Carbon $newCheckin, Carbon $newCheckout): void
    {
        $booking = Booking::query()
            ->where('customer_id', $customerId)
            ->where('id', $bookingId)
            ->firstOrFail();

        if ($booking->status !== 'Đã đặt') {
            throw new \Exception('Booking này không thể đổi ngày ở trạng thái hiện tại.');
        }

        $booking->loadMissing('bookingDetails');

        if ($booking->bookingDetails->isEmpty()) {
            throw new \Exception('Booking chưa có phòng, không thể đổi ngày.');
        }

        foreach ($booking->bookingDetails as $detail) {
            $hasConflict = BookingDetail::query()
                ->where('room_id', $detail->room_id)
                ->where('booking_id', '!=', $booking->id)
                ->whereHas('booking', function ($query): void {
                    $query->whereNotIn('status', ['Hủy', 'Không đến']);
                })
                ->where(function ($query) use ($newCheckin, $newCheckout): void {
                    $query
                        ->where('checkin_date', '<', $newCheckout)
                        ->where('checkout_date', '>', $newCheckin);
                })
                ->exists();

            if ($hasConflict) {
                throw new \Exception('Có phòng không còn trống trong khoảng thời gian mới. Vui lòng chọn ngày khác.');
            }
        }

        DB::transaction(function () use ($booking, $newCheckin, $newCheckout): void {
            $chargedDays = max((int) ceil($newCheckin->diffInSeconds($newCheckout) / 86400), 1);

            foreach ($booking->bookingDetails as $detail) {
                $detail->update([
                    'checkin_date' => $newCheckin,
                    'checkout_date' => $newCheckout,
                    'room_amount' => (float) $detail->daily_price * $chargedDays,
                ]);
            }

            $bookingPayload = [];

            if (Schema::hasColumn('bookings', 'checkin_date')) {
                $bookingPayload['checkin_date'] = $newCheckin;
            }

            if (Schema::hasColumn('bookings', 'checkout_date')) {
                $bookingPayload['checkout_date'] = $newCheckout;
            }

            if (!empty($bookingPayload)) {
                $booking->update($bookingPayload);
            }

            $this->recalculateBookingAmountsAction->execute($booking->id);
        });
    }
}
