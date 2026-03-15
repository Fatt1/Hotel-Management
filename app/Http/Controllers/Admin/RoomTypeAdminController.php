<?php

namespace App\Http\Controllers\Admin;

use App\Actions\RoomTypes\CreateRoomTypeAction;
use App\Actions\RoomTypes\DeleteRoomTypeAction;
use App\Actions\RoomTypes\GetRoomTypeListAction;
use App\Actions\RoomTypes\UpdateRoomTypeAction;
use App\Data\RoomTypeData;
use App\Http\Controllers\Controller;
use App\Models\RoomType;
use App\ViewModels\RoomTypeViewModel;
use Illuminate\Http\Request;

class RoomTypeAdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, GetRoomTypeListAction $action)
    {
        $filters = [
            'search' => $request->input('search'),
            'status' => $request->input('status'),
        ];
        $roomTypes = $action->executeWithRoomCount(filters: $filters);
        return view('admin.room-type.index', compact('roomTypes'));
    }
    public function getAll(GetRoomTypeListAction $action) {
        $roomTypes = $action->execute();
        return response()->json($roomTypes, 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $viewModel = new RoomTypeViewModel();
        return view('admin.room-type.create', compact('viewModel'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $httpRequest, RoomTypeData $request, CreateRoomTypeAction $action)
    {
        try {
            $images = $httpRequest->file('images') ?? [];
            $amenityIds = $httpRequest->input('amenities', []);
            $equipmentData = [
                'ids' => $httpRequest->input('equipments', []),
                'quantities' => $httpRequest->input('equipment_quantities', [])
            ];
            
            $action->execute($request, $images, $amenityIds, $equipmentData);

            return redirect()
                ->route('admin.room-types.index')
                ->with('success', 'Loại phòng đã được tạo thành công');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(GetRoomTypeListAction $action, string $id)
    {
        $roomType = $action->execute()->firstWhere('id', (int) $id);
        $viewModel = new RoomTypeViewModel($roomType);
        return view('admin.room-type.show', compact('viewModel', 'roomType'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(GetRoomTypeListAction $action, string $id)
    {
        $roomType = $action->execute()->firstWhere('id', (int) $id);
        $viewModel = new RoomTypeViewModel($roomType);
        return view('admin.room-type.edit', compact('viewModel', 'roomType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $httpRequest, RoomTypeData $request, string $id, UpdateRoomTypeAction $action)
    {
        try {
            $images = $httpRequest->file('images') ?? [];
            $amenityIds = $httpRequest->input('amenities', []);
            $equipmentData = [
                'ids' => $httpRequest->input('equipments', []),
                'quantities' => $httpRequest->input('equipment_quantities', [])
            ];
            $deleteImageIds = $httpRequest->input('delete_images', []);
            
            $action->execute((int) $id, $request, $images, $amenityIds, $equipmentData, $deleteImageIds);

            return redirect()
                ->route('admin.room-types.index')
                ->with('success', 'Loại phòng đã được cập nhật thành công');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id, DeleteRoomTypeAction $action)
    {
        try {
            $action->execute((int) $id);

            return redirect()
                ->route('admin.room-types.index')
                ->with('success', 'Loại phòng đã được xóa thành công');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.room-types.index')
                ->with('error', $e->getMessage());
        }
    }
}
