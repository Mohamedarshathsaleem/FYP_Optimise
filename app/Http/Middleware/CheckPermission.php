<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPermission
{
    public function handle(Request $request, Closure $next, $module, $action = 'view')
{
    if (!auth()->check()) {
        return redirect()->route('login');
    }

    $user = auth()->user();

    $permissionName = strtolower("{$module}.{$action}");

    //Gunakan parameter gabungan izin yang sesuai
    if (!$user->hasPermission($permissionName)) {
        abort(403, "UNAUTHORIZED: Missing permission {$permissionName}");
    }

    return $next($request);
}

}
