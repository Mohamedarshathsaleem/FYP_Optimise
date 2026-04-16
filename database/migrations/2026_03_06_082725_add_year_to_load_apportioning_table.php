<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasColumn('load_apportioning', 'year')) {
            return;
        }

        Schema::table('load_apportioning', function (Blueprint $table) {
            $table->smallInteger('year')->after('id')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('load_apportioning', function (Blueprint $table) {
            $table->dropColumn('year');
        });
    }
};
