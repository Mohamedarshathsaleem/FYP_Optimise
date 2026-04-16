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
       Schema::create('motivation_strategies', function (Blueprint $table) {
            $table->id();
            $table->string('motivation_activity');
            $table->string('target_group');
            $table->string('criteria_for_recognition');
            $table->string('recognition_method');
            $table->enum('frequency', ['Monthly', 'Quarterly', 'Annually', 'Bi-annually']);
            $table->string('responsible_dept');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('motivation_strategies');
    }
};
