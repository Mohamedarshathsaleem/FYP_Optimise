<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('energy_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('conversion_coefficient');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('energy_types');
    }
};
