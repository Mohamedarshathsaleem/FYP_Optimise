<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Give Settings menu a route so it links to the under-development page
        // and no longer incorrectly matches every URL as active
        DB::table('menus')
            ->where('slug', 'settings')
            ->whereNull('route')
            ->update(['route' => 'settings', 'updated_at' => now()]);
    }

    public function down(): void
    {
        DB::table('menus')
            ->where('slug', 'settings')
            ->where('route', 'settings')
            ->update(['route' => null, 'updated_at' => now()]);
    }
};
