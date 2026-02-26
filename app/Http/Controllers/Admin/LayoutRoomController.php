<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LayoutRoomController extends Controller
{
    public function index() {
        return view("admin.layout-room.index");
    }
}
