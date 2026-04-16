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
        Schema::table('permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('menu_id')->nullable()->after('description');
            $table->foreign('menu_id')->references('id')->on('menus')->onDelete('set null');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropForeign(['menu_id']); // Menghapus foreign key constraint
            $table->dropColumn('menu_id');    // Menghapus kolom menu_id
        });
    }
};
