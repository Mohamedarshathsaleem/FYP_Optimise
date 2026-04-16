<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('name');
        });

        // Convert existing name to slug
        DB::statement("UPDATE permissions SET slug = name");

        // Make slug unique after data migration
        Schema::table('permissions', function (Blueprint $table) {
            $table->string('slug')->unique()->change();
        });
    }

    public function down(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
