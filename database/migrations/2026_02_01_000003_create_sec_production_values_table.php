<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sec_production_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('month');
            $table->decimal('quantity', 12, 4)->default(0);
            $table->timestamps();

            $table->unique(['product_id', 'year', 'month']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('sec_production_values');
    }
};
