<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RoomTypeRequest;
use App\Models\RoomType;
use Illuminate\Http\Request;

class RoomTypeAdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roomTypes = RoomType::paginate(10);
        return view('admin.room-type.index', compact('roomTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.room-type.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RoomTypeRequest $request)
    {
        $validated = $request->validated();
        
        RoomType::create($validated);
        
        return redirect()
            ->route('admin.room-types.index')
            ->with('success', 'Loại phòng đã được tạo thành công');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // $roomType = RoomType::findOrFail($id);
        return view('admin.room-type.show'/*, compact('roomType') */);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $roomType = RoomType::findOrFail($id);
        return view('admin.room-type.edit', compact('roomType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RoomTypeRequest $request, string $id)
    {
        $roomType = RoomType::findOrFail($id);
        $validated = $request->validated();
        
        $roomType->update($validated);
        
        return redirect()
            ->route('admin.room-types.index')
            ->with('success', 'Loại phòng đã được cập nhật thành công');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $roomType = RoomType::findOrFail($id);
        
        // Kiểm tra xem loại phòng này có rooms không
        if ($roomType->rooms()->exists()) {
            return redirect()
                ->route('admin.room-types.index')
                ->with('error', 'Không thể xóa loại phòng này vì đã có phòng được tạo từ loại này');
        }
        
        $roomType->delete();
        
        return redirect()
            ->route('admin.room-types.index')
            ->with('success', 'Loại phòng đã được xóa thành công');
    }
}
