<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('eip_normalization_factors', function (Blueprint $table) {
            $table->id();
            $table->string('month', 7); // 'YYYY-MM'
            $table->string('factor_type', 30); // working_days, degree_days, employees, hours, area
            $table->decimal('factor_value', 12, 4);
            $table->string('notes', 255)->nullable();
            $table->timestamps();
            $table->unique(['month', 'factor_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eip_normalization_factors');
    }
};
