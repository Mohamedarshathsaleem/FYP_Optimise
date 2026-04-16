<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Drop old table that was keyed by energy_type_id
        Schema::dropIfExists('sec_energy_consumptions');

        // Recreate keyed by energy_data_id
        Schema::create('sec_energy_consumptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('energy_data_id')->constrained('energy_data')->onDelete('cascade');
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('month');
            $table->decimal('value_gj', 12, 4)->default(0);
            $table->timestamps();

            $table->unique(['energy_data_id', 'year', 'month']);
        });

        // New table for energy resource consumption
        Schema::create('sec_resource_consumptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('energy_resource_data_id')->constrained('energy_resource_data')->onDelete('cascade');
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('month');
            $table->decimal('value_gj', 12, 4)->default(0);
            $table->timestamps();

            $table->unique(['energy_resource_data_id', 'year', 'month'], 'sec_res_cons_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('sec_resource_consumptions');
        Schema::dropIfExists('sec_energy_consumptions');

        // Restore original sec_energy_consumptions
        Schema::create('sec_energy_consumptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('energy_type_id')->constrained('energy_types')->onDelete('cascade');
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('month');
            $table->decimal('value_gj', 12, 4)->default(0);
            $table->timestamps();

            $table->unique(['energy_type_id', 'year', 'month']);
        });
    }
};
