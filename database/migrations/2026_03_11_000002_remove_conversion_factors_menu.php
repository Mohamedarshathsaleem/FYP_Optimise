<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Remove the Conversion Factors child menu entry
        DB::table('menus')->where('slug', 'conversion-factors')->delete();
    }

    public function down(): void
    {
        // Re-insert the Conversion Factors menu entry under Settings
        $settingsMenu = DB::table('menus')->where('slug', 'settings')->first();

        if ($settingsMenu) {
            DB::table('menus')->insert([
                'name'       => 'Conversion Factors',
                'slug'       => 'conversion-factors',
                'icon'       => null,
                'route'      => 'conversion-factors',
                'parent_id'  => $settingsMenu->id,
                'order'      => 1,
                'level'      => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
};
