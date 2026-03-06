<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Equipments\CreateEquipmentAction;
use App\Actions\Equipments\DeleteEquipmentAction;
use App\Actions\Equipments\GetEquipmentListAction;
use App\Actions\Equipments\UpdateEquipmentAction;
use App\Data\EquipmentData;
use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\ViewModels\EquipmentViewModel;
use Exception;
use Illuminate\Http\Request;

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
        $viewModel = new EquipmentViewModel();

        return view('admin.equipments.create', [
            'viewModel' => $viewModel,
        ]);
    }

    public function store(EquipmentData $request, CreateEquipmentAction $action)
    {
        try {
            $equipment = $action->execute($request);

            return redirect()->route('admin.equipments.index')->with('success', 'Thêm thiết bị thành công!');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function edit(Equipment $equipment)
    {
        $viewModel = new EquipmentViewModel($equipment);

        return view('admin.equipments.edit', [
            'viewModel' => $viewModel,
        ]);
    }

    public function update(
        EquipmentData $request,
        Equipment $equipment,
        UpdateEquipmentAction $action
    ) {
        try {
            $updated = $action->execute($equipment, $request);

            return redirect()->route('admin.equipments.index')->with('success', 'Cập nhật thiết bị thành công!');
        } catch (\Exception $e) {

            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function destroy(Equipment $equipment, DeleteEquipmentAction $action)
    {
        
        try {
            $action->execute($equipment);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
