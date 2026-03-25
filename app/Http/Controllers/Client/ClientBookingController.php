<?php

namespace App\Http\Controllers\Client;

use App\Actions\Bookings\CancelBookingAction;
use App\Actions\Bookings\GetClientBookingsAction;
use App\Actions\Bookings\GetClientOwnedBookingAction;
use App\Actions\Bookings\UpdateClientBookingDatesAction;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientBookingController extends Controller
{
    public function index(Request $request, GetClientBookingsAction $getClientBookingsAction)
    {
        /** @var Customer $customer */
        $customer = Auth::guard('customer')->user();

        $bookingData = $getClientBookingsAction->execute($customer->id, 5);

        return view('client.profile.bookings', [
            'customer' => $customer,
            'upcomingBookings' => $bookingData['upcomingBookings'],
            'pastBookings' => $bookingData['pastBookings'],
        ]);
    }

    public function details(int $id, GetClientOwnedBookingAction $getClientOwnedBookingAction)
    {
        /** @var Customer $customer */
        $customer = Auth::guard('customer')->user();

        $booking = $getClientOwnedBookingAction->execute($customer->id, $id, [
            'bookingDetails.room.roomType',
            'bookingDetails.serviceUsages.service',
        ]);

        $html = view('client.profile.partials.history-booking-details', [
            'booking' => $booking,
        ])->render();

        return response()->json([
            'html' => $html,
        ]);
    }

    public function updateDates(
        Request $request,
        int $id,
        UpdateClientBookingDatesAction $updateClientBookingDatesAction
    ): RedirectResponse {
        $validated = $request->validate([
            'checkin_date' => ['required', 'date_format:Y-m-d'],
            'checkout_date' => ['required', 'date_format:Y-m-d', 'after:checkin_date'],
        ], [
            'checkin_date.required' => 'Vui lòng chọn ngày nhận phòng.',
            'checkout_date.required' => 'Vui lòng chọn ngày trả phòng.',
            'checkout_date.after' => 'Ngày trả phòng phải sau ngày nhận phòng.',
        ]);

        /** @var Customer $customer */
        $customer = Auth::guard('customer')->user();

        $newCheckin = Carbon::createFromFormat('Y-m-d', $validated['checkin_date'])->startOfDay();
        $newCheckout = Carbon::createFromFormat('Y-m-d', $validated['checkout_date'])->startOfDay();

        if ($newCheckin->isPast()) {
            return back()->with('error', 'Ngày nhận phòng mới phải từ hôm nay trở đi.');
        }

        try {
            $updateClientBookingDatesAction->execute($customer->id, $id, $newCheckin, $newCheckout);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('client.bookings.index')
            ->with('success', 'Đã cập nhật thời gian check-in/check-out thành công.');
    }

    public function cancel(
        int $id,
        CancelBookingAction $cancelBookingAction,
        GetClientOwnedBookingAction $getClientOwnedBookingAction
    ): RedirectResponse
    {
        /** @var Customer $customer */
        $customer = Auth::guard('customer')->user();

        $booking = $getClientOwnedBookingAction->execute($customer->id, $id);

        try {
            $cancelBookingAction->execute($booking->id);

            return redirect()
                ->route('client.bookings.index')
                ->with('success', 'Đã hủy booking thành công.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
