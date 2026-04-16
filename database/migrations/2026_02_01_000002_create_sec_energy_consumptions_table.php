<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
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

    public function down()
    {
        Schema::dropIfExists('sec_energy_consumptions');
    }
};
