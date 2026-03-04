<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Dining;

class DiningController extends Controller
{
    /**
     * Hiển thị danh sách nhà hàng và quầy bar (dùng Eloquent trực tiếp - Query Action theo rule.md)
     */
    public function index()
    {
        $dinings = Dining::where('is_active', true)->get();

        return view('client.dinings.index', compact('dinings'));
    }
}
