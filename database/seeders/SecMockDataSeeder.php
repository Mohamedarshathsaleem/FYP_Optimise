<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\SecPoeAllocation;

class SecMockDataSeeder extends Seeder
{
    public function run()
    {
        // This seeder now only handles SEC-specific data (POE allocations).
        // Energy, resource, and production data are seeded by usage table seeders
        // which are the single source of truth.

        $products = Product::where('is_active', true)->orderBy('id')->get();

        if ($products->isEmpty()) {
            return; // MonthlyProductionUsageSeeder must run first to create products
        }

        $years = range(2022, 2025);

        // POE allocations must sum to 100% per year for each category
        // Distribute across all 5 active products
        $poeDistributions = [
            2022 => [30, 25, 20, 15, 10],
            2023 => [28, 24, 22, 14, 12],
            2024 => [26, 22, 22, 16, 14],
            2025 => [24, 22, 20, 18, 16],
        ];

        $categories = ['Production', 'Sales', 'Output'];

        foreach ($categories as $category) {
            foreach ($years as $year) {
                $dist = $poeDistributions[$year];
                $productList = $products->values();
                foreach ($productList as $i => $product) {
                    if (isset($dist[$i])) {
                        SecPoeAllocation::updateOrCreate(
                            ['product_id' => $product->id, 'year' => $year, 'poe_category' => $category],
                            ['percentage' => $dist[$i]]
                        );
                    }
                }
            }
        }
    }
}
