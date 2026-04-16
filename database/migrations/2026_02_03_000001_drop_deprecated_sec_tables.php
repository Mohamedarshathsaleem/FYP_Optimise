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
        Schema::dropIfExists('sec_resource_consumptions');
        Schema::dropIfExists('sec_production_values');
        Schema::dropIfExists('sec_energy_consumptions');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // These tables are deprecated and replaced by:
        // - energy_data_usages (replaced sec_energy_consumptions)
        // - energy_resource_usages (replaced sec_resource_consumptions)
        // - monthly_production_usages (replaced sec_production_values)
        // No rollback support provided as these are obsolete tables.
    }
};
