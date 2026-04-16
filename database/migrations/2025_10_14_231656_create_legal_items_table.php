<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('legal_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('legal_id')->constrained()->onDelete('cascade');
            $table->string('item_id'); // LR-EECA-001, LR-EECA-002, etc.
            $table->text('description')->nullable();
            $table->json('details')->nullable(); // Store additional details as JSON
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['legal_id', 'is_active']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('legal_items');
    }
};
