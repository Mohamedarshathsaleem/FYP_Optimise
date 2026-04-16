<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('load_apportioning', 'seu_category')) {
            return;
        }

        Schema::table('load_apportioning', function (Blueprint $table) {
            $table->string('seu_category', 50)->nullable()->after('approach_id')
                ->comment('Energy type ID (e.g., edata_1, rdata_2)');
            $table->index('seu_category');
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('load_apportioning', 'seu_category')) {
            Schema::table('load_apportioning', function (Blueprint $table) {
                $table->dropColumn('seu_category');
            });
        }
    }
};
