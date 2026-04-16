<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monthly_production_usages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('monthly_production_id');
            $table->string('month', 7); // e.g., '2026-01'
            $table->decimal('production_amount', 12, 3); // e.g., 12345.678
            $table->string('production_unit', 10); // e.g., 'Gallon', 'Ton', 'Kg', 'L'
            $table->string('notes', 255)->nullable();
            $table->timestamps();

            $table->foreign('monthly_production_id')
                ->references('id')->on('monthly_productions')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monthly_production_usages');
    }
};