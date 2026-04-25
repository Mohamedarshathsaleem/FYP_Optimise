<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EnergyData;
use App\Models\EnergyResourceData;
use App\Models\EnergyType;
use App\Models\Product;
use App\Models\MonthlyProduction;
use App\Models\EnergyDataUsage;
use App\Models\EnergyResourceUsage;
use App\Models\MonthlyProductionUsage;
use App\Models\SecPoeAllocation;
use App\Models\SecMonthlyPoe;
use Illuminate\Support\Facades\DB;

class SecAnalysisController extends Controller
{
    public function index()
    {
        $energyTypes = EnergyType::all();

        // Sync products from MonthlyProduction (single source of truth)
        $monthlyProductions = MonthlyProduction::all();
        $syncedProductIds = [];

        foreach ($monthlyProductions as $production) {
            $product = Product::updateOrCreate(
                ['name' => $production->production_type],
                [
                    'unit' => 'Tonne',
                    'is_active' => true
                ]
            );
            $syncedProductIds[] = $product->id;
        }

        // Deactivate products that no longer exist in Monthly Production
        if (count($syncedProductIds) > 0) {
            Product::whereNotIn('id', $syncedProductIds)->update(['is_active' => false]);
        }

        $products = Product::where('is_active', true)->orderBy('id')->get();
        $energySources = EnergyData::all();
        $resourceSources = EnergyResourceData::all();

        // Extract year from string format '2026-01' in usage tables
        $minYear = EnergyDataUsage::selectRaw('MIN(CAST(SUBSTRING(month, 1, 4) AS INTEGER)) as min_year')
            ->value('min_year')
            ?? EnergyResourceUsage::selectRaw('MIN(CAST(SUBSTRING(month, 1, 4) AS INTEGER)) as min_year')
                ->value('min_year')
            ?? MonthlyProductionUsage::selectRaw('MIN(CAST(SUBSTRING(month, 1, 4) AS INTEGER)) as min_year')
                ->value('min_year')
            ?? now()->year - 3;

        $maxYear = EnergyDataUsage::selectRaw('MAX(CAST(SUBSTRING(month, 1, 4) AS INTEGER)) as max_year')
            ->value('max_year')
            ?? EnergyResourceUsage::selectRaw('MAX(CAST(SUBSTRING(month, 1, 4) AS INTEGER)) as max_year')
                ->value('max_year')
            ?? MonthlyProductionUsage::selectRaw('MAX(CAST(SUBSTRING(month, 1, 4) AS INTEGER)) as max_year')
                ->value('max_year')
            ?? now()->year;
        $years = range($minYear, max($maxYear, now()->year));

        return view('admin.sec-analysis.index', compact(
            'energyTypes', 'products', 'years', 'energySources', 'resourceSources'
        ));
    }

    public function getMatrixData(Request $request)
    {
        $request->validate([
            'year_start' => 'required|integer',
            'year_end' => 'required|integer|gte:year_start',
            'poe_category' => 'required|string',
        ]);

        $yearStart = (int) $request->year_start;
        $yearEnd = (int) $request->year_end;
        $poeCategory = $request->poe_category;

        $products = Product::where('is_active', true)->orderBy('id')->get();
        $energySources = EnergyData::orderBy('id')->get();
        $resourceSources = EnergyResourceData::orderBy('id')->get();

        $energySourceIds = $energySources->pluck('id')->toArray();
        $resourceSourceIds = $resourceSources->pluck('id')->toArray();
        $productIds = $products->pluck('id')->toArray();

        $result = [];

        for ($year = $yearStart; $year <= $yearEnd; $year++) {
            $yearData = [
                'year' => $year,
                'months' => [],
            ];

            // Load all data for this year in bulk from usage tables
            // Query energy_data_usages instead of sec_energy_consumptions
            $energyData = EnergyDataUsage::where('month', 'like', $year . '-%')
                ->whereIn('energy_data_id', $energySourceIds)
                ->get()
                ->map(function($item) {
                    // Extract month number from '2026-01' format
                    $item->month = (int) substr($item->month, -2);
                    $item->value_gj = $item->usage_gj; // Map field name for compatibility
                    return $item;
                })
                ->groupBy('month');

            // Query energy_resource_usages instead of sec_resource_consumptions
            $resourceData = EnergyResourceUsage::where('month', 'like', $year . '-%')
                ->whereIn('energy_resource_data_id', $resourceSourceIds)
                ->get()
                ->map(function($item) {
                    $item->month = (int) substr($item->month, -2);
                    $item->value_gj = $item->usage_gj;
                    return $item;
                })
                ->groupBy('month');

            // Query monthly_production_usages instead of sec_production_values
            $productionData = MonthlyProductionUsage::where('month', 'like', $year . '-%')
                ->with('monthlyProduction')
                ->get()
                ->map(function($item) use ($products) {
                    $item->month = (int) substr($item->month, -2);
                    $item->quantity = $item->production_amount; // Map field name

                    // Map monthly_production to product via production_type name
                    $product = $products->firstWhere('name', $item->monthlyProduction->production_type);
                    $item->product_id = $product ? $product->id : null;

                    return $item;
                })
                ->filter(function($item) {
                    return $item->product_id !== null; // Only include matched products
                })
                ->groupBy('month');

            $poeAllocations = SecPoeAllocation::where('year', $year)
                ->where('poe_category', $poeCategory)
                ->whereIn('product_id', $productIds)
                ->get()
                ->keyBy('product_id');

            // Accumulators for yearly totals
            $yearlyEnergyBySource = array_fill_keys($energySourceIds, 0);
            $yearlyResourceBySource = array_fill_keys($resourceSourceIds, 0);
            $yearlyTotalEnergy = 0;
            $yearlyTotalResource = 0;
            $yearlyTotalCombined = 0;
            $yearlyProductionByProduct = array_fill_keys($productIds, 0);
            $yearlyTotalProduction = 0;

            for ($month = 1; $month <= 12; $month++) {
                $monthEnergy = $energyData->get($month, collect())->keyBy('energy_data_id');
                $monthResource = $resourceData->get($month, collect())->keyBy('energy_resource_data_id');
                $monthProduction = $productionData->get($month, collect())->keyBy('product_id');

                // Energy columns (per source)
                $energyCols = [];
                $totalEnergy = 0;
                foreach ($energySourceIds as $esId) {
                    $val = (float) ($monthEnergy->get($esId)->value_gj ?? 0);
                    $energyCols[$esId] = $val;
                    $totalEnergy += $val;
                    $yearlyEnergyBySource[$esId] += $val;
                }

                // Resource columns (per source)
                $resourceCols = [];
                $totalResource = 0;
                foreach ($resourceSourceIds as $rsId) {
                    $val = (float) ($monthResource->get($rsId)->value_gj ?? 0);
                    $resourceCols[$rsId] = $val;
                    $totalResource += $val;
                    $yearlyResourceBySource[$rsId] += $val;
                }

                $totalCombined = $totalEnergy + $totalResource;
                $yearlyTotalEnergy += $totalEnergy;
                $yearlyTotalResource += $totalResource;
                $yearlyTotalCombined += $totalCombined;

                // Production columns (per product)
                $prodCols = [];
                $totalProduction = 0;
                foreach ($productIds as $pid) {
                    $val = (float) ($monthProduction->get($pid)->quantity ?? 0);
                    $prodCols[$pid] = $val;
                    $totalProduction += $val;
                    $yearlyProductionByProduct[$pid] += $val;
                }
                $yearlyTotalProduction += $totalProduction;

                // SEC per product: SEC = (TotalCombinedEnergy * POE/100) / Production
                $secCols = [];
                foreach ($productIds as $pid) {
                    $poe = (float) ($poeAllocations->get($pid)->percentage ?? 0);
                    $prodQty = $prodCols[$pid];
                    if ($prodQty > 0 && $poe > 0) {
                        $secCols[$pid] = round(($totalCombined * $poe / 100) / $prodQty, 4);
                    } else {
                        $secCols[$pid] = 0;
                    }
                }
                $secTotal = $totalProduction > 0 ? round($totalCombined / $totalProduction, 4) : 0;

                $yearData['months'][] = [
                    'month' => $month,
                    'energy' => $energyCols,
                    'total_energy' => round($totalEnergy, 4),
                    'resource' => $resourceCols,
                    'total_resource' => round($totalResource, 4),
                    'total_combined' => round($totalCombined, 4),
                    'production' => $prodCols,
                    'total_production' => round($totalProduction, 4),
                    'sec' => $secCols,
                    'sec_total' => $secTotal,
                ];
            }

            // Yearly SEC totals
            $yearlySecByProduct = [];
            foreach ($productIds as $pid) {
                $poe = (float) ($poeAllocations->get($pid)->percentage ?? 0);
                $sumProd = $yearlyProductionByProduct[$pid];
                if ($sumProd > 0 && $poe > 0) {
                    $yearlySecByProduct[$pid] = round(($yearlyTotalCombined * $poe / 100) / $sumProd, 4);
                } else {
                    $yearlySecByProduct[$pid] = 0;
                }
            }
            $yearlySecTotal = $yearlyTotalProduction > 0
                ? round($yearlyTotalCombined / $yearlyTotalProduction, 4)
                : 0;

            $yearData['yearly_totals'] = [
                'energy' => $yearlyEnergyBySource,
                'total_energy' => round($yearlyTotalEnergy, 4),
                'resource' => $yearlyResourceBySource,
                'total_resource' => round($yearlyTotalResource, 4),
                'total_combined' => round($yearlyTotalCombined, 4),
                'production' => $yearlyProductionByProduct,
                'total_production' => round($yearlyTotalProduction, 4),
            ];
            $yearData['yearly_sec'] = $yearlySecByProduct;
            $yearData['yearly_sec_total'] = $yearlySecTotal;

            $result[] = $yearData;
        }

        // Build labels for the frontend
        $energySourceNames = [];
        foreach ($energySources as $es) {
            $energySourceNames[$es->id] = $es->energy_type;
        }
        $resourceSourceNames = [];
        foreach ($resourceSources as $rs) {
            $resourceSourceNames[$rs->id] = $rs->resource_type;
        }
        $productNames = [];
        foreach ($products as $p) {
            $productNames[$p->id] = $p->name;
        }

        // SEC Total table: one row per product, one column per year
        $secTotalTable = [];
        foreach ($products as $product) {
            $row = ['product_id' => $product->id, 'product_name' => $product->name, 'years' => []];
            foreach ($result as $yearData) {
                $row['years'][$yearData['year']] = $yearData['yearly_sec'][$product->id] ?? 0;
            }
            $secTotalTable[] = $row;
        }
        $totalRow = ['product_id' => null, 'product_name' => 'Total Production Outputs', 'years' => []];
        foreach ($result as $yearData) {
            $totalRow['years'][$yearData['year']] = $yearData['yearly_sec_total'];
        }
        $secTotalTable[] = $totalRow;

        return response()->json([
            'success' => true,
            'data' => $result,
            'energy_source_names' => $energySourceNames,
            'resource_source_names' => $resourceSourceNames,
            'product_names' => $productNames,
            'sec_total_table' => $secTotalTable,
        ]);
    }

    public function storePoe(Request $request)
    {
        $request->validate([
            'year' => 'required|integer',
            'poe_category' => 'required|string',
            'allocations' => 'required|array',
            'allocations.*.product_id' => 'required|exists:products,id',
            'allocations.*.percentage' => 'required|numeric|min:0|max:100',
        ]);

        $total = collect($request->allocations)->sum('percentage');
        if (abs($total - 100) > 0.01) {
            return response()->json([
                'success' => false,
                'message' => 'POE percentages must sum to exactly 100%. Current total: ' . $total . '%'
            ], 422);
        }

        foreach ($request->allocations as $alloc) {
            SecPoeAllocation::updateOrCreate(
                [
                    'product_id' => $alloc['product_id'],
                    'year' => $request->year,
                    'poe_category' => $request->poe_category,
                ],
                ['percentage' => $alloc['percentage']]
            );
        }

        return response()->json(['success' => true, 'message' => 'POE allocations saved successfully.']);
    }

    public function storeEnergyData(Request $request)
    {
        $request->validate([
            'year' => 'required|integer',
            'entries' => 'required|array',
            'entries.*.energy_data_id' => 'required|exists:energy_data,id',
            'entries.*.month' => 'required|integer|between:1,12',
            'entries.*.value_gj' => 'required|numeric|min:0',
        ]);

        foreach ($request->entries as $entry) {
            $monthKey = $request->year . '-' . str_pad($entry['month'], 2, '0', STR_PAD_LEFT);

            EnergyDataUsage::updateOrCreate(
                [
                    'energy_data_id' => $entry['energy_data_id'],
                    'month' => $monthKey,
                ],
                [
                    'usage_value' => $entry['value_gj'],
                    'usage_unit' => 'GJ',
                    'usage_gj' => $entry['value_gj'],
                ]
            );
        }

        return response()->json(['success' => true, 'message' => 'Energy data saved successfully.']);
    }

    public function storeProductionData(Request $request)
    {
        $request->validate([
            'year' => 'required|integer',
            'entries' => 'required|array',
            'entries.*.product_id' => 'required|exists:products,id',
            'entries.*.month' => 'required|integer|between:1,12',
            'entries.*.quantity' => 'required|numeric|min:0',
        ]);

        foreach ($request->entries as $entry) {
            $product = Product::find($entry['product_id']);
            $monthKey = $request->year . '-' . str_pad($entry['month'], 2, '0', STR_PAD_LEFT);

            // Find or create the monthly_production record
            $monthlyProduction = MonthlyProduction::firstOrCreate(
                ['production_type' => $product->name]
            );

            MonthlyProductionUsage::updateOrCreate(
                [
                    'monthly_production_id' => $monthlyProduction->id,
                    'month' => $monthKey,
                ],
                [
                    'production_amount' => $entry['quantity'],
                    'production_unit' => 'Tonne',
                ]
            );
        }

        return response()->json(['success' => true, 'message' => 'Production data saved successfully.']);
    }

    /**
     * Store monthly POE allocations.
     * Supports both yearly (applies same % to all months) and monthly (per-month %) input.
     */
    public function storeMonthlyPoe(Request $request)
    {
        $request->validate([
            'poe_category' => 'required|string',
            'mode' => 'required|in:yearly,monthly',
            'year_start' => 'required|integer',
            'year_end' => 'required|integer|gte:year_start',
            'allocations' => 'required|array',
        ]);

        $category = $request->poe_category;
        $yearStart = (int) $request->year_start;
        $yearEnd = (int) $request->year_end;

        if ($request->mode === 'yearly') {
            // Yearly mode: each allocation has product_id and percentage (apply to all months in range)
            foreach ($request->allocations as $alloc) {
                $pct = (float) ($alloc['percentage'] ?? 0);
                for ($y = $yearStart; $y <= $yearEnd; $y++) {
                    // Save to yearly table too
                    SecPoeAllocation::updateOrCreate(
                        ['product_id' => $alloc['product_id'], 'year' => $y, 'poe_category' => $category],
                        ['percentage' => $pct]
                    );
                    // Save to monthly table (same % for all 12 months)
                    for ($m = 1; $m <= 12; $m++) {
                        $monthKey = $y . '-' . str_pad($m, 2, '0', STR_PAD_LEFT);
                        SecMonthlyPoe::updateOrCreate(
                            ['product_id' => $alloc['product_id'], 'month' => $monthKey, 'poe_category' => $category],
                            ['percentage' => $pct, 'created_by' => auth()->id()]
                        );
                    }
                }
            }
        } else {
            // Monthly mode: allocations keyed by product_id, each with months array
            foreach ($request->allocations as $alloc) {
                $productId = $alloc['product_id'];
                foreach ($alloc['months'] as $monthKey => $pct) {
                    SecMonthlyPoe::updateOrCreate(
                        ['product_id' => $productId, 'month' => $monthKey, 'poe_category' => $category],
                        ['percentage' => (float) $pct, 'created_by' => auth()->id()]
                    );
                }
                // Also update yearly averages
                for ($y = $yearStart; $y <= $yearEnd; $y++) {
                    $yearlyAvg = SecMonthlyPoe::where('product_id', $productId)
                        ->where('poe_category', $category)
                        ->where('month', 'like', $y . '-%')
                        ->avg('percentage') ?? 0;
                    SecPoeAllocation::updateOrCreate(
                        ['product_id' => $productId, 'year' => $y, 'poe_category' => $category],
                        ['percentage' => round($yearlyAvg, 4)]
                    );
                }
            }
        }

        return response()->json(['success' => true, 'message' => 'Monthly POE saved successfully.']);
    }

    /**
     * Get monthly POE allocations for a date range.
     */
    public function getMonthlyPoe(Request $request)
    {
        $request->validate([
            'poe_category' => 'required|string',
            'year_start' => 'required|integer',
            'year_end' => 'required|integer|gte:year_start',
        ]);

        $startMonth = $request->year_start . '-01';
        $endMonth = $request->year_end . '-12';

        $allocations = SecMonthlyPoe::where('poe_category', $request->poe_category)
            ->whereBetween('month', [$startMonth, $endMonth])
            ->orderBy('product_id')
            ->orderBy('month')
            ->get()
            ->groupBy('product_id')
            ->map(function ($items) {
                return $items->keyBy('month')->map(function ($item) {
                    return $item->percentage;
                });
            });

        // Also get yearly POE
        $yearly = SecPoeAllocation::where('poe_category', $request->poe_category)
            ->whereBetween('year', [$request->year_start, $request->year_end])
            ->get()
            ->groupBy('product_id')
            ->map(function ($items) {
                return $items->keyBy('year')->map(function ($item) {
                    return $item->percentage;
                });
            });

        return response()->json([
            'success' => true,
            'monthly' => $allocations,
            'yearly' => $yearly,
        ]);
    }

    public function store(Request $request)
    {
        return response()->json(['success' => false, 'message' => 'Not implemented.'], 501);
    }

    public function show($id)
    {
        return response()->json(['success' => false, 'message' => 'Not implemented.'], 501);
    }

    public function update(Request $request, $id)
    {
        return response()->json(['success' => false, 'message' => 'Not implemented.'], 501);
    }

    public function destroy($id)
    {
        return response()->json(['success' => false, 'message' => 'Not implemented.'], 501);
    }
}
