<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MonthlyProduction;
use App\Models\MonthlyProductionUsage;
use App\Models\Product;

class MonthlyProductionUsageSeeder extends Seeder
{
    public function run(): void
    {
        // Create production types (5 products as requested)
        $productTypes = [
            'Product A Real',
            'Product B Real',
            'Manufacture',
            'Assembly Line',
            'Packaging',
        ];

        foreach ($productTypes as $type) {
            MonthlyProduction::firstOrCreate(['production_type' => $type]);
        }

        // Sync products from MonthlyProduction (mirrors SecAnalysisController logic)
        $monthlyProductions = MonthlyProduction::all();
        $syncedProductIds = [];
        foreach ($monthlyProductions as $production) {
            $product = Product::updateOrCreate(
                ['name' => $production->production_type],
                ['unit' => 'Tonne', 'is_active' => true]
            );
            $syncedProductIds[] = $product->id;
        }
        if (count($syncedProductIds) > 0) {
            Product::whereNotIn('id', $syncedProductIds)->update(['is_active' => false]);
        }

        $productions = MonthlyProduction::all();
        $years = range(2022, 2025);

        // Base monthly production amounts (tonnes)
        $baselines = [
            'Product A Real' => 12000,
            'Product B Real' => 8500,
            'Manufacture'    => 5000,
            'Assembly Line'  => 6500,
            'Packaging'      => 9000,
        ];

        // Production seasonal factors (dip in Dec/Jan for maintenance)
        $seasonalFactors = [
            1 => 0.88, 2 => 0.95, 3 => 1.02, 4 => 1.05,
            5 => 1.08, 6 => 1.10, 7 => 1.06, 8 => 1.04,
            9 => 1.02, 10 => 1.00, 11 => 0.95, 12 => 0.85,
        ];

        $yearGrowth = [2022 => 1.0, 2023 => 1.06, 2024 => 1.13, 2025 => 1.20];

        foreach ($years as $year) {
            $growth = $yearGrowth[$year];
            foreach (range(1, 12) as $month) {
                $seasonal = $seasonalFactors[$month];
                $monthKey = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT);

                foreach ($productions as $prod) {
                    $base = $baselines[$prod->production_type] ?? 5000;
                    $noise = 1.0 + (mt_rand(-50, 50) / 1000);
                    $amount = round($base * $growth * $seasonal * $noise, 3);

                    MonthlyProductionUsage::updateOrCreate(
                        ['monthly_production_id' => $prod->id, 'month' => $monthKey],
                        [
                            'production_amount' => $amount,
                            'production_unit' => 'Tonne',
                        ]
                    );
                }
            }
        }
    }
}
