<?php

namespace App\Http\Controllers\Admin;

use App\Actions\RoomTypes\CreateRoomTypeAction;
use App\Actions\RoomTypes\DeleteRoomTypeAction;
use App\Actions\RoomTypes\GetRoomTypeListAction;
use App\Actions\RoomTypes\UpdateRoomTypeAction;
use App\Data\RoomTypeData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RoomTypeRequest;
use App\Models\RoomType;
use App\ViewModels\RoomTypeViewModel;
use Illuminate\Http\Request;

class RoomTypeAdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(GetRoomTypeListAction $action)
    {
        $roomTypes = $action->executeWithRoomCount();
        return view('admin.room-type.index', compact('roomTypes'));
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
    public function store(RoomTypeRequest $request, CreateRoomTypeAction $action)
    {
        try {
            $data = RoomTypeData::from($request->validated());
            $action->execute($data);

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
    public function show(string $id)
    {
        $roomType = RoomType::findOrFail($id);
        $viewModel = new RoomTypeViewModel($roomType);
        return view('admin.room-type.show', compact('viewModel', 'roomType'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $roomType = RoomType::findOrFail($id);
        $viewModel = new RoomTypeViewModel($roomType);
        return view('admin.room-type.edit', compact('viewModel', 'roomType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RoomTypeRequest $request, string $id, UpdateRoomTypeAction $action)
    {
        try {
            $data = RoomTypeData::from($request->validated());
            $action->execute((int) $id, $data);

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
