<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seu_flagging', function (Blueprint $table) {
            $table->boolean('auto_flagged')->default(true)->after('is_flagged')
                ->comment('Criteria-calculated flag value, unaffected by manual overrides');
        });

        // Non-overridden rows: their current is_flagged IS the auto-calculated value
        DB::statement('UPDATE seu_flagging SET auto_flagged = is_flagged WHERE is_manually_overridden = 0');
        // Manually-overridden rows: best approximation — user toggled once from the opposite state
        DB::statement('UPDATE seu_flagging SET auto_flagged = NOT is_flagged WHERE is_manually_overridden = 1');
    }

    public function down(): void
    {
        Schema::table('seu_flagging', function (Blueprint $table) {
            $table->dropColumn('auto_flagged');
        });
    }
};
