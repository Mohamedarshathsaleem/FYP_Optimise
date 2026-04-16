<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversion_factors', function (Blueprint $table) {
            $table->id();
            $table->string('energy_type', 100);
            $table->string('from_unit', 30);
            $table->string('to_unit', 30);
            $table->decimal('factor', 18, 8);
            $table->boolean('is_default')->default(false);
            $table->foreignId('organization_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index(['energy_type', 'from_unit', 'to_unit'], 'cf_lookup_idx');
            $table->index(['organization_id', 'energy_type'], 'cf_org_energy_idx');
            $table->index('is_default');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversion_factors');
    }
};
