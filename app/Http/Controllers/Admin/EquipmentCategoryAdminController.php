<?php

namespace App\Http\Controllers\Admin;

use App\Actions\EquipmentCategories\CreateEquipmentCategoryAction;
use App\Actions\EquipmentCategories\DeleteEquipmentCategoryAction;
use App\Actions\EquipmentCategories\GetEquipmentCategoryListAction;
use App\Actions\EquipmentCategories\UpdateEquipmentCategoryAction;
use App\Data\EquipmentCategoryData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\EquipmentCategoryRequest;
use App\Models\EquipmentCategory;
use App\ViewModels\EquipmentCategoryViewModel;

class EquipmentCategoryAdminController extends Controller
{
    /**
     * Danh sách loại thiết bị
     */
    public function index(GetEquipmentCategoryListAction $action)
    {
        $equipmentCategories = $action->executePaginated(perPage: 10);
        return view('admin.equipment-category.index', compact('equipmentCategories'));
    }

    /**
     * Form tạo mới loại thiết bị
     */
    public function create()
    {
        $viewModel = new EquipmentCategoryViewModel();
        return view('admin.equipment-category.modal-form', compact('viewModel'));
    }

    /**
     * Lưu loại thiết bị mới
     */
    public function store(EquipmentCategoryRequest $request, CreateEquipmentCategoryAction $action)
    {
        try {
            $data = EquipmentCategoryData::from($request->validated());
            $category = $action->execute($data);

            // Check if it's an AJAX request
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Loại thiết bị đã được tạo thành công',
                    'data' => $category
                ], 201);
            }

            return redirect()
                ->route('admin.equipment-categories.index')
                ->with('success', 'Loại thiết bị đã được tạo thành công');
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 400);
            }
            
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Chi tiết loại thiết bị
     */
    public function show(string $id)
    {
        $equipmentCategory = EquipmentCategory::findOrFail($id);
        $viewModel = new EquipmentCategoryViewModel($equipmentCategory);
        return view('admin.equipment-category.show', compact('viewModel'));
    }

    /**
     * Form chỉnh sửa loại thiết bị
     */
    public function edit(EquipmentCategory $equipment_category)
    {
        $viewModel = new EquipmentCategoryViewModel($equipment_category);
        return view('admin.equipment-category.modal-form', compact('viewModel'));
    }

    /**
     * Cập nhật loại thiết bị
     */
    public function update(EquipmentCategoryRequest $request, EquipmentCategory $equipment_category, UpdateEquipmentCategoryAction $action)
    {
        try {
            $data = EquipmentCategoryData::from($request->validated());
            $category = $action->execute($equipment_category->id, $data);

            // Check if it's an AJAX request
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Loại thiết bị đã được cập nhật thành công',
                    'data' => $category
                ], 200);
            }

            return redirect()
                ->route('admin.equipment-categories.index')
                ->with('success', 'Loại thiết bị đã được cập nhật thành công');
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 400);
            }

            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Xóa loại thiết bị
     */
    public function destroy(EquipmentCategory $equipment_category, DeleteEquipmentCategoryAction $action)
    {
        try {
            $action->execute($equipment_category->id);

            // Check if it's an AJAX request
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Loại thiết bị đã được xóa thành công'
                ], 200);
            }

            return redirect()
                ->route('admin.equipment-categories.index')
                ->with('success', 'Loại thiết bị đã được xóa thành công');
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 400);
            }

            return redirect()
                ->route('admin.equipment-categories.index')
                ->with('error', $e->getMessage());
        }
    }
}
