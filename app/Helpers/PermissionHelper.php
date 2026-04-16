<?php
// app/Helpers/PermissionHelper.php

if (!function_exists('checkPermissionOrFail')) {
    function checkPermissionOrFail($module, $action = 'can_view', $customMessage = null) {
        if (!auth()->check()) {
            return redirect('/login');
        }

        // if (!auth()->user()->canView($module)) {
        //     return response()->view('errors.403', [
        //         'requiredPermission' => ucfirst($module) . ' - ' . ucfirst(str_replace('can_', '', $action)),
        //         'currentRole' => auth()->user()->role,
        //         'message' => $customMessage ?: "You need permission to access the {$module} section."
        //     ], 403);
        // }

        return null;
    }
}
