<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RoomDiagramAdminController extends Controller
{
    /**
     * Display a listing of room diagrams.
     */
    public function index()
    {
        return view('admin.room-diagram.index');
    }

    /**
     * Show the form for editing the room diagram.
     */
    public function edit()
    {
        return view('admin.room-diagram.edit');
    }

    /**
     * Update the room diagram in storage.
     */
    public function update(Request $request)
    {
        // Xử lý cập nhật sơ đồ phòng
        return redirect()->route('admin.room-diagrams.edit')->with('success', 'Sơ đồ phòng đã được cập nhật');
    }
}
