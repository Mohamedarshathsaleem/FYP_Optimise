<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SeuCriteria;
use App\Models\SeuFlagging;
use App\Models\SeuActionItem;
use App\Models\LoadApportioning;
use App\Models\EnergyData;
use App\Models\EnergyResourceData;
use App\Models\EnergyDataUsage;
use App\Models\EnergyResourceUsage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SeuFlaggingController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->hasPermission('seu-flagging.view')) {
            abort(403, 'Unauthorized');
        }

        $years = $this->getAvailableYears();
        $selectedYear = $request->get('year', date('Y'));

        // Get or create criteria for this year
        $criteria = SeuCriteria::getOrCreateForYear($selectedYear);

        // Auto-refresh SEU data from Load Apportioning
        $this->refreshSeuData($selectedYear, $criteria);

        // Get ALL SEU lists (not just flagged) for display
        $energySeus = SeuFlagging::byYear($selectedYear)
            ->energySeus()
            ->orderByDesc('overall_usage_pct')
            ->get();

        $resourceSeus = SeuFlagging::byYear($selectedYear)
            ->energyResourceSeus()
            ->orderByDesc('overall_usage_pct')
            ->get();

        // Build energy type name lookup from LoadApportioning seu_category
        // seu_category stores "edata_X" or "rdata_X" which maps to EnergyData/EnergyResourceData
        $energyTypeNames = $this->buildEnergyTypeNameMap($selectedYear);

        // Attach energy_type_name to each SEU record
        $allSeus = $energySeus->merge($resourceSeus);
        $laIds = $allSeus->pluck('load_apportioning_id')->filter()->unique();
        $laMap = LoadApportioning::whereIn('id', $laIds)->pluck('seu_category', 'id');

        foreach ($energySeus as $seu) {
            $category = $laMap->get($seu->load_apportioning_id, '');
            $seu->energy_type_name = $energyTypeNames[$category] ?? $category;
        }
        foreach ($resourceSeus as $seu) {
            $category = $laMap->get($seu->load_apportioning_id, '');
            $seu->energy_type_name = $energyTypeNames[$category] ?? $category;
        }

        return view('admin.seu-flagging.index', compact(
            'years',
            'selectedYear',
            'criteria',
            'energySeus',
            'resourceSeus'
        ));
    }

    /**
     * Auto-refresh SEU data from Load Apportioning equipment names
     */
    protected function refreshSeuData($year, $criteria)
    {
        DB::beginTransaction();
        try {
            // Delete existing non-overridden SEUs for this year
            SeuFlagging::where('year', $year)
                ->where('is_manually_overridden', false)
                ->delete();

            // Get Load Apportioning data by individual equipment_name
            $loadData = LoadApportioning::where('year', $year)
                ->whereNotNull('equipment_name')
                ->where('equipment_name', '!=', '')
                ->get();

            // Calculate grand totals
            $grandTotalElectricity = $loadData->sum('electricity_load_gj');
            $grandTotalNg = $loadData->sum('ng_load_gj');

            $sortOrder = 0;

            foreach ($loadData as $item) {
                $equipmentName = $item->equipment_name;

                // Process Energy (Electricity) SEUs
                if ($item->electricity_load_gj > 0) {
                    $pct = $grandTotalElectricity > 0
                        ? ($item->electricity_load_gj / $grandTotalElectricity)
                        : 0;

                    $isFlagged = $pct >= $criteria->lower_limit && $pct <= $criteria->upper_limit;

                    // Check if manually overridden entry exists
                    $existing = SeuFlagging::where('year', $year)
                        ->where('seu_name', $equipmentName)
                        ->where('energy_type', 'energy')
                        ->where('is_manually_overridden', true)
                        ->first();

                    if (!$existing) {
                        SeuFlagging::create([
                            'year' => $year,
                            'criteria_id' => $criteria->id,
                            'seu_name' => $equipmentName,
                            'energy_type' => 'energy',
                            'current_gj' => $item->electricity_load_gj,
                            'overall_usage_pct' => $pct,
                            'enpi_reference' => 'GJ/tonne of production',
                            'is_flagged' => $isFlagged,
                            'load_apportioning_id' => $item->id,
                            'sort_order' => $sortOrder++,
                            'created_by' => auth()->id(),
                        ]);
                    } else {
                        $existing->update([
                            'current_gj' => $item->electricity_load_gj,
                            'overall_usage_pct' => $pct,
                            'load_apportioning_id' => $item->id,
                            'updated_by' => auth()->id(),
                        ]);
                    }
                }

                // Process Energy Resource (NG) SEUs
                if ($item->ng_load_gj > 0) {
                    $pct = $grandTotalNg > 0
                        ? ($item->ng_load_gj / $grandTotalNg)
                        : 0;

                    $isFlagged = $pct >= $criteria->lower_limit && $pct <= $criteria->upper_limit;

                    $existing = SeuFlagging::where('year', $year)
                        ->where('seu_name', $equipmentName)
                        ->where('energy_type', 'energy_resource')
                        ->where('is_manually_overridden', true)
                        ->first();

                    if (!$existing) {
                        SeuFlagging::create([
                            'year' => $year,
                            'criteria_id' => $criteria->id,
                            'seu_name' => $equipmentName,
                            'energy_type' => 'energy_resource',
                            'current_gj' => $item->ng_load_gj,
                            'overall_usage_pct' => $pct,
                            'load_apportioning_id' => $item->id,
                            'is_flagged' => $isFlagged,
                            'sort_order' => $sortOrder++,
                            'created_by' => auth()->id(),
                        ]);
                    } else {
                        $existing->update([
                            'current_gj' => $item->ng_load_gj,
                            'overall_usage_pct' => $pct,
                            'load_apportioning_id' => $item->id,
                            'updated_by' => auth()->id(),
                        ]);
                    }
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('SEU refresh failed: ' . $e->getMessage());
        }
    }

    /**
     * Update SEU criteria (thresholds)
     */
    public function updateCriteria(Request $request)
    {
        if (!auth()->user()->hasPermission('seu-flagging.edit')) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'year' => 'required|integer|min:2000|max:2100',
            'criteria_type' => 'required|string|in:load_percentage,absolute_gj,custom',
            'upper_limit' => 'required|numeric|min:0|max:1',
            'lower_limit' => 'required|numeric|min:0|max:1|lte:upper_limit',
            'notes' => 'nullable|string|max:1000',
        ]);

        $criteria = SeuCriteria::updateOrCreate(
            [
                'year' => $validated['year'],
                'criteria_type' => $validated['criteria_type'],
            ],
            [
                'upper_limit' => $validated['upper_limit'],
                'lower_limit' => $validated['lower_limit'],
                'notes' => $validated['notes'] ?? null,
                'updated_by' => auth()->id(),
            ]
        );

        // Re-apply flagging with new criteria
        $year = $validated['year'];
        $seus = SeuFlagging::where('year', $year)
            ->where('is_manually_overridden', false)
            ->get();

        foreach ($seus as $seu) {
            $isFlagged = $seu->overall_usage_pct >= $criteria->lower_limit
                      && $seu->overall_usage_pct <= $criteria->upper_limit;
            $seu->update(['is_flagged' => $isFlagged, 'criteria_id' => $criteria->id]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Criteria updated successfully',
            'data' => $criteria,
        ]);
    }

    /**
     * Generate SEU list (kept for backward compatibility, now delegates to refreshSeuData)
     */
    public function generate(Request $request)
    {
        if (!auth()->user()->hasPermission('seu-flagging.edit')) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'year' => 'required|integer|min:2000|max:2100',
        ]);

        $year = $validated['year'];
        $criteria = SeuCriteria::getOrCreateForYear($year);

        $this->refreshSeuData($year, $criteria);

        return response()->json([
            'status' => 'success',
            'message' => 'SEU list generated successfully',
            'counts' => [
                'energy' => SeuFlagging::byYear($year)->energySeus()->flagged()->count(),
                'resource' => SeuFlagging::byYear($year)->energyResourceSeus()->flagged()->count(),
            ],
        ]);
    }

    /**
     * Toggle SEU flag status
     */
    public function toggleFlag(Request $request, $id)
    {
        if (!auth()->user()->hasPermission('seu-flagging.edit')) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $seu = SeuFlagging::findOrFail($id);
        $seu->toggleFlag($validated['reason'] ?? null);

        return response()->json([
            'status' => 'success',
            'message' => 'SEU flag status updated',
            'data' => $seu,
        ]);
    }

    /**
     * Get SEU data for AJAX table/chart rendering
     */
    public function getData(Request $request)
    {
        $year = $request->get('year', date('Y'));
        $type = $request->get('type'); // 'energy' or 'energy_resource'

        $query = SeuFlagging::byYear($year)
            ->orderByDesc('overall_usage_pct');

        if ($type) {
            $query->byEnergyType($type);
        }

        $flaggedOnly = $request->boolean('flagged_only', false);
        if ($flaggedOnly) {
            $query->flagged();
        }

        $data = $query->get();

        return response()->json([
            'status' => 'success',
            'data' => $data,
            'year' => $year,
            'totals' => [
                'energy_flagged' => SeuFlagging::byYear($year)->energySeus()->flagged()->count(),
                'resource_flagged' => SeuFlagging::byYear($year)->energyResourceSeus()->flagged()->count(),
                'total_energy_gj' => SeuFlagging::byYear($year)->energySeus()->sum('current_gj'),
                'total_resource_gj' => SeuFlagging::byYear($year)->energyResourceSeus()->sum('current_gj'),
            ],
        ]);
    }

    /**
     * Get chart data for SEU Flagging graphs
     */
    public function getChartData(Request $request)
    {
        $year = $request->get('year', date('Y'));

        $energySeus = SeuFlagging::byYear($year)
            ->energySeus()
            ->orderByDesc('current_gj')
            ->get()
            ->map(function ($seu) {
                return [
                    'id' => $seu->id,
                    'name' => $seu->seu_name,
                    'gj' => round($seu->current_gj, 2),
                    'percentage' => round($seu->overall_usage_pct * 100, 2),
                    'is_flagged' => $seu->is_flagged,
                    'is_manual' => $seu->is_manually_overridden,
                ];
            });

        $resourceSeus = SeuFlagging::byYear($year)
            ->energyResourceSeus()
            ->orderByDesc('current_gj')
            ->get()
            ->map(function ($seu) {
                return [
                    'id' => $seu->id,
                    'name' => $seu->seu_name,
                    'gj' => round($seu->current_gj, 2),
                    'percentage' => round($seu->overall_usage_pct * 100, 2),
                    'is_flagged' => $seu->is_flagged,
                    'is_manual' => $seu->is_manually_overridden,
                ];
            });

        return response()->json([
            'status' => 'success',
            'energy_seus' => $energySeus->values(),
            'resource_seus' => $resourceSeus->values(),
            'totals' => [
                'energy_gj' => round($energySeus->sum('gj'), 2),
                'resource_gj' => round($resourceSeus->sum('gj'), 2),
                'energy_count' => $energySeus->count(),
                'resource_count' => $resourceSeus->count(),
                'energy_flagged' => $energySeus->where('is_flagged', true)->count(),
                'resource_flagged' => $resourceSeus->where('is_flagged', true)->count(),
            ],
            'year' => $year,
        ]);
    }

    /**
     * Export SEU list to CSV
     */
    public function export(Request $request)
    {
        if (!auth()->user()->hasPermission('seu-flagging.view')) {
            abort(403, 'Unauthorized');
        }

        $year = $request->get('year', date('Y'));

        $energySeus = SeuFlagging::byYear($year)->energySeus()->orderByDesc('overall_usage_pct')->get();
        $resourceSeus = SeuFlagging::byYear($year)->energyResourceSeus()->orderByDesc('overall_usage_pct')->get();

        $exportData = [];

        $exportData[] = ['ENERGY SEUs (Electricity)'];
        $exportData[] = ['#', 'Name of SEU', 'Current GJ', '% of Overall Usage', 'Flagged', 'Override'];
        foreach ($energySeus as $index => $seu) {
            $exportData[] = [
                $index + 1,
                $seu->seu_name,
                number_format($seu->current_gj, 2),
                number_format($seu->overall_usage_pct * 100, 2) . '%',
                $seu->is_flagged ? 'SEU' : 'Not SEU',
                $seu->is_manually_overridden ? 'Manual' : 'Auto',
            ];
        }

        $exportData[] = [];

        $exportData[] = ['ENERGY RESOURCE SEUs (Natural Gas)'];
        $exportData[] = ['#', 'Name of SEU', 'Current GJ', '% of Overall Usage', 'Flagged', 'Override'];
        foreach ($resourceSeus as $index => $seu) {
            $exportData[] = [
                $index + 1,
                $seu->seu_name,
                number_format($seu->current_gj, 2),
                number_format($seu->overall_usage_pct * 100, 2) . '%',
                $seu->is_flagged ? 'SEU' : 'Not SEU',
                $seu->is_manually_overridden ? 'Manual' : 'Auto',
            ];
        }

        $filename = "SEU_Flagging_{$year}_" . date('Ymd_His') . '.csv';
        $handle = fopen('php://temp', 'r+');

        foreach ($exportData as $row) {
            fputcsv($handle, $row);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    protected function getAvailableYears()
    {
        $currentYear = (int) date('Y');
        $rangeYears = collect(range(2020, $currentYear));

        $seuYears = SeuFlagging::distinct()->pluck('year');
        $loadYears = LoadApportioning::distinct()->pluck('year');

        return $rangeYears->merge($seuYears)->merge($loadYears)
            ->unique()->sort()->values();
    }

    /**
     * Build a mapping of seu_category values (edata_X, rdata_X) to human-readable energy type names.
     */
    protected function buildEnergyTypeNameMap($year)
    {
        $map = [];

        // Map edata_X → EnergyData.energy_type
        $energySources = EnergyData::orderBy('id')->get();
        foreach ($energySources as $es) {
            $map['edata_' . $es->id] = $es->energy_type;
        }

        // Map rdata_X → EnergyResourceData.resource_type
        $resourceSources = EnergyResourceData::orderBy('id')->get();
        foreach ($resourceSources as $rs) {
            $map['rdata_' . $rs->id] = $rs->resource_type;
        }

        return $map;
    }
}
