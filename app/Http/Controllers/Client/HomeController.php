<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\RoomType;
use App\Models\Amenity;

class HomeController extends Controller
{
    public function index()
    {
        // Lấy 6 room type nổi bật để hiển thị "Curated Stays"
        $featuredRooms = RoomType::with(['images'])
            ->where('is_active', true)
            ->orderBy('daily_price', 'desc')
            ->take(6)
            ->get();

        // Lấy 4 tiện ích nổi bật (amenities)
        $featuredAmenities = Amenity::take(4)->get();

        return view('client.home.index', compact('featuredRooms', 'featuredAmenities'));
    }
}
