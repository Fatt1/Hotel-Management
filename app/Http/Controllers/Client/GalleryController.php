<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Gallery;

class GalleryController extends Controller
{
    /**
     * Hiển thị toàn bộ album ảnh khách sạn (static HTML)
     */
    public function index()
    {
        return view('client.gallery.index');
    }
}
