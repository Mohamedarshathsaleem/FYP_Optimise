<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('energy_resource_conversion_factors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('energy_resource_data_id');
            $table->string('from_unit', 20);
            $table->string('to_unit', 20)->default('GJ');
            $table->decimal('factor', 18, 8);
            $table->string('notes', 255)->nullable();
            $table->timestamps();

            $table->foreign('energy_resource_data_id', 'ercf_resource_data_fk')
                  ->references('id')->on('energy_resource_data')
                  ->onDelete('cascade');

            $table->unique(['energy_resource_data_id', 'from_unit'], 'ercf_unique_unit');
            $table->index('energy_resource_data_id', 'ercf_resource_data_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('energy_resource_conversion_factors');
    }
};
