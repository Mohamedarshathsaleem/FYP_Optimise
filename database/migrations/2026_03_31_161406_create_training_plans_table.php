<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('training_plans', function (Blueprint $table) {
            $table->id();
            $table->string('competency_area');
            $table->text('required_knowledge');
            $table->string('target_group');
            $table->enum('competency_level', [
                '1',           // Complete level 1
                '2',           // Complete level 2  
                '1 to 4',      // Range 1 to 4
                '1 to 5',      // Range 1 to 5
                '2 to 5',      // Range 2 to 5
                '3',           // Complete level 3
                '4'            // Complete level 4
            ])->default('1');
            $table->text('training_needs');
            $table->string('training_method');
            $table->string('frequency');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('training_plans');
    }
};