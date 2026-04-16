<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('eip_currency_rates', function (Blueprint $table) {
            $table->id();
            $table->string('currency_code', 3);
            $table->decimal('rate_to_myr', 12, 6);
            $table->date('effective_date');
            $table->timestamps();
            $table->index(['currency_code', 'effective_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eip_currency_rates');
    }
};
