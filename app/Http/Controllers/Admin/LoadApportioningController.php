<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoadApportioning;
use App\Models\LoadApportioningApproach;
use App\Models\EnergyData;
use App\Models\EnergyResourceData;
use App\Models\EnergyDataUsage;
use App\Models\EnergyResourceUsage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoadApportioningController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->hasPermission('load-apportioning.view')) {
            abort(403, 'Unauthorized');
        }

        $years = $this->getAvailableYears();
        $selectedYear = $request->get('year', date('Y'));
        $approaches = LoadApportioningApproach::orderBy('name')->get();
        $selectedApproach = $request->get('approach_id', $approaches->first()?->id);

        $energySources = EnergyData::orderBy('id')->get();
        $resourceSources = EnergyResourceData::orderBy('id')->get();

        return view('admin.load-apportioning.index', compact(
            'years',
            'selectedYear',
            'approaches',
            'selectedApproach',
            'energySources',
            'resourceSources'
        ));
    }

    /**
     * Get equipment counts per energy type for a given year + approach.
     */
    public function getEquipmentCounts(Request $request)
    {
        $year = $request->get('year', date('Y'));
        $approachId = $request->get('approach_id');

        $query = LoadApportioning::where('year', $year);
        if ($approachId) {
            $query->where('approach_id', $approachId);
        }

        return response()->json(
            $query->selectRaw('seu_category, COUNT(*) as count')
                ->groupBy('seu_category')
                ->pluck('count', 'seu_category')
                ->toArray()
        );
    }

    /**
     * Get load apportioning data formatted for blade view compatibility.
     *
     * The blade JS expects data keyed by the selected energy_type_id with rows having:
     * row_label, energy_consumption_gj, load_percentage, sort_order
     *
     * Energy type IDs from the blade modal can be:
     * - Numeric (from energy_types table) → show electricity data
     * - "edata_X" (from energy_data table) → show electricity data
     * - "rdata_X" (from energy_resource_data table) → show NG data
     */
    public function getData(Request $request)
    {
        $year = $request->get('year', date('Y'));
        $approachId = $request->get('approach_id');
        $energyTypeIds = $request->get('energy_type_ids', []);

        $query = LoadApportioning::where('year', $year)
            ->orderBy('seu_category')
            ->orderBy('sort_order');

        if ($approachId) {
            $query->where('approach_id', $approachId);
        }

        $data = $query->get();

        // Build response grouped by energy_type_id for blade view compatibility
        // Each row's seu_category stores which energy type it belongs to
        $grouped = [];
        if (!empty($energyTypeIds)) {
            foreach ($energyTypeIds as $etId) {
                $isResource = is_string($etId) && str_starts_with($etId, 'rdata_');

                // Filter rows that belong to this specific energy type
                $rows = $data->filter(fn($row) => $row->seu_category === (string) $etId);

                if ($rows->isEmpty()) {
                    // Fallback for legacy data without seu_category: use old behavior
                    if ($isResource) {
                        $rows = $data->filter(fn($row) => $row->ng_load_gj > 0 && empty($row->seu_category));
                    } else {
                        $rows = $data->filter(fn($row) => $row->electricity_load_gj > 0 && empty($row->seu_category));
                    }
                }

                // Calculate total per energy type (not global) so percentages sum to 100% per type
                if ($isResource) {
                    $total = $rows->sum('ng_load_gj');
                    $grouped[$etId] = $rows->map(function ($row) use ($total) {
                        $pct = $total > 0 ? ($row->ng_load_gj / $total) * 100 : 0;
                        return [
                            'id' => $row->id,
                            'row_label' => $row->equipment_name ?? $row->seu_category ?? '',
                            'energy_consumption_gj' => $row->ng_load_gj,
                            'load_percentage' => round($pct, 2),
                            'sort_order' => $row->sort_order,
                            'seu_category' => $row->seu_category,
                            'submeter_reference' => $row->submeter_reference,
                            'equipment_type' => $row->equipment_type,
                            'equipment_name' => $row->equipment_name,
                        ];
                    })->values()->toArray();
                } else {
                    $total = $rows->sum('electricity_load_gj');
                    $grouped[$etId] = $rows->map(function ($row) use ($total) {
                        $pct = $total > 0 ? ($row->electricity_load_gj / $total) * 100 : 0;
                        return [
                            'id' => $row->id,
                            'row_label' => $row->equipment_name ?? $row->seu_category ?? '',
                            'energy_consumption_gj' => $row->electricity_load_gj,
                            'load_percentage' => round($pct, 2),
                            'sort_order' => $row->sort_order,
                            'seu_category' => $row->seu_category,
                            'submeter_reference' => $row->submeter_reference,
                            'equipment_type' => $row->equipment_type,
                            'equipment_name' => $row->equipment_name,
                        ];
                    })->values()->toArray();
                }
            }
        }

        return response()->json([
            'success' => true,
            'data' => $grouped,
            'totals' => [
                'electricity_gj' => $data->sum('electricity_load_gj'),
                'ng_gj' => $data->sum('ng_load_gj'),
                'total_gj' => $data->sum('total_energy_gj'),
            ],
            'year' => $year,
        ]);
    }

    /**
     * Save load apportioning data (bulk save).
     *
     * Accepts two formats:
     * - New format: { rows: [{seu_category, equipment_name, ...}] }
     * - Blade format: { tables: { etId: [{row_label, energy_consumption_gj, load_percentage}] } }
     */
    public function save(Request $request)
    {
        if (!auth()->user()->hasPermission('load-apportioning.edit')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $year = $request->input('year');
        $approachId = $request->input('approach_id');

        if (!$year || !$approachId) {
            return response()->json(['success' => false, 'message' => 'Year and approach are required.'], 422);
        }

        // Check if approach exists
        $approach = LoadApportioningApproach::find($approachId);
        if (!$approach) {
            return response()->json(['success' => false, 'message' => 'Invalid approach.'], 422);
        }

        DB::beginTransaction();
        try {
            // Determine format: blade sends "tables", new format sends "rows"
            $tables = $request->input('tables');
            $rows = $request->input('rows');

            if ($tables && is_array($tables)) {
                // Blade format: tables keyed by energy type ID
                // Merge all tables into flat rows, using row_label as equipment_name
                $this->saveFromBladeFormat($year, $approachId, $tables, $request->input('unit_mode', 'energy_gj'));
            } elseif ($rows && is_array($rows)) {
                // New format: flat array of rows with full SEU fields
                $this->saveFromNewFormat($year, $approachId, $rows);
            } else {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'No data provided.'], 422);
            }

            // Recalculate percentages
            $this->recalculatePercentages($year, $approachId);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Load apportioning data saved successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to save data: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Save from blade JS format: {tables: {etId: [{row_label, energy_consumption_gj, load_percentage, sort_order}]}}
     *
     * Each etId key represents the energy type the equipment belongs to.
     * Equipment rows are stored per energy type — they are NOT merged across types.
     */
    protected function saveFromBladeFormat(int $year, int $approachId, array $tables, string $unitMode)
    {
        // Try to match existing rows to preserve SEU metadata
        $existingRows = LoadApportioning::where('year', $year)
            ->where('approach_id', $approachId)
            ->get()
            ->keyBy(function ($row) {
                return $row->seu_category . '::' . $row->equipment_name;
            });

        // Delete existing and re-insert
        LoadApportioning::where('year', $year)
            ->where('approach_id', $approachId)
            ->delete();

        $globalSort = 0;

        foreach ($tables as $etId => $tableRows) {
            $isResource = is_string($etId) && str_starts_with($etId, 'rdata_');

            foreach ($tableRows as $row) {
                $label = $row['row_label'] ?? '';
                if (empty($label)) continue;

                $existingKey = $etId . '::' . $label;
                $existing = $existingRows->get($existingKey);

                LoadApportioning::create([
                    'year' => $year,
                    'approach_id' => $approachId,
                    'seu_category' => (string) $etId,
                    'submeter_reference' => $existing->submeter_reference ?? null,
                    'equipment_type' => $existing->equipment_type ?? null,
                    'equipment_name' => $label,
                    'equipment_remark' => $existing->equipment_remark ?? null,
                    'electricity_load_gj' => $isResource ? 0 : ($row['energy_consumption_gj'] ?? 0),
                    'ng_meter_reference' => $existing->ng_meter_reference ?? null,
                    'ng_load_gj' => $isResource ? ($row['energy_consumption_gj'] ?? 0) : 0,
                    'total_energy_gj' => ($row['energy_consumption_gj'] ?? 0),
                    'calculation_remark' => $existing->calculation_remark ?? null,
                    'sort_order' => $row['sort_order'] ?? $globalSort,
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                ]);

                $globalSort++;
            }
        }
    }

    /**
     * Save from new format: {rows: [{seu_category, equipment_name, electricity_load_gj, ng_load_gj, ...}]}
     */
    protected function saveFromNewFormat(int $year, int $approachId, array $rows)
    {
        LoadApportioning::where('year', $year)
            ->where('approach_id', $approachId)
            ->delete();

        $sortOrder = 0;
        foreach ($rows as $row) {
            $electricityGj = $row['electricity_load_gj'] ?? 0;
            $ngGj = $row['ng_load_gj'] ?? 0;

            LoadApportioning::create([
                'year' => $year,
                'approach_id' => $approachId,
                'seu_category' => $row['seu_category'] ?? null,
                'submeter_reference' => $row['submeter_reference'] ?? null,
                'equipment_type' => $row['equipment_type'] ?? null,
                'equipment_name' => $row['equipment_name'] ?? null,
                'equipment_remark' => $row['equipment_remark'] ?? null,
                'electricity_load_gj' => $electricityGj,
                'ng_meter_reference' => $row['ng_meter_reference'] ?? null,
                'ng_load_gj' => $ngGj,
                'total_energy_gj' => $electricityGj + $ngGj,
                'calculation_remark' => $row['calculation_remark'] ?? null,
                'sort_order' => $sortOrder++,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);
        }
    }

    /**
     * Recalculate all percentages for a year/approach
     */
    protected function recalculatePercentages($year, $approachId)
    {
        $rows = LoadApportioning::where('year', $year)
            ->where('approach_id', $approachId)
            ->get();

        // Group by seu_category so percentages sum to 100% per energy type
        $grouped = $rows->groupBy('seu_category');

        foreach ($grouped as $seuCategory => $categoryRows) {
            $totalElec = $categoryRows->sum('electricity_load_gj');
            $totalNg = $categoryRows->sum('ng_load_gj');
            $totalEnergy = $categoryRows->sum('total_energy_gj');

            foreach ($categoryRows as $row) {
                $row->electricity_load_pct = $totalElec > 0
                    ? $row->electricity_load_gj / $totalElec
                    : 0;
                $row->ng_load_pct = $totalNg > 0
                    ? $row->ng_load_gj / $totalNg
                    : 0;
                $row->total_energy_pct = $totalEnergy > 0
                    ? $row->total_energy_gj / $totalEnergy
                    : 0;
                $row->save();
            }
        }
    }

    /**
     * Store a new custom approach
     */
    public function storeApproach(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:load_apportioning_approaches,name',
        ]);

        $approach = LoadApportioningApproach::create([
            'name' => $request->name,
            'is_default' => false,
            'created_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'approach' => [
                'id' => $approach->id,
                'name' => $approach->name,
            ],
            'message' => 'Approach created successfully.',
        ]);
    }

    /**
     * Get SEU Summary (aggregated by SEU category)
     */
    public function getSeuSummary(Request $request)
    {
        $year = $request->get('year', date('Y'));

        $summary = LoadApportioning::where('year', $year)
            ->select('seu_category')
            ->selectRaw('SUM(electricity_load_gj) as total_electricity_gj')
            ->selectRaw('SUM(ng_load_gj) as total_ng_gj')
            ->selectRaw('SUM(total_energy_gj) as total_energy_gj')
            ->selectRaw('COUNT(*) as item_count')
            ->whereNotNull('seu_category')
            ->groupBy('seu_category')
            ->orderByDesc('total_energy_gj')
            ->get();

        $grandTotal = $summary->sum('total_energy_gj');

        foreach ($summary as $row) {
            $row->load_pct = $grandTotal > 0 ? ($row->total_energy_gj / $grandTotal) : 0;
        }

        return response()->json([
            'success' => true,
            'data' => $summary,
            'grand_total' => $grandTotal,
            'year' => $year,
        ]);
    }

    /**
     * Get Monthly NG/Resource Breakdown by meter.
     * Response formatted for the blade's buildMonthlyResourceTable() function.
     */
    public function getMonthlyNgBreakdown(Request $request)
    {
        $year = $request->get('year', date('Y'));

        // Get unique NG meters from load apportioning
        $ngMeterRefs = LoadApportioning::where('year', $year)
            ->whereNotNull('ng_meter_reference')
            ->distinct()
            ->pluck('ng_meter_reference');

        if ($ngMeterRefs->isEmpty()) {
            return response()->json([
                'success' => false,
                'meters' => [],
            ]);
        }

        // Build monthly data structure per meter
        $monthLabels = [];
        $meters = [];
        $monthlyTotals = array_fill(0, 12, 0);

        for ($m = 1; $m <= 12; $m++) {
            $monthLabels[] = date('M', mktime(0, 0, 0, $m, 1));
        }

        // Get NG load values from load apportioning grouped by meter
        foreach ($ngMeterRefs as $ref) {
            $meterRow = LoadApportioning::where('year', $year)
                ->where('ng_meter_reference', $ref)
                ->first();

            $annualGj = $meterRow ? $meterRow->ng_load_gj : 0;

            // Distribute evenly across months (since we only have annual data)
            $monthlyValues = array_fill(0, 12, round($annualGj / 12, 4));

            $meters[] = [
                'name' => $ref,
                'monthly' => $monthlyValues,
                'total' => $annualGj,
                'percentage' => 0, // calculated below
            ];
        }

        // Calculate grand total and percentages
        $grandTotal = collect($meters)->sum('total');

        foreach ($meters as &$meter) {
            $meter['percentage'] = $grandTotal > 0
                ? round(($meter['total'] / $grandTotal) * 100, 2)
                : 0;

            // Update monthly totals
            for ($m = 0; $m < 12; $m++) {
                $monthlyTotals[$m] += $meter['monthly'][$m];
            }
        }
        unset($meter);

        return response()->json([
            'success' => true,
            'meters' => $meters,
            'month_labels' => $monthLabels,
            'monthly_totals' => $monthlyTotals,
            'grand_total' => $grandTotal,
            'year' => $year,
        ]);
    }

    /**
     * Get available years from data
     */
    protected function getAvailableYears()
    {
        $loadYears = LoadApportioning::distinct()->pluck('year');
        $energyYears = EnergyDataUsage::selectRaw('DISTINCT SUBSTRING(month, 1, 4) as year')
            ->pluck('year')
            ->map(fn($y) => (int)$y);

        return $loadYears->merge($energyYears)->unique()->sort()->values();
    }
}
