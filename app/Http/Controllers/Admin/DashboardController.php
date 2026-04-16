<?php
// app/Http/Controllers/Admin/UserPermissionController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
     public function index()
    {
        
        $total_users = DB::table('users')->count();
        $users_today = DB::table('users')
            ->whereDate('updated_at', today())
            ->count(); // User yang login hari ini
        $active_users = DB::table('users')
            ->where('updated_at', '>=', now()->subDays(7))
            ->count(); // Aktif 7 hari terakhir
        
        return view('dashboard', [
            'total_users' => $total_users,
            'users_today' => $users_today,
            'active_users' => $active_users,
        ]);
    }

}
