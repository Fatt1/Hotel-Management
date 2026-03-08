<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Bookings\CancelBookingAction;
use App\Actions\Bookings\CheckinBookingAction;
use App\Actions\Bookings\CreateBookingAction;
use App\Actions\Bookings\GetAllBookingsAction;
use App\Actions\Bookings\GetBookingByIdAction;
use App\Actions\Bookings\UpdateBookingAction;
use App\Data\BookingData;
use App\Http\Controllers\Controller;
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
        
        // Prepare booking data for JavaScript
        $bookingData = [
            'id' => $booking->id,
            'customer' => [
                'first_name' => $booking->customer->first_name,
                'last_name' => $booking->customer->last_name,
                'email' => $booking->customer->email,
                'phone_number' => $booking->customer->phone_number,
                'country' => $booking->customer->country,
            ],
            'booking_date' => $booking->booking_date->format('Y-m-d H:i:s'),
            'status' => $booking->status,
            'booking_details' => $booking->bookingDetails->map(function($detail) {
                return [
                    'room' => [
                        'id' => $detail->room->id,
                        'name' => $detail->room->name,
                        'status' => $detail->room->status,
                        'room_type' => [
                            'id' => $detail->room->roomType->id,
                            'name' => $detail->room->roomType->name,
                            'code' => $detail->room->roomType->code,
                            'daily_price' => $detail->daily_price,
                            'hourly_price' => $detail->hourly_price,
                        ],
                        'floor' => [
                            'id' => $detail->room->floor->id,
                            'name' => $detail->room->floor->name,
                        ],
                    ],
                    'checkin_date' => $detail->checkin_date->format('Y-m-d H:i:s'),
                    'checkout_date' => $detail->checkout_date->format('Y-m-d H:i:s'),
                    'services' => $detail->serviceUsages->map(function($usage) {
                        return [
                            'id' => $usage->service->id,
                            'name' => $usage->service->name,
                            'unit_price' => $usage->unit_price,
                            'quantity' => $usage->quantity,
                            'unit' => $usage->service->unit,
                            'group' => $usage->service->serviceGroup->name ?? '',
                        ];
                    }),
                ];
            })->toArray(),
        ];
        
        return view('admin.bookings.edit', compact('booking', 'bookingData'));
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
}
