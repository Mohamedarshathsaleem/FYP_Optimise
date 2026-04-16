<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('energy_data_conversion_factors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('energy_data_id');
            $table->string('from_unit', 20);
            $table->string('to_unit', 20)->default('GJ');
            $table->decimal('factor', 18, 8);
            $table->string('notes', 255)->nullable();
            $table->timestamps();

            $table->foreign('energy_data_id')
                  ->references('id')->on('energy_data')
                  ->onDelete('cascade');

            $table->unique(['energy_data_id', 'from_unit'], 'edcf_unique_unit');
            $table->index('energy_data_id', 'edcf_energy_data_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('energy_data_conversion_factors');
    }
};
