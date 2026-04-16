<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('energy_data', function (Blueprint $table) {
            $table->string('category')->default('Industrial')->after('id');
            $table->index('category');
        });

        Schema::table('energy_resource_data', function (Blueprint $table) {
            $table->string('category')->default('Industrial')->after('id');
            $table->index('category');
        });

        Schema::table('monthly_productions', function (Blueprint $table) {
            $table->string('category')->default('Industrial')->after('id');
            $table->index('category');
        });

        Schema::table('monthly_variables', function (Blueprint $table) {
            $table->string('category')->default('Industrial')->after('id');
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::table('energy_data', function (Blueprint $table) {
            $table->dropIndex(['category']);
            $table->dropColumn('category');
        });

        Schema::table('energy_resource_data', function (Blueprint $table) {
            $table->dropIndex(['category']);
            $table->dropColumn('category');
        });

        Schema::table('monthly_productions', function (Blueprint $table) {
            $table->dropIndex(['category']);
            $table->dropColumn('category');
        });

        Schema::table('monthly_variables', function (Blueprint $table) {
            $table->dropIndex(['category']);
            $table->dropColumn('category');
        });
    }
};