<?php

namespace App\Http\Controllers;

use App\Models\BaselineModel;
use App\Models\EnergyData;
use App\Models\EnergyDataUsage;
use App\Models\EnergyResourceData;
use App\Models\EnergyResourceUsage;
use App\Models\MonthlyProduction;
use App\Models\MonthlyProductionUsage;
use App\Models\MonthlyVariable;
use App\Models\MonthlyVariableUsage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\SimpleExcel\SimpleExcelWriter;

class BaselineModelController extends Controller
{
    // ──────────────────────────────────────────────────────────────────────────
    // CRUD
    // ──────────────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        if (!auth()->user()->hasPermission('enpi-baseline-management.view')) {
            abort(403, 'Unauthorized');
        }

        // Default to the most recent year that has models; fall back to current year
        $defaultYear = BaselineModel::max('year') ?? (int) date('Y');
        $year = (int) $request->get('category', $defaultYear);

        $models = BaselineModel::where('year', $year)
            ->with([
                'energyData',
                'energyResource',
                'monthlyProductionX1', 'monthlyVariableX1',
                'monthlyProductionX2', 'monthlyVariableX2',
                'monthlyProductionX3', 'monthlyVariableX3',
                'monthlyProductionX4', 'monthlyVariableX4',
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        $energyData         = EnergyData::orderBy('energy_type')->get();
        $energyResourceData = EnergyResourceData::orderBy('resource_type')->get();
        $monthlyProductions = MonthlyProduction::orderBy('production_type')->get();
        $monthlyVariables   = MonthlyVariable::orderBy('variable_name')->get();

        return view('admin.enpi-baseline-management.index', compact(
            'models', 'year', 'energyData', 'energyResourceData', 'monthlyProductions', 'monthlyVariables'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->validationRules());

        $modelData = $this->buildModelData($validated);

        BaselineModel::create($modelData);

        return redirect()
            ->route('enpi-baseline-management.index', ['category' => $validated['year']])
            ->with('success', 'Model added successfully!');
    }

    public function update(Request $request, $id)
    {
        $model     = BaselineModel::findOrFail($id);
        $validated = $request->validate($this->validationRules());

        $model->update($this->buildModelData($validated));

        return redirect()
            ->route('enpi-baseline-management.index', ['category' => $validated['year']])
            ->with('success', 'Model updated successfully!');
    }

    public function destroy($id)
    {
        BaselineModel::findOrFail($id)->delete();

        return redirect()->back()->with('success', 'Model deleted successfully!');
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Calculate
    // ──────────────────────────────────────────────────────────────────────────

    public function calculate($id)
    {
        set_time_limit(120);
        ini_set('memory_limit', '256M');

        try {
            $model = BaselineModel::with([
                'energyData', 'energyResource',
                'monthlyProductionX1', 'monthlyVariableX1',
                'monthlyProductionX2', 'monthlyVariableX2',
                'monthlyProductionX3', 'monthlyVariableX3',
                'monthlyProductionX4', 'monthlyVariableX4',
            ])->findOrFail($id);

            $monthlyData   = $this->buildMonthlyDisplayData($model);
            $regressionData = $this->getRegressionChartData($model, $monthlyData);

            if (empty($monthlyData)) {
                return redirect()->back()->with('error', 'No data available for calculation');
            }

            return view('admin.enpi-baseline-management.calculate-result', compact(
                'model', 'monthlyData', 'regressionData'
            ));

        } catch (\Exception $e) {
            \Log::error('Calculate error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Calculation failed: ' . $e->getMessage());
        }
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Export
    // ──────────────────────────────────────────────────────────────────────────

    public function exportExcel($id)
    {
        $model = BaselineModel::with([
            'energyData', 'energyResource',
            'monthlyProductionX1', 'monthlyVariableX1',
            'monthlyProductionX2', 'monthlyVariableX2',
            'monthlyProductionX3', 'monthlyVariableX3',
            'monthlyProductionX4', 'monthlyVariableX4',
        ])->findOrFail($id);

        $monthlyData = $this->buildMonthlyDisplayData($model);
        $filename    = Str::slug($model->model_name) . '-' . $model->year . '.xlsx';

        // Write to a temp file so we can return a proper Laravel download response
        // (avoids calling exit() mid-request the way toBrowser() does)
        $tmpPath = tempnam(sys_get_temp_dir(), 'baseline_') . '.xlsx';

        try {
            $writer = SimpleExcelWriter::create($tmpPath)->noHeaderRow();

            // ── Summary section ───────────────────────────────────────────────
            $writer->addRow(['Model Name',           $model->model_name]);
            $writer->addRow(['Year',                 (string) $model->year]);
            $writer->addRow(['Dependent Variable',   $model->dependent_label]);
            $writer->addRow(['Equation',             $model->equation]);
            $writer->addRow(['R²',                   (string) $model->r_squared]);
            $writer->addRow(['Correlation Strength', $model->correlation_strength]);
            $writer->addRow(['', '']); // blank separator row

            // ── Column headers ────────────────────────────────────────────────
            $headers = ['Month / Year', $model->dependent_label . ' (Y)'];
            for ($i = 1; $i <= $model->number_of_independent_variables; $i++) {
                $xVar      = 'independent_variable_x' . $i;
                $headers[] = ($model->$xVar ?? ('X' . $i)) . ' (X' . $i . ')';
            }
            $writer->addRow($headers);

            // ── Data rows ────────────────────────────────────────────────────
            foreach ($monthlyData as $row) {
                $dataRow = [$row['month'], (string) $row['dependent']];
                for ($i = 1; $i <= $model->number_of_independent_variables; $i++) {
                    $dataRow[] = (string) ($row['independent_x' . $i] ?? 0);
                }
                $writer->addRow($dataRow);
            }

            $writer->close();

            return response()->download($tmpPath, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            @unlink($tmpPath);
            \Log::error('Excel export error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Export failed: ' . $e->getMessage());
        }
    }

    // ──────────────────────────────────────────────────────────────────────────
    // API
    // ──────────────────────────────────────────────────────────────────────────

    public function getVariables()
    {
        $variables = MonthlyVariable::orderBy('variable_name')->pluck('variable_name');
        return response()->json(['variables' => $variables]);
    }

    /**
     * Return energy_data + energy_resource_data records that have usage entries
     * in the requested year. Used to populate the dependent-variable dropdown.
     */
    public function getDependentOptions(Request $request)
    {
        $year = $request->get('year', date('Y'));

        $energyData = EnergyData::whereHas('usages', function ($q) use ($year) {
            $q->whereRaw('SUBSTRING(month, 1, 4) = ?', [$year]);
        })->orderBy('energy_type')->get()->map(fn ($ed) => [
            'value' => 'energy_data:' . $ed->id,
            'label' => $ed->energy_type . ' — ' . $ed->provider . ' (' . $ed->account_no . ')',
            'group' => 'Energy Data',
            'type'  => 'energy_data',
            'id'    => $ed->id,
            'name'  => $ed->energy_type . ' (' . $ed->provider . ')',
        ]);

        $energyResource = EnergyResourceData::whereHas('usages', function ($q) use ($year) {
            $q->whereRaw('SUBSTRING(month, 1, 4) = ?', [$year]);
        })->orderBy('resource_type')->get()->map(fn ($er) => [
            'value' => 'energy_resource:' . $er->id,
            'label' => $er->resource_type . ' — ' . $er->provider . ' (' . $er->account_no . ')',
            'group' => 'Energy Resource',
            'type'  => 'energy_resource',
            'id'    => $er->id,
            'name'  => $er->resource_type . ' (' . $er->provider . ')',
        ]);

        return response()->json(['options' => $energyData->merge($energyResource)->values()]);
    }

    /**
     * Return monthly_productions + monthly_variables records that have usage
     * entries in the requested year. Used to populate independent-variable dropdowns.
     */
    public function getIndependentOptions(Request $request)
    {
        $year = $request->get('year', date('Y'));

        $productions = MonthlyProduction::whereHas('usages', function ($q) use ($year) {
            $q->whereRaw('SUBSTRING(month, 1, 4) = ?', [$year]);
        })->orderBy('production_type')->get()->map(fn ($mp) => [
            'value' => 'monthly_production:' . $mp->id,
            'label' => $mp->production_type,
            'group' => 'Production Data',
            'type'  => 'monthly_production',
            'id'    => $mp->id,
            'name'  => $mp->production_type,
        ]);

        $variables = MonthlyVariable::whereHas('usages', function ($q) use ($year) {
            $q->whereRaw('SUBSTRING(month, 1, 4) = ?', [$year]);
        })->orderBy('variable_name')->get()->map(fn ($mv) => [
            'value' => 'monthly_variable:' . $mv->id,
            'label' => $mv->variable_name,
            'group' => 'Variable Data',
            'type'  => 'monthly_variable',
            'id'    => $mv->id,
            'name'  => $mv->variable_name,
        ]);

        return response()->json(['options' => $productions->merge($variables)->values()]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Validation & model-building helpers
    // ──────────────────────────────────────────────────────────────────────────

    private function validationRules(): array
    {
        return [
            'model_name'                     => 'required|string|max:255',
            'number_of_independent_variables'=> 'required|integer|in:1,2,3,4',
            'year'                           => 'required|integer',

            // Dependent variable
            'dependent_variable_type' => 'required|in:energy_data,energy_resource,monthly_variable',
            'energy_data_id'          => 'nullable|required_if:dependent_variable_type,energy_data|exists:energy_data,id',
            'energy_resource_id'      => 'nullable|required_if:dependent_variable_type,energy_resource|exists:energy_resource_data,id',
            'dependent_variable'      => 'nullable|string|max:255',

            // X1
            'independent_variable_x1'      => 'required|string|max:255',
            'independent_variable_type_x1' => 'required|in:monthly_production,monthly_variable',
            'monthly_production_id_x1'     => 'nullable|exists:monthly_productions,id',
            'monthly_variable_id_x1'       => 'nullable|exists:monthly_variables,id',

            // X2
            'independent_variable_x2'      => 'nullable|string|max:255',
            'independent_variable_type_x2' => 'nullable|in:monthly_production,monthly_variable',
            'monthly_production_id_x2'     => 'nullable|exists:monthly_productions,id',
            'monthly_variable_id_x2'       => 'nullable|exists:monthly_variables,id',

            // X3
            'independent_variable_x3'      => 'nullable|string|max:255',
            'independent_variable_type_x3' => 'nullable|in:monthly_production,monthly_variable',
            'monthly_production_id_x3'     => 'nullable|exists:monthly_productions,id',
            'monthly_variable_id_x3'       => 'nullable|exists:monthly_variables,id',

            // X4
            'independent_variable_x4'      => 'nullable|string|max:255',
            'independent_variable_type_x4' => 'nullable|in:monthly_production,monthly_variable',
            'monthly_production_id_x4'     => 'nullable|exists:monthly_productions,id',
            'monthly_variable_id_x4'       => 'nullable|exists:monthly_variables,id',
        ];
    }

    /**
     * Merge validated input with freshly calculated regression stats.
     */
    private function buildModelData(array $validated): array
    {
        // Temporarily hydrate a non-persisted model so we can re-use the
        // data-fetching helpers that expect a model instance.
        $temp = new BaselineModel($validated);

        $regressionData = $this->fetchRegressionDataForModel($temp);

        $result = empty($regressionData)
            ? ['r_squared' => 0, 'equation' => 'No data available', 'correlation_strength' => 'None']
            : ($temp->number_of_independent_variables === 1
                ? $this->simpleLinearRegression($regressionData)
                : $this->multipleLinearRegression($regressionData, $temp->number_of_independent_variables));

        return array_merge($validated, [
            'r_squared'           => $result['r_squared'],
            'equation'            => $result['equation'],
            'correlation_strength'=> $result['correlation_strength'],
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Data-fetching: dependent variable (Y)
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Returns array keyed by 'YYYY-MM' => float value (GJ where applicable).
     */
    private function getDependentVariableData(BaselineModel $model): array
    {
        $year = (string) $model->year;

        if ($model->dependent_variable_type === 'energy_data' && $model->energy_data_id) {
            return EnergyDataUsage::where('energy_data_id', $model->energy_data_id)
                ->whereRaw('SUBSTRING(month, 1, 4) = ?', [$year])
                ->pluck('usage_gj', 'month')
                ->toArray();
        }

        if ($model->dependent_variable_type === 'energy_resource' && $model->energy_resource_id) {
            return EnergyResourceUsage::where('energy_resource_data_id', $model->energy_resource_id)
                ->whereRaw('SUBSTRING(month, 1, 4) = ?', [$year])
                ->pluck('usage_gj', 'month')
                ->toArray();
        }

        // monthly_variable (legacy / fallback)
        $varId = MonthlyVariable::where('variable_name', $model->dependent_variable)->value('id');
        if (!$varId) return [];

        return MonthlyVariableUsage::where('monthly_variable_id', $varId)
            ->whereRaw('SUBSTRING(month, 1, 4) = ?', [$year])
            ->pluck('variable_value', 'month')
            ->toArray();
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Data-fetching: independent variables (X1–X4)
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Returns ['x1' => [...], 'x2' => [...], 'x3' => [...], 'x4' => [...]].
     * Each inner array is keyed 'YYYY-MM' => float.
     */
    private function getIndependentVariablesData(BaselineModel $model): array
    {
        $year = (string) $model->year;
        $data = ['x1' => [], 'x2' => [], 'x3' => [], 'x4' => []];

        for ($i = 1; $i <= $model->number_of_independent_variables; $i++) {
            $typeKey  = 'independent_variable_type_x' . $i;
            $prodKey  = 'monthly_production_id_x' . $i;
            $varKey   = 'monthly_variable_id_x' . $i;

            $data['x' . $i] = $this->getSingleIndependentVariableData(
                $model->$typeKey,
                $model->$prodKey,
                $model->$varKey,
                $year
            );
        }

        return $data;
    }

    private function getSingleIndependentVariableData(
        ?string $type,
        ?int    $productionId,
        ?int    $variableId,
        string  $year
    ): array {
        if ($type === 'monthly_production' && $productionId) {
            return MonthlyProductionUsage::where('monthly_production_id', $productionId)
                ->whereRaw('SUBSTRING(month, 1, 4) = ?', [$year])
                ->pluck('production_amount', 'month')
                ->toArray();
        }

        if ($type === 'monthly_variable' && $variableId) {
            return MonthlyVariableUsage::where('monthly_variable_id', $variableId)
                ->whereRaw('SUBSTRING(month, 1, 4) = ?', [$year])
                ->pluck('variable_value', 'month')
                ->toArray();
        }

        return [];
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Regression data builder
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Combines Y and X arrays into data-point objects, skipping any month
     * that is missing data for Y or any required X.
     *
     * @return array of \stdClass { month, y, x1 [, x2, x3, x4] }
     */
    private function fetchRegressionDataForModel(BaselineModel $model): array
    {
        $yData = $this->getDependentVariableData($model);
        $xData = $this->getIndependentVariablesData($model);

        $points = [];

        foreach ($yData as $month => $yValue) {
            $point = new \stdClass();
            $point->month = $month;
            $point->y     = (float) $yValue;

            $allPresent = true;
            for ($i = 1; $i <= $model->number_of_independent_variables; $i++) {
                $xKey = 'x' . $i;
                if (!isset($xData[$xKey][$month])) {
                    $allPresent = false;
                    break;
                }
                $point->$xKey = (float) $xData[$xKey][$month];
            }

            if ($allPresent) {
                $points[] = $point;
            }
        }

        usort($points, fn($a, $b) => strcmp($a->month, $b->month));

        return $points;
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Monthly display data (for the results table)
    // ──────────────────────────────────────────────────────────────────────────

    private function buildMonthlyDisplayData(BaselineModel $model): array
    {
        $yData = $this->getDependentVariableData($model);
        $xData = $this->getIndependentVariablesData($model);

        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                   'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        $rows = [];
        foreach ($months as $idx => $label) {
            $monthStr = $model->year . '-' . str_pad($idx + 1, 2, '0', STR_PAD_LEFT);
            $displayMonth = $label . '-' . substr((string) $model->year, -2);

            $row = [
                'month'     => $displayMonth,
                'month_key' => $monthStr,
                'dependent' => (float) ($yData[$monthStr] ?? 0),
            ];

            for ($i = 1; $i <= $model->number_of_independent_variables; $i++) {
                $row['independent_x' . $i] = (float) ($xData['x' . $i][$monthStr] ?? 0);
            }

            $rows[] = $row;
        }

        return $rows;
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Regression algorithms
    // ──────────────────────────────────────────────────────────────────────────

    private function simpleLinearRegression(array $data): array
    {
        $n = count($data);
        $sumX = $sumY = $sumXY = $sumX2 = $sumY2 = 0;

        foreach ($data as $point) {
            $x = (float) $point->x1;
            $y = (float) $point->y;
            $sumX  += $x;
            $sumY  += $y;
            $sumXY += $x * $y;
            $sumX2 += $x * $x;
            $sumY2 += $y * $y;
        }

        $denominator = $n * $sumX2 - $sumX * $sumX;

        if ($denominator == 0) {
            return ['r_squared' => 0, 'equation' => 'Cannot calculate - insufficient variation',
                    'correlation_strength' => 'None', 'coefficients' => ['b0' => 0, 'b1' => 0]];
        }

        $b1 = ($n * $sumXY - $sumX * $sumY) / $denominator;
        $b0 = ($sumY - $b1 * $sumX) / $n;

        $yMean = $sumY / $n;
        $ssTotal = $ssResidual = 0;

        foreach ($data as $point) {
            $yPred      = $b0 + $b1 * (float) $point->x1;
            $ssTotal    += pow((float) $point->y - $yMean, 2);
            $ssResidual += pow((float) $point->y - $yPred, 2);
        }

        $rSquared = $ssTotal > 0 ? 1 - ($ssResidual / $ssTotal) : 0;

        return [
            'r_squared'            => round($rSquared, 3),
            'equation'             => str_replace('+ -', '- ', sprintf('y = %.4f x1 + %.4f', $b1, $b0)),
            'correlation_strength' => $this->getCorrelationStrength($rSquared),
            'coefficients'         => ['b0' => $b0, 'b1' => $b1],
        ];
    }

    /**
     * Multiple linear regression supporting 2–4 independent variables.
     * Uses normal equations: β = (X'X)⁻¹ X'Y
     */
    private function multipleLinearRegression(array $data, int $numVars = 2): array
    {
        $n = count($data);

        if ($n <= $numVars) {
            return ['r_squared' => 0, 'equation' => 'Insufficient data points',
                    'correlation_strength' => 'None',
                    'coefficients' => $this->zeroCoefficients($numVars)];
        }

        // Validate that all required x columns are present
        foreach ($data as $point) {
            for ($i = 1; $i <= $numVars; $i++) {
                $xKey = 'x' . $i;
                if (!isset($point->$xKey)) {
                    return ['r_squared' => 0, 'equation' => 'Missing variable x' . $i,
                            'correlation_strength' => 'None',
                            'coefficients' => $this->zeroCoefficients($numVars)];
                }
            }
        }

        // Build X matrix and Y vector
        $X = [];
        $Y = [];

        foreach ($data as $point) {
            $row = [1]; // intercept column
            for ($i = 1; $i <= $numVars; $i++) {
                $xKey  = 'x' . $i;
                $row[] = (float) $point->$xKey;
            }
            $X[] = $row;
            $Y[] = (float) $point->y;
        }

        try {
            $XT    = $this->transpose($X);
            $XTX   = $this->matrixMultiply($XT, $X);

            if ($this->isNearSingular($XTX)) {
                return ['r_squared' => 0, 'equation' => 'Cannot calculate - singular matrix',
                        'correlation_strength' => 'None',
                        'coefficients' => $this->zeroCoefficients($numVars)];
            }

            $XTXInv = $this->matrixInverse($XTX);
            $XTY    = $this->matrixVectorMultiply($XT, $Y);
            $beta   = $this->matrixVectorMultiply($XTXInv, $XTY);

            // R²
            $yMean = array_sum($Y) / $n;
            $ssTotal = $ssResidual = 0;

            foreach ($data as $i => $point) {
                $yPred = $beta[0];
                for ($j = 1; $j <= $numVars; $j++) {
                    $xKey   = 'x' . $j;
                    $yPred += $beta[$j] * (float) $point->$xKey;
                }
                $ssTotal    += pow($Y[$i] - $yMean, 2);
                $ssResidual += pow($Y[$i] - $yPred, 2);
            }

            $rSquared = $ssTotal > 0 ? 1 - ($ssResidual / $ssTotal) : 0;

            // Build equation string
            $parts = [];
            for ($i = 1; $i <= $numVars; $i++) {
                $parts[] = sprintf('%.4f x%d', $beta[$i], $i);
            }
            $equation = str_replace('+ -', '- ', 'y = ' . implode(' + ', $parts) . sprintf(' + %.4f', $beta[0]));

            // Build coefficients array
            $coefficients = ['b0' => $beta[0]];
            for ($i = 1; $i <= $numVars; $i++) {
                $coefficients['b' . $i] = $beta[$i];
            }

            return [
                'r_squared'            => round($rSquared, 3),
                'equation'             => $equation,
                'correlation_strength' => $this->getCorrelationStrength($rSquared),
                'coefficients'         => $coefficients,
            ];

        } catch (\Exception $e) {
            \Log::error('Multiple regression error: ' . $e->getMessage());
            return ['r_squared' => 0, 'equation' => 'Cannot calculate - ' . $e->getMessage(),
                    'correlation_strength' => 'None',
                    'coefficients' => $this->zeroCoefficients($numVars)];
        }
    }

    private function zeroCoefficients(int $numVars): array
    {
        $c = ['b0' => 0];
        for ($i = 1; $i <= $numVars; $i++) {
            $c['b' . $i] = 0;
        }
        return $c;
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Chart data
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Returns one chart dataset per independent variable.
     * Each dataset contains data points (Y vs Xi) and a 2-point partial
     * regression line (all other X held at their mean).
     *
     * Structure returned:
     * ['charts' => [
     *   ['xi' => 1, 'xLabel' => '...', 'dataPoints' => [...], 'regressionLine' => [...]],
     *   ...
     * ]]
     */
    private function getRegressionChartData(BaselineModel $model, array $monthlyData): array
    {
        $regressionPoints = $this->fetchRegressionDataForModel($model);
        $numVars          = $model->number_of_independent_variables;

        $result       = $numVars === 1
            ? $this->simpleLinearRegression($regressionPoints)
            : $this->multipleLinearRegression($regressionPoints, $numVars);
        $coefficients = $result['coefficients'] ?? ['b0' => 0, 'b1' => 0];

        // Mean of each X across months that have data — used for partial regression lines
        $means = [];
        for ($i = 1; $i <= $numVars; $i++) {
            $key          = 'independent_x' . $i;
            $vals         = array_filter(array_column($monthlyData, $key), fn($v) => $v != 0);
            $means[$i]    = count($vals) > 0 ? array_sum($vals) / count($vals) : 0;
        }

        $charts = [];

        for ($xi = 1; $xi <= $numVars; $xi++) {
            $xVarKey = 'independent_variable_x' . $xi;
            $xLabel  = $model->$xVarKey ?? ('X' . $xi);
            $xCol    = 'independent_x' . $xi;

            // Scatter points: Y vs this Xi, skip rows where both are zero
            $dataPoints = [];
            foreach ($monthlyData as $row) {
                if ($row['dependent'] == 0 && ($row[$xCol] ?? 0) == 0) continue;
                $dataPoints[] = [
                    'x'     => (float) ($row[$xCol] ?? 0),
                    'y'     => (float) $row['dependent'],
                    'month' => $row['month'],
                ];
            }

            // Partial regression line: vary Xi, hold all other Xj at mean(Xj)
            $capturedXi = $xi;
            $predictY   = function (float $xVal) use ($coefficients, $numVars, $means, $capturedXi): float {
                $y = $coefficients['b0'];
                for ($j = 1; $j <= $numVars; $j++) {
                    $bKey  = 'b' . $j;
                    $xUsed = ($j === $capturedXi) ? $xVal : ($means[$j] ?? 0);
                    $y    += ($coefficients[$bKey] ?? 0) * $xUsed;
                }
                return $y;
            };

            $regressionLine = [];
            if (!empty($dataPoints)) {
                $xVals = array_column($dataPoints, 'x');
                $xMin  = min($xVals);
                $xMax  = max($xVals);
                $pad   = ($xMax - $xMin) * 0.05 ?: abs($xMin) * 0.05 ?: 1;
                $regressionLine = [
                    ['x' => $xMin - $pad, 'y' => $predictY($xMin - $pad)],
                    ['x' => $xMax + $pad, 'y' => $predictY($xMax + $pad)],
                ];
            }

            $charts[] = [
                'xi'             => $xi,
                'xLabel'         => $xLabel,
                'dataPoints'     => $dataPoints,
                'regressionLine' => $regressionLine,
            ];
        }

        return ['charts' => $charts];
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Correlation strength
    // ──────────────────────────────────────────────────────────────────────────

    private function getCorrelationStrength(float $rSquared): string
    {
        if ($rSquared >= 0.9) return 'Very Strong';
        if ($rSquared >= 0.7) return 'Strong';
        if ($rSquared >= 0.5) return 'Moderate';
        if ($rSquared >= 0.3) return 'Weak';
        return 'Very Weak';
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Matrix helpers
    // ──────────────────────────────────────────────────────────────────────────

    private function isNearSingular(array $matrix): bool
    {
        $n = count($matrix);
        if ($n === 3) {
            $m = $matrix;
            $det = $m[0][0] * ($m[1][1] * $m[2][2] - $m[1][2] * $m[2][1])
                 - $m[0][1] * ($m[1][0] * $m[2][2] - $m[1][2] * $m[2][0])
                 + $m[0][2] * ($m[1][0] * $m[2][1] - $m[1][1] * $m[2][0]);
            return abs($det) < 1e-10;
        }
        // For larger matrices just attempt the inversion (it will throw if singular)
        return false;
    }

    private function transpose(array $matrix): array
    {
        return array_map(null, ...$matrix);
    }

    private function matrixMultiply(array $a, array $b): array
    {
        $result = [];
        for ($i = 0; $i < count($a); $i++) {
            for ($j = 0; $j < count($b[0]); $j++) {
                $result[$i][$j] = 0;
                for ($k = 0; $k < count($b); $k++) {
                    $result[$i][$j] += $a[$i][$k] * $b[$k][$j];
                }
            }
        }
        return $result;
    }

    private function matrixVectorMultiply(array $matrix, array $vector): array
    {
        $result = [];
        for ($i = 0; $i < count($matrix); $i++) {
            $result[$i] = 0;
            for ($j = 0; $j < count($vector); $j++) {
                $result[$i] += $matrix[$i][$j] * $vector[$j];
            }
        }
        return $result;
    }

    private function matrixInverse(array $matrix): array
    {
        $n        = count($matrix);
        $identity = [];

        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                $identity[$i][$j] = ($i === $j) ? 1 : 0;
            }
        }

        for ($i = 0; $i < $n; $i++) {
            $pivot = $matrix[$i][$i];

            if (abs($pivot) < 1e-10) {
                throw new \Exception('Matrix is singular');
            }

            for ($j = 0; $j < $n; $j++) {
                $matrix[$i][$j]   /= $pivot;
                $identity[$i][$j] /= $pivot;
            }

            for ($k = 0; $k < $n; $k++) {
                if ($k !== $i) {
                    $factor = $matrix[$k][$i];
                    for ($j = 0; $j < $n; $j++) {
                        $matrix[$k][$j]   -= $factor * $matrix[$i][$j];
                        $identity[$k][$j] -= $factor * $identity[$i][$j];
                    }
                }
            }
        }

        return $identity;
    }
}
