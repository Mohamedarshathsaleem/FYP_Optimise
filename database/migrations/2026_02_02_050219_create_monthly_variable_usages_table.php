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
        Schema::create('monthly_variable_usages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('monthly_variable_id');
            $table->string('month', 7); // e.g., '2026-01'
            $table->decimal('variable_value', 12, 3); // e.g., 25.5 (temperature, etc.)
            $table->string('variable_unit', 10); // e.g., '°C', '°F', 'K'
            $table->string('notes', 255)->nullable();
            $table->timestamps();

            $table->foreign('monthly_variable_id')
                ->references('id')->on('monthly_variables')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_variable_usages');
    }
};