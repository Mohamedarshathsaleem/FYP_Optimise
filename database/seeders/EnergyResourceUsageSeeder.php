<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EnergyResourceData;
use App\Models\EnergyResourceUsage;

class EnergyResourceUsageSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure energy resource data entries exist
        if (EnergyResourceData::count() === 0) {
            EnergyResourceData::create(['resource_type' => 'Biomass', 'provider' => 'BioFuel Sdn Bhd', 'account_no' => 'BM-7712', 'contract_type' => 'Annual']);
            EnergyResourceData::create(['resource_type' => 'Diesel', 'provider' => 'Petronas', 'account_no' => 'D-3305', 'contract_type' => 'Spot']);
        }

        $resourceEntries = EnergyResourceData::all();
        $years = range(2022, 2025);

        // Base values in original units
        // Biomass: ~8,000 kg/month, Diesel: ~3,500 L/month
        $baselines = [];
        foreach ($resourceEntries as $idx => $rd) {
            if ($idx === 0) {
                $baselines[$rd->id] = ['value' => 8000, 'unit' => 'kg', 'cost_rate' => 1.20];
            } else {
                $baselines[$rd->id] = ['value' => 3500, 'unit' => 'L', 'cost_rate' => 3.80];
            }
        }

        // Seasonal multipliers (slightly different pattern for resources)
        $seasonalFactors = [
            1 => 0.90, 2 => 0.92, 3 => 0.98, 4 => 1.03,
            5 => 1.08, 6 => 1.12, 7 => 1.15, 8 => 1.10,
            9 => 1.05, 10 => 1.00, 11 => 0.93, 12 => 0.88,
        ];

        $yearGrowth = [2022 => 1.0, 2023 => 1.04, 2024 => 1.09, 2025 => 1.15];

        foreach ($years as $year) {
            $growth = $yearGrowth[$year];
            foreach (range(1, 12) as $month) {
                $seasonal = $seasonalFactors[$month];
                $monthKey = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT);

                foreach ($resourceEntries as $rd) {
                    $base = $baselines[$rd->id];
                    $noise = 1.0 + (mt_rand(-100, 100) / 1000);
                    $usageValue = round($base['value'] * $growth * $seasonal * $noise, 3);
                    $gj = EnergyResourceUsage::calculateGJ($usageValue, $base['unit']);
                    $cost = round($usageValue * $base['cost_rate'] * $noise, 2);

                    EnergyResourceUsage::updateOrCreate(
                        ['energy_resource_data_id' => $rd->id, 'month' => $monthKey],
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
