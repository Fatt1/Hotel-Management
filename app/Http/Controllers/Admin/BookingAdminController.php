<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Bookings\CalculateCheckoutPaymentAction;
use App\Actions\Bookings\CancelBookingAction;
use App\Actions\Bookings\CheckinBookingAction;
use App\Actions\Bookings\CheckoutBookingAction;
use App\Actions\Bookings\CreateBookingAction;
use App\Actions\Bookings\GetAllBookingsAction;
use App\Actions\Bookings\GetBookingByIdAction;
use App\Actions\Bookings\UpdateBookingAction;
use App\Data\BookingData;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class BookingAdminController extends Controller
{
    public function index(GetAllBookingsAction $getAllBookingsAction, Request $request)
    {
        $page_number = $request->input('page', 1);
        $page_size = $request->input('page_size', 10);
        $search = $request->input('search');
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');
        $status = $request->input('status');
        $bookings = $getAllBookingsAction->handle($page_number, $page_size, $search, $from_date, $to_date, $status);
        
        return view("admin.bookings.index", compact('bookings'));
    }
    public function create()
    {
        $countries = (new \App\ViewModels\CustomerViewModel())->countries();
        return view("admin.bookings.create", compact('countries'));
    }

    public function store(BookingData $bookingData, CreateBookingAction $createBookingAction) {
        try{
            $booking = $createBookingAction->execute($bookingData);
            return response()->json([
                'message' => 'Đặt phòng thành công',
                'booking_id' => $booking->id
            ], 201);
        }
        catch(\Exception $e){
           return response()->json([
                'message' => 'Đặt phòng thất bại: ' . $e->getMessage()
            ], 500);
        }
       
       
    }

    public function show($id, GetBookingByIdAction $getBookingByIdAction)
    {
        $booking = $getBookingByIdAction->execute($id);
        return view("admin.bookings.show", compact('booking'));
    }

    public function checkoutConfirm($id, GetBookingByIdAction $getBookingByIdAction)
    {
        $booking = $getBookingByIdAction->execute($id);
        return view("admin.bookings.checkout", compact('booking'));
    }

    public function checkinConfirm($id, GetBookingByIdAction $getBookingByIdAction)
    {
        $booking = $getBookingByIdAction->execute($id);
        return view("admin.bookings.checkin", compact('booking'));
    }

    public function checkin($id, CheckinBookingAction $checkinBookingAction)
    {
        try {
            $checkinBookingAction->execute($id);
            return redirect()->route('admin.layout-rooms.index')->with('success', 'Check-in thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Check-in thất bại: ' . $e->getMessage());
        }
    }

    public function checkout($id, CheckoutBookingAction $checkoutBookingAction, Request $request)
    {
        try {
            $paymentAmount = (float) ($request->input('payment_amount', 0) ?: 0);
            $paymentMethod = $request->input('payment_method', 'cash');
            $checkoutBookingAction->execute($id, $paymentAmount, $paymentMethod);
            return redirect()->route('admin.layout-rooms.index')->with('success', 'Check-out thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Check-out thất bại: ' . $e->getMessage());
        }
    }

    public function cancel($id, CancelBookingAction $cancelBookingAction)
    {
        try {
            $cancelBookingAction->execute($id);
            return redirect()->route('admin.layout-rooms.index')->with('success', 'Đã hủy booking thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Hủy booking thất bại: ' . $e->getMessage());
        }
    }

    public function edit($id, GetBookingByIdAction $getBookingByIdAction)
    {
        $booking = $getBookingByIdAction->execute($id);
        return view('admin.bookings.edit', compact('booking'));
    }

    public function update($id, BookingData $bookingData, UpdateBookingAction $updateBookingAction)
    {
        try {
            $booking = $updateBookingAction->execute($id, $bookingData);
            return response()->json([
                'message' => 'Cập nhật booking thành công',
                'booking_id' => $booking->id
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Cập nhật booking thất bại: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tính tiền checkout: trả về chi tiết từng phòng, phụ thu và tổng cần thanh toán.
     * POST /admin/bookings/calculate-payment
     * Body: { booking_id: int, room_ids: int[] }
     */
    public function calculatePayment(Request $request, CalculateCheckoutPaymentAction $action)
    {
        $request->validate([
            'booking_id' => 'required|integer|exists:bookings,id',
            'room_ids'   => 'required|array|min:1',
            'room_ids.*' => 'integer|exists:rooms,id',
        ]);

        try {
            $result = $action->execute([
                'booking_id' => $request->integer('booking_id'),
                'room_ids'   => $request->input('room_ids'),
            ]);

            return response()->json($result, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Tính tiền thất bại: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ghi nhận thanh toán / hoàn tiền (AJAX, không checkout).
     * POST /admin/bookings/{id}/record-payment
     */
    public function recordPayment(Request $request, int $id)
    {
        $request->validate([
            'amount'         => 'required|numeric|min:0.01',
            'payment_method' => 'required|string|in:cash,bank_transfer,card',
            'payment_type'   => 'required|string|in:payment,refund',
        ]);

        try {
            $amount = (float) $request->input('amount');
            if ($request->input('payment_type') === 'refund') {
                $amount = -$amount;
            }

            Payment::create([
                'booking_id'     => $id,
                'amount'         => $amount,
                'payment_method' => $request->input('payment_method'),
                'staff_id'       => auth('staff')->id(),
            ]);

            return response()->json(['success' => true], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ghi nhận thất bại: ' . $e->getMessage()
            ], 500);
        }
    }
}
