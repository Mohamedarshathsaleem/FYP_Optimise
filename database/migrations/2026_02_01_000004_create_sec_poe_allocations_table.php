<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sec_poe_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->unsignedSmallInteger('year');
            $table->decimal('percentage', 5, 2);
            $table->string('poe_category')->default('Production');
            $table->timestamps();

            $table->unique(['product_id', 'year', 'poe_category']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('sec_poe_allocations');
    }
};
