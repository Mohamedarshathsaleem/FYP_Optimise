<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EnergyData;
use App\Models\EnergyResourceData;
use App\Models\EnergyType;
use App\Models\MonthlyVariable;
use App\Models\EnergyDataUsage;
use App\Models\EnergyResourceUsage;
use App\Models\MonthlyVariableUsage;
use App\Models\EipFilterPreset;
use App\Models\EipTarget;
use App\Models\EipCurrencyRate;
use App\Services\EipFilterService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EIPAnalysisController extends Controller
{
    protected EipFilterService $filterService;

    public function __construct(EipFilterService $filterService)
    {
        $this->filterService = $filterService;
    }

    public function index()
    {
        $energyTypes = EnergyType::all();
        $energySources = EnergyData::orderBy('id')->get();
        $resourceSources = EnergyResourceData::orderBy('id')->get();
        $variables = MonthlyVariable::orderBy('variable_name')->get();

        $minYear = EnergyDataUsage::selectRaw('MIN(YEAR(CONCAT(month, "-01"))) as min_year')
            ->value('min_year')
            ?? EnergyResourceUsage::selectRaw('MIN(YEAR(CONCAT(month, "-01"))) as min_year')
                ->value('min_year')
            ?? MonthlyVariableUsage::selectRaw('MIN(YEAR(CONCAT(month, "-01"))) as min_year')
                ->value('min_year')
            ?? now()->year - 3;

        $maxYear = EnergyDataUsage::selectRaw('MAX(YEAR(CONCAT(month, "-01"))) as max_year')
            ->value('max_year')
            ?? EnergyResourceUsage::selectRaw('MAX(YEAR(CONCAT(month, "-01"))) as max_year')
                ->value('max_year')
            ?? MonthlyVariableUsage::selectRaw('MAX(YEAR(CONCAT(month, "-01"))) as max_year')
                ->value('max_year')
            ?? now()->year;

        $years = range($minYear, max($maxYear, now()->year));

        // Load presets for the filter modal
        $systemPresets = EipFilterPreset::where('is_system', true)->get();
        $userPresets = auth()->check()
            ? EipFilterPreset::where('user_id', auth()->id())->get()
            : collect();

        // Load targets
        $targets = EipTarget::whereBetween('year', [$minYear, $maxYear])->get();

        return view('admin.eip-analysis.index', compact(
            'energyTypes', 'years', 'energySources', 'resourceSources', 'variables',
            'systemPresets', 'userPresets', 'targets'
        ));
    }

    public function getMatrixData(Request $request)
    {
        $request->validate([
            'year_start' => 'required|integer',
            'year_end' => 'required|integer|gte:year_start',
            'variable_type' => 'required|exists:monthly_variables,id',
        ]);

        $result = $this->filterService->loadMatrixData($request->all());
        return response()->json($result);
    }

    public function getFilterInsights(Request $request)
    {
        $request->validate([
            'year_start' => 'required|integer',
            'year_end' => 'required|integer|gte:year_start',
            'variable_type' => 'required|exists:monthly_variables,id',
        ]);

        $insights = $this->filterService->getQuickInsights($request->all());
        return response()->json($insights);
    }

    public function exportData(Request $request)
    {
        $request->validate([
            'year_start' => 'required|integer',
            'year_end' => 'required|integer|gte:year_start',
            'variable_type' => 'required|exists:monthly_variables,id',
        ]);

        $data = $this->filterService->loadMatrixData($request->all());
        return $this->exportCsv($data);
    }

    private function exportCsv(array $data): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $filename = 'eip-analysis-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($data) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Year', 'Month', 'Total Energy (GJ)', 'Total Resource (GJ)', 'Total Combined (GJ)', 'Variable Value', 'EIP Energy', 'EIP Resource', 'EIP Combined']);

            foreach ($data['data'] as $yearData) {
                foreach ($yearData['months'] as $m) {
                    fputcsv($handle, [
                        $yearData['year'], $m['month'], $m['total_energy'], $m['total_resource'],
                        $m['total_combined'], $m['variable_value'], $m['eip_energy'], $m['eip_resource'], $m['eip_combined'],
                    ]);
                }
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    // ===== REGRESSION DATA ENDPOINT =====

    /**
     * Get regression data for user-selectable X/Y variable combinations.
     * X-axis: any variable from monthly_variables
     * Y-axis: single or multiple energy/energy resource (sum if multiple)
     */
    public function getRegressionData(Request $request)
    {
        $request->validate([
            'year_start' => 'required|integer',
            'year_end' => 'required|integer|gte:year_start',
            'x_variable_ids' => 'required|array|min:1',
            'x_variable_ids.*' => 'exists:monthly_variables,id',
            'y_energy_ids' => 'nullable|array',
            'y_resource_ids' => 'nullable|array',
        ]);

        $yearStart = (int) $request->year_start;
        $yearEnd = (int) $request->year_end;
        $startMonth = $yearStart . '-01';
        $endMonth = $yearEnd . '-12';

        $xVarIds = $request->x_variable_ids;
        $yEnergyIds = $request->y_energy_ids ?? [];
        $yResourceIds = $request->y_resource_ids ?? [];

        // Fetch X variable data
        $xData = MonthlyVariableUsage::whereIn('monthly_variable_id', $xVarIds)
            ->whereBetween('month', [$startMonth, $endMonth])
            ->get()
            ->groupBy('month');

        // Fetch Y energy data
        $yEnergyData = collect();
        if (!empty($yEnergyIds)) {
            $yEnergyData = EnergyDataUsage::whereIn('energy_data_id', $yEnergyIds)
                ->whereBetween('month', [$startMonth, $endMonth])
                ->get()
                ->groupBy('month');
        }

        $yResourceData = collect();
        if (!empty($yResourceIds)) {
            $yResourceData = EnergyResourceUsage::whereIn('energy_resource_data_id', $yResourceIds)
                ->whereBetween('month', [$startMonth, $endMonth])
                ->get()
                ->groupBy('month');
        }

        // Build data points
        $points = [];
        $xVarNames = MonthlyVariable::whereIn('id', $xVarIds)->pluck('variable_name', 'id');

        for ($y = $yearStart; $y <= $yearEnd; $y++) {
            for ($m = 1; $m <= 12; $m++) {
                $monthKey = $y . '-' . str_pad($m, 2, '0', STR_PAD_LEFT);

                // X values (one per variable)
                $xValues = [];
                $monthXData = $xData->get($monthKey, collect());
                foreach ($xVarIds as $vid) {
                    $entry = $monthXData->firstWhere('monthly_variable_id', $vid);
                    $xValues[$vid] = $entry ? (float) $entry->variable_value : 0;
                }

                // Y value (sum of selected energy + resource)
                $yVal = 0;
                $monthEData = $yEnergyData->get($monthKey, collect());
                foreach ($yEnergyIds as $eid) {
                    $entry = $monthEData->firstWhere('energy_data_id', $eid);
                    $yVal += $entry ? (float) $entry->usage_gj : 0;
                }
                $monthRData = $yResourceData->get($monthKey, collect());
                foreach ($yResourceIds as $rid) {
                    $entry = $monthRData->firstWhere('energy_resource_data_id', $rid);
                    $yVal += $entry ? (float) $entry->usage_gj : 0;
                }

                $points[] = [
                    'month' => $monthKey,
                    'label' => $monthKey,
                    'x_values' => $xValues,
                    'y_value' => round($yVal, 4),
                ];
            }
        }

        // Compute simple linear regression (first X variable vs Y)
        $regression = null;
        if (count($xVarIds) === 1) {
            $xId = $xVarIds[0];
            $xArr = array_map(function ($p) use ($xId) { return $p['x_values'][$xId]; }, $points);
            $yArr = array_map(function ($p) { return $p['y_value']; }, $points);
            $n = count($xArr);
            if ($n > 1) {
                $sx = array_sum($xArr);
                $sy = array_sum($yArr);
                $sxy = 0; $sx2 = 0; $sy2 = 0;
                for ($i = 0; $i < $n; $i++) {
                    $sxy += $xArr[$i] * $yArr[$i];
                    $sx2 += $xArr[$i] * $xArr[$i];
                    $sy2 += $yArr[$i] * $yArr[$i];
                }
                $denom = $n * $sx2 - $sx * $sx;
                $slope = $denom != 0 ? ($n * $sxy - $sx * $sy) / $denom : 0;
                $intercept = ($sy - $slope * $sx) / $n;
                $meanY = $sy / $n;
                $ssTot = 0; $ssRes = 0;
                for ($i = 0; $i < $n; $i++) {
                    $predicted = $slope * $xArr[$i] + $intercept;
                    $ssTot += pow($yArr[$i] - $meanY, 2);
                    $ssRes += pow($yArr[$i] - $predicted, 2);
                }
                $r2 = $ssTot > 0 ? 1 - ($ssRes / $ssTot) : 0;
                $regression = [
                    'slope' => round($slope, 6),
                    'intercept' => round($intercept, 4),
                    'r_squared' => round($r2, 6),
                    'equation' => 'y = ' . round($slope, 4) . 'x + ' . round($intercept, 4),
                ];
            }
        }

        return response()->json([
            'success' => true,
            'points' => $points,
            'x_variable_names' => $xVarNames,
            'regression' => $regression,
        ]);
    }

    // ===== PRESET ENDPOINTS =====

    public function listPresets()
    {
        $system = EipFilterPreset::where('is_system', true)->get();
        $user = auth()->check()
            ? EipFilterPreset::where('user_id', auth()->id())->get()
            : collect();

        return response()->json(['system' => $system, 'user' => $user]);
    }

    public function savePreset(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'filters' => 'required|array',
        ]);

        $preset = EipFilterPreset::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'description' => $request->description,
            'filters' => $request->filters,
            'is_system' => false,
            'share_token' => Str::random(32),
        ]);

        return response()->json(['success' => true, 'preset' => $preset]);
    }

    public function loadPreset($id)
    {
        $preset = EipFilterPreset::findOrFail($id);
        $preset->increment('usage_count');
        return response()->json(['success' => true, 'preset' => $preset]);
    }

    public function deletePreset($id)
    {
        $preset = EipFilterPreset::where('id', $id)
            ->where('user_id', auth()->id())
            ->where('is_system', false)
            ->firstOrFail();

        $preset->delete();
        return response()->json(['success' => true]);
    }

    public function toggleFavorite($id)
    {
        $preset = EipFilterPreset::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $preset->update(['is_favorite' => !$preset->is_favorite]);
        return response()->json(['success' => true, 'is_favorite' => $preset->is_favorite]);
    }

    // ===== TARGETS =====

    public function getTargets(Request $request)
    {
        return response()->json(EipTarget::orderBy('year')->get());
    }

    public function storeTarget(Request $request)
    {
        $request->validate([
            'year' => 'required|integer',
            'target_type' => 'required|string|max:30',
            'target_value' => 'required|numeric',
            'seu_threshold' => 'nullable|numeric',
        ]);

        $target = EipTarget::updateOrCreate(
            ['year' => $request->year, 'target_type' => $request->target_type],
            ['target_value' => $request->target_value, 'seu_threshold' => $request->seu_threshold]
        );

        return response()->json(['success' => true, 'target' => $target]);
    }

    // ===== CURRENCY =====

    public function getCurrencyRates()
    {
        $rates = EipCurrencyRate::orderBy('currency_code')
            ->orderByDesc('effective_date')
            ->get()
            ->unique('currency_code');

        return response()->json($rates->values());
    }

    // ===== EXISTING DATA ENTRY ENDPOINTS =====

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
                ['energy_data_id' => $entry['energy_data_id'], 'month' => $monthKey],
                ['usage_value' => $entry['value_gj'], 'usage_unit' => 'GJ', 'usage_gj' => $entry['value_gj']]
            );
        }

        return response()->json(['success' => true, 'message' => 'Energy data saved successfully.']);
    }

    public function storeVariableData(Request $request)
    {
        $request->validate([
            'year' => 'required|integer',
            'variable_id' => 'required|exists:monthly_variables,id',
            'entries' => 'required|array',
            'entries.*.month' => 'required|integer|between:1,12',
            'entries.*.value' => 'required|numeric|min:0',
            'entries.*.unit' => 'required|string|max:10',
        ]);

        foreach ($request->entries as $entry) {
            $monthKey = $request->year . '-' . str_pad($entry['month'], 2, '0', STR_PAD_LEFT);

            MonthlyVariableUsage::updateOrCreate(
                ['monthly_variable_id' => $request->variable_id, 'month' => $monthKey],
                ['variable_value' => $entry['value'], 'variable_unit' => $entry['unit']]
            );
        }

        return response()->json(['success' => true, 'message' => 'Variable data saved successfully.']);
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
