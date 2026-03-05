<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Amenity;

class AmenityController extends Controller
{
    /**
     * Hiển thị tất cả tiện ích của khách sạn (static HTML)
     */
    public function index()
    {
        return view('client.amenities.index');
    }
}
