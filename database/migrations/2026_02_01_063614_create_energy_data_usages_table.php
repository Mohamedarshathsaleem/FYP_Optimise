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
        Schema::create('energy_data_usages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('energy_data_id');
            $table->string('month', 7); // e.g., '2026-01'
            $table->decimal('usage_value', 12, 3); // e.g., 12345.678 kWh
            $table->string('usage_unit', 10); // e.g., 'kWh', 'MWh'
            $table->decimal('usage_gj', 12, 3); // calculated GJ value
            $table->decimal('cost', 12, 2)->nullable(); // cost of energy usage
            $table->string('notes', 255)->nullable();
            $table->timestamps();

            $table->foreign('energy_data_id')
                ->references('id')->on('energy_data')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('energy_data_usages');
    }
};
