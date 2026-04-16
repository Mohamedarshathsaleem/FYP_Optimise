<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ActionPlanController extends Controller
{
    public function overview()
    {
        return view('admin.action-plan.overview');
    }

    public function yearly()
    {
        return view('admin.action-plan.yearly');
    }

    public function storeYearly(Request $request)
    {
        //
    }
}
