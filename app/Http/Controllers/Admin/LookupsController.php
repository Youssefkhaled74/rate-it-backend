<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class LookupsController extends Controller
{
    public function index()
    {
        return view('admin.lookups.index');
    }
}
