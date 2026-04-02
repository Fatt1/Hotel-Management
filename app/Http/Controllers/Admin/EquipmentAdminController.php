<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Equipments\CreateEquipmentAction;
use App\Actions\Equipments\DeleteEquipmentAction;
use App\Actions\Equipments\GetEquipmentListAction;
use App\Actions\Equipments\UpdateEquipmentAction;
use App\Data\EquipmentData;
use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\EquipmentCategory;
use App\ViewModels\EquipmentViewModel;
use Exception;
use Illuminate\Http\Request;

class EquipmentAdminController extends Controller
{
    public function index(Request $request, GetEquipmentListAction $action)
    {
        $filters = [
            'search'                => $request->input('search'),
            'equipment_category_id' => $request->input('category_id'),
        ];

        $perPage = (int) $request->input('page_size', 10);
        $perPage = max(5, min(100, $perPage));

        $equipments = $action->executePaginated(filters: $filters, perPage: $perPage);
        $categories = EquipmentCategory::orderBy('name')->get();

        return view('admin.equipments.index', compact('equipments', 'categories'));
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
