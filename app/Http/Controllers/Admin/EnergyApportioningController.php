<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EnergyApportioningController extends Controller
{
    public function index()
    {
        return view('admin.load-apportioning-energy.index');
    }



}
