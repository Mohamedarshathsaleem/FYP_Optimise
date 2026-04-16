<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sec_monthly_poe', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('month', 7); // YYYY-MM format
            $table->string('poe_category', 30); // Production, Sales, Output
            $table->decimal('percentage', 8, 4)->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->unique(['product_id', 'month', 'poe_category'], 'smp_unique');
            $table->index(['month', 'poe_category'], 'smp_filter_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sec_monthly_poe');
    }
};
