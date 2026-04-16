<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('committees', function (Blueprint $table) {
            $table->id();
            $table->string('committee_id')->unique();
            $table->string('name');
            $table->string('position');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('role', ['Chairperson', 'Secretary', 'Member']);
            $table->string('department');
            $table->string('communication_method');
            $table->text('responsibilities');
            $table->timestamps();

            // Indexes for better performance
            $table->index('role');
            $table->index('department');
            $table->index('start_date');
            $table->index('end_date');
        });
    }

    public function down()
    {
        Schema::dropIfExists('committees');
    }
};
