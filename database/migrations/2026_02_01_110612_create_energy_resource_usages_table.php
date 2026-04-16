<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('energy_resource_usages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('energy_resource_data_id');
            $table->string('month', 7); // e.g., '2026-01'
            $table->decimal('usage_value', 12, 3); // e.g., 12345.678
            $table->string('usage_unit', 10); // e.g., 'L', 'kg', 'tons'
            $table->decimal('usage_gj', 12, 3); // calculated GJ value
            $table->decimal('cost', 12, 2)->nullable(); // cost of resource usage
            $table->string('notes', 255)->nullable();
            $table->timestamps();

            $table->foreign('energy_resource_data_id')
                ->references('id')->on('energy_resource_data')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('energy_resource_usages');
    }
};