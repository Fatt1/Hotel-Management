<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Bookings\CreateBookingAction;
use App\Actions\Bookings\GetAllBookingsAction;
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
}
