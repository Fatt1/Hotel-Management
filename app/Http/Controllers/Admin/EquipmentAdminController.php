<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Equipments\CreateEquipmentAction;
use App\Actions\Equipments\DeleteEquipmentAction;
use App\Actions\Equipments\GetEquipmentListAction;
use App\Actions\Equipments\UpdateEquipmentAction;
use App\Data\EquipmentData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\EquipmentRequest;
use App\Models\Equipment;
use App\Models\EquipmentCategory;

class EquipmentAdminController extends Controller
{
    public function index(GetEquipmentListAction $action)
    {
        $equipments = $action->executePaginated(perPage: 10);

        return view('admin.equipments.index', [
            'equipments' => $equipments,
        ]);
    }

    public function create()
    {
        $categories = EquipmentCategory::all();

        return view('admin.equipments.create', [
            'categories' => $categories,
        ]);
    }

    public function store(EquipmentRequest $request, CreateEquipmentAction $action)
    {
        try {
            $data = EquipmentData::from($request->validated());
            $equipment = $action->execute($data);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Thêm thiết bị thành công!',
                    'data' => $equipment,
                ], 201);
            }

            return redirect()->route('admin.equipments.index')->with('success', 'Thêm thiết bị thành công!');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 400);
            }
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function edit(Equipment $equipment)
    {
        $categories = EquipmentCategory::all();

        return view('admin.equipments.edit', [
            'equipment' => $equipment,
            'categories' => $categories,
        ]);
    }

    public function update(
        EquipmentRequest $request,
        Equipment $equipment,
        UpdateEquipmentAction $action
    ) {
        try {
            $data = EquipmentData::from($request->validated());
            $updated = $action->execute($equipment, $data);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Cập nhật thiết bị thành công!',
                    'data' => $updated,
                ], 200);
            }

            return redirect()->route('admin.equipments.index')->with('success', 'Cập nhật thiết bị thành công!');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 400);
            }
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function destroy(Equipment $equipment, DeleteEquipmentAction $action)
    {
        try {
            $action->execute($equipment);

            return response()->json([
                'success' => true,
                'message' => 'Xóa thiết bị thành công!',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
