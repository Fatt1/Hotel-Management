<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Gallery;

class GalleryController extends Controller
{
    /**
     * Hiển thị toàn bộ album ảnh khách sạn (dùng Eloquent trực tiếp - Query Action theo rule.md)
     */
    public function index()
    {
        // Lấy tất cả ảnh, nhóm theo category để View dễ render từng tab/section
        $galleries = Gallery::all()->groupBy('category');

        return view('client.gallery.index', compact('galleries'));
    }
}
