<?php

namespace App\Http\Controllers\Admin;

use App\Actions\LayoutRooms\GetAllLayoutRoomsAction;
use App\Http\Controllers\Controller;
use App\ViewModels\LayoutRoomIndexViewModel;
use Illuminate\Http\Request;

class LayoutRoomController extends Controller
{
    public function index(Request $request, GetAllLayoutRoomsAction $action) {
        $filterDate = $request->input('date', today()->format('Y-m-d'));
        $filterStatus = $request->input('status');
        $groupBy = $request->input('group_by', 'type');
        
        // Validate groupBy
        if (!in_array($groupBy, ['type', 'floor', 'room'])) {
            $groupBy = 'type';
        }
        
        $roomsByType = $action->execute($filterDate, $filterStatus, $groupBy);
        $statusCounts = $action->getStatusCounts($filterDate);
        $floors = $action->getAllFloors();
        
        $viewModel = new LayoutRoomIndexViewModel(
            roomsByType: $roomsByType,
            statusCounts: $statusCounts,
            filterDate: $filterDate,
            filterStatus: $filterStatus,
            groupBy: $groupBy,
            floors: $floors
        );
        
        return view("admin.layout-room.index", compact('viewModel'));
    }
}

