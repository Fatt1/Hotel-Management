<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Floors\CreateFloorAction;
use App\Actions\Floors\DeleteFloorAction;
use App\Actions\Floors\UpdateFloorAction;
use App\Actions\Rooms\CreateRoomAction;
use App\Actions\Rooms\DeleteRoomAction;
use App\Actions\Rooms\UpdateRoomAction;
use App\Http\Controllers\Controller;
use App\ViewModels\RoomDiagramViewModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoomDiagramAdminController extends Controller
{
    /**
     * Display a listing of room diagrams.
     */
    public function index(Request $request)
    {
        return view('admin.room-diagram.index', [
            'viewModel' => new RoomDiagramViewModel($request),
        ]);
    }

    /**
     * Show the form for editing the room diagram.
     */
    public function edit(Request $request)
    {
        return view('admin.room-diagram.edit', [
            'viewModel' => new RoomDiagramViewModel($request),
        ]);
    }

    /**
     * Update the room diagram in storage.
     */
    public function update(Request $request)
    {
        // Xử lý cập nhật sơ đồ phòng
        return redirect()->route('admin.room-diagrams.edit')->with('success', 'Sơ đồ phòng đã được cập nhật');
    }

    // ============ FLOOR API ============

    /**
     * Tạo tầng mới
     */
    public function storeFloor(Request $request, CreateFloorAction $action): JsonResponse
    {
        try {
            $request->validate([
                'name' => 'required|string|max:50',
            ]);

            $floor = $action->execute($request->input('name'));

            return response()->json([
                'success' => true,
                'message' => 'Tạo tầng thành công',
                'data' => [
                    'id' => $floor->id,
                    'name' => $floor->name,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Cập nhật tầng
     */
    public function updateFloor(Request $request, int $id, UpdateFloorAction $action): JsonResponse
    {
        try {
            $request->validate([
                'name' => 'required|string|max:50',
            ]);

            $floor = $action->execute($id, $request->input('name'));

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật tầng thành công',
                'data' => [
                    'id' => $floor->id,
                    'name' => $floor->name,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Xóa tầng
     */
    public function destroyFloor(int $id, DeleteFloorAction $action): JsonResponse
    {
        try {
            $action->execute($id);

            return response()->json([
                'success' => true,
                'message' => 'Xóa tầng thành công',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    // ============ ROOM API ============

    /**
     * Tạo phòng mới
     */
    public function storeRoom(Request $request, CreateRoomAction $action): JsonResponse
    {
        try {
            $request->validate([
                'name' => 'required|string|max:20',
                'floor_id' => 'required|integer|exists:floors,id',
                'room_type_id' => 'required|integer|exists:room_types,id',
            ]);

            $room = $action->execute(
                $request->input('name'),
                (int) $request->input('floor_id'),
                (int) $request->input('room_type_id')
            );

            return response()->json([
                'success' => true,
                'message' => 'Tạo phòng thành công',
                'data' => [
                    'id' => $room->id,
                    'name' => $room->name,
                    'floor_id' => $room->floor_id,
                    'room_type_id' => $room->room_type_id,
                    'room_type_name' => $room->roomType->name ?? '',
                    'status' => $room->status,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Cập nhật phòng
     */
    public function updateRoom(Request $request, int $id, UpdateRoomAction $action): JsonResponse
    {
        try {
            $request->validate([
                'name' => 'required|string|max:20',
                'room_type_id' => 'required|integer|exists:room_types,id',
            ]);

            $room = $action->execute(
                $id,
                $request->input('name'),
                (int) $request->input('room_type_id')
            );

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật phòng thành công',
                'data' => [
                    'id' => $room->id,
                    'name' => $room->name,
                    'room_type_id' => $room->room_type_id,
                    'room_type_name' => $room->roomType->name ?? '',
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Xóa phòng
     */
    public function destroyRoom(int $id, DeleteRoomAction $action): JsonResponse
    {
        try {
            $action->execute($id);

            return response()->json([
                'success' => true,
                'message' => 'Xóa phòng thành công',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
