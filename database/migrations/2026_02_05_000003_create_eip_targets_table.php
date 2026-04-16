<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('eip_targets', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('year');
            $table->string('target_type', 30); // eip_energy, eip_resource, eip_combined, consumption, cost
            $table->decimal('target_value', 12, 4);
            $table->decimal('seu_threshold', 12, 4)->nullable();
            $table->string('notes', 255)->nullable();
            $table->timestamps();
            $table->unique(['year', 'target_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eip_targets');
    }
};
