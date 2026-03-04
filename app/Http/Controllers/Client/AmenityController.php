<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Amenity;

class AmenityController extends Controller
{
    /**
     * Hiển thị tất cả tiện ích của khách sạn (dùng Eloquent trực tiếp - Query Action theo rule.md)
     */
    public function index()
    {
        $amenities = Amenity::all();

        return view('client.amenities.index', compact('amenities'));
    }
}
