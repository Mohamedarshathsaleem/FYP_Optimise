<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('baseline_models', function (Blueprint $table) {
            $table->id();
            $table->string('model_name');
            $table->integer('number_of_independent_variables');
            $table->float('r_squared')->nullable();
            $table->text('equation')->nullable();
            $table->string('correlation_strength')->nullable();
            $table->integer('year');
            $table->string('dependent_variable');
            $table->string('independent_variable_x1');
            $table->string('independent_variable_x2')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('baseline_models');
    }
};
