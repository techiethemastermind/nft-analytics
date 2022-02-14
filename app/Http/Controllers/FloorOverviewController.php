<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FloorOverviewController extends Controller
{
    //
    public function index()
    {
        return view('floor-overview');
    }
}
