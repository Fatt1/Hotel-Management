<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Dining;

class DiningController extends Controller
{
    /**
     * Hiển thị danh sách nhà hàng và quầy bar (static HTML)
     */
    public function index()
    {
        return view('client.dinings.index');
    }
}
