<?php

namespace App\Http\Controllers\Client;

use App\Actions\Bookings\CancelBookingAction;
use App\Actions\Bookings\RecalculateBookingAmountsAction;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingDetail;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ClientBookingController extends Controller
{
    public function index(Request $request)
    {
        /** @var Customer $customer */
        $customer = Auth::guard('customer')->user();

        $bookings = Booking::query()
            ->where('customer_id', $customer->id)
            ->with([
                'bookingDetails.room.roomType',
            ])
            ->orderByDesc('booking_date')
            ->get();

        $now = Carbon::now();

        $upcomingBookings = $bookings->filter(function (Booking $booking) use ($now): bool {
            if (in_array($booking->status, ['Hủy', 'Không đến', 'Hoàn tất'], true)) {
                return false;
            }

            if ($booking->checkout_date instanceof Carbon) {
                return $booking->checkout_date->greaterThanOrEqualTo($now);
            }

            return true;
        })->values();

        $pastBookings = $bookings->reject(function (Booking $booking) use ($upcomingBookings): bool {
            return $upcomingBookings->contains('id', $booking->id);
        })->values();

        return view('client.profile.bookings', [
            'customer' => $customer,
            'upcomingBookings' => $upcomingBookings,
            'pastBookings' => $pastBookings,
        ]);
    }

    public function updateDates(
        Request $request,
        int $id,
        RecalculateBookingAmountsAction $recalculateBookingAmountsAction
    ): RedirectResponse {
        $validated = $request->validate([
            'checkin_date' => ['required', 'date_format:Y-m-d'],
            'checkout_date' => ['required', 'date_format:Y-m-d', 'after:checkin_date'],
        ], [
            'checkin_date.required' => 'Vui lòng chọn ngày nhận phòng.',
            'checkout_date.required' => 'Vui lòng chọn ngày trả phòng.',
            'checkout_date.after' => 'Ngày trả phòng phải sau ngày nhận phòng.',
        ]);

        $booking = $this->getOwnedBooking($id);

        if ($booking->status !== 'Đã đặt') {
            return back()->with('error', 'Booking này không thể đổi ngày ở trạng thái hiện tại.');
        }

        $newCheckin = Carbon::createFromFormat('Y-m-d', $validated['checkin_date'])->startOfDay();
        $newCheckout = Carbon::createFromFormat('Y-m-d', $validated['checkout_date'])->startOfDay();

        if ($newCheckin->isPast()) {
            return back()->with('error', 'Ngày nhận phòng mới phải từ hôm nay trở đi.');
        }

        $booking->loadMissing('bookingDetails');

        if ($booking->bookingDetails->isEmpty()) {
            return back()->with('error', 'Booking chưa có phòng, không thể đổi ngày.');
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
                return back()->with('error', 'Có phòng không còn trống trong khoảng thời gian mới. Vui lòng chọn ngày khác.');
            }
        }

        DB::transaction(function () use ($booking, $newCheckin, $newCheckout, $recalculateBookingAmountsAction): void {
            $chargedDays = max((int) ceil($newCheckin->diffInSeconds($newCheckout) / 86400), 1);

            foreach ($booking->bookingDetails as $detail) {
                $detail->update([
                    'checkin_date' => $newCheckin,
                    'checkout_date' => $newCheckout,
                    'room_amount' => (float) $detail->daily_price * $chargedDays,
                ]);
            }

            $booking->update([
                'checkin_date' => $newCheckin,
                'checkout_date' => $newCheckout,
            ]);

            $recalculateBookingAmountsAction->execute($booking->id);
        });

        return redirect()
            ->route('client.bookings.index')
            ->with('success', 'Đã cập nhật thời gian check-in/check-out thành công.');
    }

    public function cancel(int $id, CancelBookingAction $cancelBookingAction): RedirectResponse
    {
        $booking = $this->getOwnedBooking($id);

        try {
            $cancelBookingAction->execute($booking->id);

            return redirect()
                ->route('client.bookings.index')
                ->with('success', 'Đã hủy booking thành công.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    private function getOwnedBooking(int $bookingId): Booking
    {
        /** @var Customer $customer */
        $customer = Auth::guard('customer')->user();

        return Booking::query()
            ->where('customer_id', $customer->id)
            ->where('id', $bookingId)
            ->firstOrFail();
    }
}
