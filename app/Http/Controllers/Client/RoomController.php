<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\RoomType;

class RoomController extends Controller
{
    /**
     * Hiển thị danh sách loại phòng (dùng Eloquent trực tiếp - Query Action theo rule.md)
     */
    public function index()
    {
        $roomTypes = RoomType::where('is_active', true)
            ->with(['images', 'amenities'])
            ->get();

        return view('client.rooms.index', compact('roomTypes'));
    }

    /**
     * Hiển thị chi tiết một loại phòng
     */
    public function show(int $id)
    {
        $roomType = RoomType::with(['images', 'amenities', 'rooms'])
            ->findOrFail($id);

        return view('client.rooms.show', compact('roomType'));
    }
}
