<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('energy_units', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name', 100);
            $table->enum('unit_type', ['energy', 'volume', 'mass', 'other']);
            $table->string('symbol', 20)->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('unit_type');
            $table->index('is_active');
        });

        // Seed common units
        $units = [
            // Energy Units
            ['code' => 'GJ', 'name' => 'Gigajoule', 'unit_type' => 'energy', 'symbol' => 'GJ', 'sort_order' => 1],
            ['code' => 'MJ', 'name' => 'Megajoule', 'unit_type' => 'energy', 'symbol' => 'MJ', 'sort_order' => 2],
            ['code' => 'kWh', 'name' => 'Kilowatt Hour', 'unit_type' => 'energy', 'symbol' => 'kWh', 'sort_order' => 3],
            ['code' => 'MWh', 'name' => 'Megawatt Hour', 'unit_type' => 'energy', 'symbol' => 'MWh', 'sort_order' => 4],
            ['code' => 'BTU', 'name' => 'British Thermal Unit', 'unit_type' => 'energy', 'symbol' => 'BTU', 'sort_order' => 5],
            ['code' => 'MMBtu', 'name' => 'Million BTU', 'unit_type' => 'energy', 'symbol' => 'MMBtu', 'sort_order' => 6],
            ['code' => 'toe', 'name' => 'Tonne of Oil Equivalent', 'unit_type' => 'energy', 'symbol' => 'toe', 'sort_order' => 8],
            ['code' => 'tce', 'name' => 'Tonne of Coal Equivalent', 'unit_type' => 'energy', 'symbol' => 'tce', 'sort_order' => 9],
            ['code' => 'RTh', 'name' => 'Refrigeration Ton Hour', 'unit_type' => 'energy', 'symbol' => 'RTh', 'sort_order' => 10],
            // Volume Units
            ['code' => 'L', 'name' => 'Litre', 'unit_type' => 'volume', 'symbol' => 'L', 'sort_order' => 20],
            ['code' => 'm3', 'name' => 'Cubic Metre', 'unit_type' => 'volume', 'symbol' => 'm³', 'sort_order' => 22],
            ['code' => 'Gallon', 'name' => 'Gallon (US)', 'unit_type' => 'volume', 'symbol' => 'gal', 'sort_order' => 23],
            ['code' => 'mscf', 'name' => 'Thousand Standard Cubic Feet', 'unit_type' => 'volume', 'symbol' => 'mscf', 'sort_order' => 24],
            ['code' => 'mmscf', 'name' => 'Million Standard Cubic Feet', 'unit_type' => 'volume', 'symbol' => 'mmscf', 'sort_order' => 25],
            // Mass Units
            ['code' => 'kg', 'name' => 'Kilogram', 'unit_type' => 'mass', 'symbol' => 'kg', 'sort_order' => 30],
            ['code' => 'tonne', 'name' => 'Metric Tonne', 'unit_type' => 'mass', 'symbol' => 't', 'sort_order' => 31],
            ['code' => 'ton', 'name' => 'Ton', 'unit_type' => 'mass', 'symbol' => 'ton', 'sort_order' => 32],
            ['code' => 'lb', 'name' => 'Pound', 'unit_type' => 'mass', 'symbol' => 'lb', 'sort_order' => 33],
        ];

        $now = now();
        foreach ($units as $unit) {
            DB::table('energy_units')->insert(array_merge($unit, [
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('energy_units');
    }
};
