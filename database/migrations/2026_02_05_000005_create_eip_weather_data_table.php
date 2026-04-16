<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('eip_weather_data', function (Blueprint $table) {
            $table->id();
            $table->string('month', 7); // 'YYYY-MM'
            $table->decimal('avg_temperature', 5, 2)->nullable();
            $table->decimal('avg_humidity', 5, 2)->nullable();
            $table->decimal('heating_degree_days', 8, 2)->nullable();
            $table->decimal('cooling_degree_days', 8, 2)->nullable();
            $table->timestamps();
            $table->unique('month');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eip_weather_data');
    }
};
