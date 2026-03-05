<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Utilities\CreateUtilityAction;
use App\Actions\Utilities\DeleteUtilityAction;
use App\Actions\Utilities\GetUtilityListAction;
use App\Actions\Utilities\UpdateUtilityAction;
use App\Data\UtilityData;
use App\Http\Controllers\Controller;
use App\Models\Utility;
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
        // $viewModel = new Ul();
        return view('admin.utilities.create');
    }

    public function store(Request $request, CreateUtilityAction $action)
    {
        try {
            $data = UtilityData::from($request->all());

            $utility = $action->execute($data);

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Tiện ích đã được tạo thành công!', 'data' => $utility]);
            }

            return redirect()->route('admin.utilities.index')->with('success', 'Tiện ích đã được tạo thành công!');
        } catch (Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['message' => $e->getMessage()], 422);
            }

            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function edit(Utility $utility)
    {
        return view('admin.utilities.edit', compact('utility'));
    }

    public function update(Request $request, Utility $utility, UpdateUtilityAction $action)
    {
        try {
            $data = UtilityData::from($request->all());

            $updatedUtility = $action->execute($utility, $data);

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Tiện ích đã được cập nhật thành công!', 'data' => $updatedUtility]);
            }

            return redirect()->route('admin.utilities.index')->with('success', 'Tiện ích đã được cập nhật thành công!');
        } catch (Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['message' => $e->getMessage()], 422);
            }

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
