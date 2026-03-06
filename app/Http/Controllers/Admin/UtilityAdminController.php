<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Utilities\CreateUtilityAction;
use App\Actions\Utilities\DeleteUtilityAction;
use App\Actions\Utilities\GetUtilityListAction;
use App\Actions\Utilities\UpdateUtilityAction;
use App\Data\UtilityData;
use App\Http\Controllers\Controller;
use App\Models\Utility;
use App\ViewModels\UtilityViewModel;
use Exception;
use GuzzleHttp\Promise\Create;
use Illuminate\Http\Request;

class UtilityAdminController extends Controller
{
    public function index(GetUtilityListAction $action)
    {
        try {
            $utilities = $action->executePaginated(perPage: 10);

            return view('admin.utilities.index', compact('utilities'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function create()
    {
        $viewModel = new UtilityViewModel();
        return view('admin.utilities.create', compact('viewModel'));
    }

    public function store(UtilityData $request, CreateUtilityAction $action)
    {
        try {
            $utility = $action->execute($request);
            return redirect()->route('admin.utilities.index')->with('success', 'Tiện ích đã được tạo thành công!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function edit(Utility $utility)
    {
        $viewModel = new UtilityViewModel($utility);
        return view('admin.utilities.edit', compact('viewModel'));
    }

    public function update(UtilityData $request, Utility $utility, UpdateUtilityAction $action)
    {
        try {
            $updatedUtility = $action->execute($utility, $request);
            return redirect()->route('admin.utilities.index')->with('success', 'Tiện ích đã được cập nhật thành công!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function destroy(Utility $utility, DeleteUtilityAction $action)
    {
        try {
            $action->execute($utility);

            return response()->json(['message' => 'Tiện ích đã được xóa thành công!']);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}
