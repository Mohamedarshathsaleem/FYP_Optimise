<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EnergyData;
use App\Models\EnergyDataUsage;

class EnergyDataUsageSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure energy data entries exist
        if (EnergyData::count() === 0) {
            EnergyData::create(['energy_type' => 'Electricity', 'provider' => 'TNB', 'account_no' => 'E-10234', 'contract_type' => 'Industrial']);
            EnergyData::create(['energy_type' => 'Natural Gas', 'provider' => 'Gas Malaysia', 'account_no' => 'G-50891', 'contract_type' => 'Commercial']);
        }

        $energyDataEntries = EnergyData::all();
        $years = range(2022, 2025);

        // Base values in original units (before GJ conversion)
        // Electricity: ~150,000 kWh/month base, Natural Gas: ~12,000 m3/month base
        $baselines = [];
        foreach ($energyDataEntries as $idx => $ed) {
            if ($idx === 0) {
                $baselines[$ed->id] = ['value' => 150000, 'unit' => 'kWh', 'cost_rate' => 0.30];
            } else {
                $baselines[$ed->id] = ['value' => 12000, 'unit' => 'm3', 'cost_rate' => 2.50];
            }
        }

        // Seasonal multipliers (energy usage higher in hot months)
        $seasonalFactors = [
            1 => 0.85, 2 => 0.88, 3 => 0.95, 4 => 1.05,
            5 => 1.12, 6 => 1.18, 7 => 1.20, 8 => 1.15,
            9 => 1.08, 10 => 1.00, 11 => 0.90, 12 => 0.82,
        ];

        // Year-over-year growth
        $yearGrowth = [2022 => 1.0, 2023 => 1.06, 2024 => 1.13, 2025 => 1.20];

        foreach ($years as $year) {
            $growth = $yearGrowth[$year];
            foreach (range(1, 12) as $month) {
                $seasonal = $seasonalFactors[$month];
                $monthKey = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT);

                foreach ($energyDataEntries as $ed) {
                    $base = $baselines[$ed->id];
                    $noise = 1.0 + (mt_rand(-80, 80) / 1000);
                    $usageValue = round($base['value'] * $growth * $seasonal * $noise, 3);
                    $gj = EnergyDataUsage::calculateGJ($usageValue, $base['unit']);
                    $cost = round($usageValue * $base['cost_rate'] * $noise, 2);

                    EnergyDataUsage::updateOrCreate(
                        ['energy_data_id' => $ed->id, 'month' => $monthKey],
                        [
                            'usage_value' => $usageValue,
                            'usage_unit' => $base['unit'],
                            'usage_gj' => $gj,
                            'cost' => $cost,
                        ]
                    );
                }
            }
        }
    }
}
