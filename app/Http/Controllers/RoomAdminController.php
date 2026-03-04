<?php

namespace App\Http\Controllers;

use App\Actions\Rooms\GetAvailableRoomsAction;
use DateTime;
use Illuminate\Http\Request;

class RoomAdminController extends Controller
{
    public function getAvailableRooms(Request $request, GetAvailableRoomsAction $action)
    {
        $checkinDate = $request->input('checkin_date', new DateTime());
        $checkoutDate = $request->input('checkout_date', (new DateTime())->modify('+1 day'));
        $roomTypeId = $request->input('room_type_id');
        $floorId = $request->input('floor_id');

        // Gọi API để lấy danh sách phòng trống dựa trên ngày check-in và check-out
        $availableRooms = $action->handle($checkinDate, $checkoutDate, $roomTypeId, $floorId);

        // Trả về danh sách phòng trống dưới dạng JSON
        return response()->json([
            'available_rooms' => $availableRooms
        ]);
    }
}
