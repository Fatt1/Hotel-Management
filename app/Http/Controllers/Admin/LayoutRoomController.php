<?php

namespace App\Http\Controllers\Admin;

use App\Actions\LayoutRooms\GetAllLayoutRoomsAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LayoutRoomController extends Controller
{
    public function index(Request $request, GetAllLayoutRoomsAction $action)
    {
        $filterDate = $request->input('date', today()->format('Y-m-d'));
        $filterStatus = $request->input('status');
        $groupBy = $request->input('group_by', 'type');

        if (!in_array($groupBy, ['type', 'floor', 'room'])) {
            $groupBy = 'type';
        }

        $viewModel = $action->execute($filterDate, $filterStatus, $groupBy);

        return view("admin.layout-room.index", compact('viewModel'));
    }
}

