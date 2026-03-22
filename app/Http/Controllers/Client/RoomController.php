<?php

namespace App\Http\Controllers\Client;

use App\Actions\RoomTypes\GetClientRoomTypeDetailAction;
use App\Actions\RoomTypes\SearchAvailableRoomTypesAction;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    /**
     * Hiển thị danh sách loại phòng với bộ lọc tìm kiếm.
     */
    public function index(Request $request, SearchAvailableRoomTypesAction $searchAction)
    {
        // --- Parse search params ---
        $checkIn    = $request->input('check_in',  now()->format('Y-m-d'));
        $checkOut   = $request->input('check_out', now()->addDays(3)->format('Y-m-d'));
        $adults     = max(1, (int) $request->input('adults', 2));
        $children   = max(0, (int) $request->input('children', 0));
        $roomsCount = max(1, (int) $request->input('rooms_count', 1));

        // Validate & sanitise dates
        try {
            $checkInDate  = Carbon::parse($checkIn);
            $checkOutDate = Carbon::parse($checkOut);
            if ($checkInDate->gte($checkOutDate)) {
                $checkOutDate = $checkInDate->copy()->addDay();
            }
        } catch (\Throwable) {
            $checkInDate  = now();
            $checkOutDate = now()->addDays(3);
        }
        $checkIn  = $checkInDate->format('Y-m-d');
        $checkOut = $checkOutDate->format('Y-m-d');
        $nights   = max(1, (int) $checkInDate->diffInDays($checkOutDate));

        // Delegate logic to Action class
        $roomTypes = $searchAction->execute($checkIn, $checkOut, $adults);

        return view('client.rooms.index', compact(
            'roomTypes', 'checkIn', 'checkOut',
            'adults', 'children', 'roomsCount', 'nights'
        ));
    }

    /**
     * Hiển thị chi tiết một loại phòng (show page).
     */
    public function show(int $id, GetClientRoomTypeDetailAction $detailAction)
    {
        // Delegate logic to Action class
        $roomType = $detailAction->execute($id);

        return view('client.rooms.show', compact('roomType'));
    }
}
