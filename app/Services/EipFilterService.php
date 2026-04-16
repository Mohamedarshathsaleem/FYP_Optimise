<?php

namespace App\Services;

use App\Models\EnergyData;
use App\Models\EnergyResourceData;
use App\Models\EnergyDataUsage;
use App\Models\EnergyResourceUsage;
use App\Models\MonthlyVariable;
use App\Models\MonthlyVariableUsage;
use App\Models\EipNormalizationFactor;
use App\Models\EipTarget;
use App\Models\EipCurrencyRate;
use App\Models\EipWeatherData;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EipFilterService
{
    /**
     * Resolve date preset into concrete start/end month strings.
     */
    public function resolveMonthRange(array $filters): array
    {
        $preset = $filters['date_preset'] ?? null;
        $now = Carbon::now();

        switch ($preset) {
            case 'last_3':
                return [
                    'start' => $now->copy()->subMonths(3)->format('Y-m'),
                    'end'   => $now->format('Y-m'),
                ];
            case 'last_6':
                return [
                    'start' => $now->copy()->subMonths(6)->format('Y-m'),
                    'end'   => $now->format('Y-m'),
                ];
            case 'last_12':
                return [
                    'start' => $now->copy()->subMonths(12)->format('Y-m'),
                    'end'   => $now->format('Y-m'),
                ];
            case 'ytd':
                return [
                    'start' => $now->copy()->startOfYear()->format('Y-m'),
                    'end'   => $now->format('Y-m'),
                ];
            case 'custom':
                return [
                    'start' => $filters['custom_start'] ?? $now->copy()->subYear()->format('Y-m'),
                    'end'   => $filters['custom_end'] ?? $now->format('Y-m'),
                ];
            default:
                return [
                    'start' => ($filters['year_start'] ?? $now->year) . '-01',
                    'end'   => ($filters['year_end'] ?? $now->year) . '-12',
                ];
        }
    }

    /**
     * Load the full matrix data with all filters applied.
     */
    public function loadMatrixData(array $params): array
    {
        $yearStart = (int) $params['year_start'];
        $yearEnd = (int) $params['year_end'];
        $variableTypeId = (int) $params['variable_type'];

        $energySources = EnergyData::orderBy('id')->get();
        $resourceSources = EnergyResourceData::orderBy('id')->get();

        $energySourceIds = $params['energy_source_ids']
            ?? $energySources->pluck('id')->toArray();
        $resourceSourceIds = $params['resource_source_ids']
            ?? $resourceSources->pluck('id')->toArray();

        // Date range resolution
        $dateRange = $this->resolveMonthRange($params);
        $quarters = $params['quarters'] ?? null;
        $quarterMonths = $this->getQuarterMonths($quarters);

        // OPTIMIZATION: Fetch ALL data in bulk before processing
        $startMonth = $yearStart . '-01';
        $endMonth = $yearEnd . '-12';

        $allEnergyData = EnergyDataUsage::whereBetween('month', [$startMonth, $endMonth])
            ->whereIn('energy_data_id', $energySourceIds)
            ->get()
            ->keyBy(function ($item) {
                return $item->month . '|' . $item->energy_data_id;
            });

        $allResourceData = EnergyResourceUsage::whereBetween('month', [$startMonth, $endMonth])
            ->whereIn('energy_resource_data_id', $resourceSourceIds)
            ->get()
            ->keyBy(function ($item) {
                return $item->month . '|' . $item->energy_resource_data_id;
            });

        $allVariableData = MonthlyVariableUsage::whereBetween('month', [$startMonth, $endMonth])
            ->where('monthly_variable_id', $variableTypeId)
            ->get()
            ->keyBy('month');

        // OPTIMIZATION: Fetch all normalization factors in bulk
        $normType = $params['normalization_type'] ?? 'none';
        $allNormFactors = [];
        if ($normType !== 'none') {
            $allNormFactors = EipNormalizationFactor::where('factor_type', $normType)
                ->whereBetween('month', [$startMonth, $endMonth])
                ->get()
                ->keyBy('month')
                ->mapWithKeys(function ($item) {
                    return [$item->month => $item->factor_value];
                })
                ->toArray();
        }

        // OPTIMIZATION: Fetch all costs in bulk
        $energyCosts = EnergyDataUsage::whereBetween('month', [$startMonth, $endMonth])
            ->whereIn('energy_data_id', $energySourceIds)
            ->get()
            ->groupBy('month')
            ->mapWithKeys(function ($group, $month) {
                return [$month => $group->sum('cost')];
            })
            ->toArray();

        $resourceCosts = EnergyResourceUsage::whereBetween('month', [$startMonth, $endMonth])
            ->whereIn('energy_resource_data_id', $resourceSourceIds)
            ->get()
            ->groupBy('month')
            ->mapWithKeys(function ($group, $month) {
                return [$month => $group->sum('cost')];
            })
            ->toArray();

        $result = [];

        for ($year = $yearStart; $year <= $yearEnd; $year++) {
            $yearData = ['year' => $year, 'months' => []];

            $yearlyEnergyBySource = array_fill_keys($energySourceIds, 0);
            $yearlyResourceBySource = array_fill_keys($resourceSourceIds, 0);
            $yearlyTotalEnergy = 0;
            $yearlyTotalResource = 0;
            $yearlyTotalCombined = 0;
            $yearlyTotalVariable = 0;

            for ($month = 1; $month <= 12; $month++) {
                // Quarter filtering
                if ($quarterMonths && !in_array($month, $quarterMonths)) {
                    continue;
                }

                // Date range filtering
                $monthKey = sprintf('%d-%02d', $year, $month);
                if ($monthKey < $dateRange['start'] || $monthKey > $dateRange['end']) {
                    continue;
                }

                $energyCols = [];
                $totalEnergy = 0;
                foreach ($energySourceIds as $esId) {
                    $key = $monthKey . '|' . $esId;
                    $val = isset($allEnergyData[$key]) ? (float) $allEnergyData[$key]->usage_gj : 0;
                    $energyCols[$esId] = $val;
                    $totalEnergy += $val;
                    $yearlyEnergyBySource[$esId] += $val;
                }

                $resourceCols = [];
                $totalResource = 0;
                foreach ($resourceSourceIds as $rsId) {
                    $key = $monthKey . '|' . $rsId;
                    $val = isset($allResourceData[$key]) ? (float) $allResourceData[$key]->usage_gj : 0;
                    $resourceCols[$rsId] = $val;
                    $totalResource += $val;
                    $yearlyResourceBySource[$rsId] += $val;
                }

                $totalCombined = $totalEnergy + $totalResource;
                $yearlyTotalEnergy += $totalEnergy;
                $yearlyTotalResource += $totalResource;
                $yearlyTotalCombined += $totalCombined;

                $monthVariable = $allVariableData->get($monthKey);
                $variableValue = (float) ($monthVariable->variable_value ?? 0);
                $yearlyTotalVariable += $variableValue;

                // Normalization (now from pre-loaded data)
                $normFactor = $allNormFactors[$monthKey] ?? 0;
                $divisor = $normFactor > 0 ? $normFactor : ($variableValue > 0 ? $variableValue : 1);

                if ($normType === 'none') {
                    $divisor = $variableValue > 0 ? $variableValue : 1;
                }

                $eipEnergy = round($totalEnergy / $divisor, 4);
                $eipResource = round($totalResource / $divisor, 4);
                $eipCombined = round($totalCombined / $divisor, 4);

                // Cost (now from pre-loaded data)
                $cost = ($energyCosts[$monthKey] ?? 0) + ($resourceCosts[$monthKey] ?? 0);

                $yearData['months'][] = [
                    'month' => $month,
                    'month_key' => $monthKey,
                    'energy' => $energyCols,
                    'total_energy' => round($totalEnergy, 4),
                    'resource' => $resourceCols,
                    'total_resource' => round($totalResource, 4),
                    'total_combined' => round($totalCombined, 4),
                    'variable_value' => round($variableValue, 4),
                    'eip_energy' => $eipEnergy,
                    'eip_resource' => $eipResource,
                    'eip_combined' => $eipCombined,
                    'cost' => round($cost, 2),
                ];
            }

            // Apply threshold filters to months
            $yearData['months'] = $this->applyThresholdFilters($yearData['months'], $params);

            // Yearly totals
            $yearlyEipEnergy = $yearlyTotalVariable > 0 ? round($yearlyTotalEnergy / $yearlyTotalVariable, 4) : 0;
            $yearlyEipResource = $yearlyTotalVariable > 0 ? round($yearlyTotalResource / $yearlyTotalVariable, 4) : 0;
            $yearlyEipCombined = $yearlyTotalVariable > 0 ? round($yearlyTotalCombined / $yearlyTotalVariable, 4) : 0;

            $yearData['yearly_totals'] = [
                'energy' => $yearlyEnergyBySource,
                'total_energy' => round($yearlyTotalEnergy, 4),
                'resource' => $yearlyResourceBySource,
                'total_resource' => round($yearlyTotalResource, 4),
                'total_combined' => round($yearlyTotalCombined, 4),
                'total_variable' => round($yearlyTotalVariable, 4),
            ];
            $yearData['yearly_eip'] = [
                'eip_energy' => $yearlyEipEnergy,
                'eip_resource' => $yearlyEipResource,
                'eip_combined' => $yearlyEipCombined,
            ];

            $result[] = $yearData;
        }

        // Build source name maps
        $energySourceNames = [];
        foreach ($energySources as $es) {
            $energySourceNames[$es->id] = $es->energy_type;
        }
        $resourceSourceNames = [];
        foreach ($resourceSources as $rs) {
            $resourceSourceNames[$rs->id] = $rs->resource_type;
        }

        $variableType = MonthlyVariable::find($variableTypeId);

        // EIP Total table
        $eipTotalTable = $this->buildEipTotalTable($result);

        // Trend detection
        $allEipValues = [];
        foreach ($result as $yd) {
            foreach ($yd['months'] as $md) {
                $allEipValues[] = $md['eip_energy'];
            }
        }
        $trends = $this->detectTrends($allEipValues);

        // Recommendations (Phase 3)
        $recommendations = $this->generateRecommendations($result, $allEipValues);

        return [
            'success' => true,
            'data' => $result,
            'energy_source_names' => $energySourceNames,
            'resource_source_names' => $resourceSourceNames,
            'variable_type' => $variableType ? $variableType->variable_name : 'Unknown',
            'eip_total_table' => $eipTotalTable,
            'trends' => $trends,
            'recommendations' => $recommendations,
        ];
    }

    /**
     * Apply consumption/cost threshold filters to monthly data.
     */
    public function applyThresholdFilters(array $months, array $params): array
    {
        $minConsumption = $params['min_consumption'] ?? null;
        $maxConsumption = $params['max_consumption'] ?? null;
        $hideZero = $params['hide_zero'] ?? false;
        $pctThreshold = $params['pct_change_threshold'] ?? null;

        return array_values(array_filter($months, function ($m) use ($minConsumption, $maxConsumption, $hideZero, $pctThreshold) {
            $val = $m['total_combined'];

            if ($hideZero && $val == 0) return false;
            if ($minConsumption !== null && $val < $minConsumption) return false;
            if ($maxConsumption !== null && $val > $maxConsumption) return false;

            return true;
        }));
    }

    /**
     * Detect trends using linear regression and anomaly detection.
     */
    public function detectTrends(array $values): array
    {
        if (count($values) < 3) {
            return ['trend' => 'stable', 'slope' => 0, 'anomalies' => [], 'peak_index' => 0, 'low_index' => 0];
        }

        $n = count($values);
        $sumX = 0; $sumY = 0; $sumXY = 0; $sumX2 = 0;
        for ($i = 0; $i < $n; $i++) {
            $sumX += $i;
            $sumY += $values[$i];
            $sumXY += $i * $values[$i];
            $sumX2 += $i * $i;
        }
        $slope = ($n * $sumXY - $sumX * $sumY) / max($n * $sumX2 - $sumX * $sumX, 1);
        $mean = $sumY / $n;

        // Standard deviation
        $variance = 0;
        foreach ($values as $v) {
            $variance += ($v - $mean) ** 2;
        }
        $stdDev = sqrt($variance / $n);

        // Anomaly detection (z-score > 2)
        $anomalies = [];
        foreach ($values as $i => $v) {
            if ($stdDev > 0 && abs($v - $mean) / $stdDev > 2) {
                $anomalies[] = $i;
            }
        }

        // Peak and low
        $peakIndex = array_search(max($values), $values);
        $nonZero = array_filter($values, fn($v) => $v > 0);
        $lowIndex = $nonZero ? array_search(min($nonZero), $values) : 0;

        // Trend classification
        $slopeRatio = $mean > 0 ? abs($slope) / $mean : 0;
        if ($slopeRatio < 0.01) {
            $trend = 'stable';
        } elseif ($slope > 0) {
            $trend = 'increasing';
        } else {
            $trend = 'decreasing';
        }

        return [
            'trend' => $trend,
            'slope' => round($slope, 6),
            'mean' => round($mean, 4),
            'std_dev' => round($stdDev, 4),
            'anomalies' => $anomalies,
            'peak_index' => $peakIndex,
            'low_index' => $lowIndex,
        ];
    }

    /**
     * Aggregate data by granularity.
     */
    public function aggregate(array $monthlyRows, string $granularity, string $method = 'sum'): array
    {
        if ($granularity === 'monthly') {
            return $monthlyRows;
        }

        $buckets = [];
        foreach ($monthlyRows as $row) {
            $key = $this->getBucketKey($row['month'], $row['month_key'] ?? '', $granularity);
            if (!isset($buckets[$key])) {
                $buckets[$key] = [];
            }
            $buckets[$key][] = $row;
        }

        $aggregated = [];
        foreach ($buckets as $key => $rows) {
            $energyVals = array_column($rows, 'total_energy');
            $resourceVals = array_column($rows, 'total_resource');
            $combinedVals = array_column($rows, 'total_combined');
            $eipEnergyVals = array_column($rows, 'eip_energy');
            $eipResourceVals = array_column($rows, 'eip_resource');
            $eipCombinedVals = array_column($rows, 'eip_combined');

            $aggregated[] = [
                'label' => $key,
                'month' => $rows[0]['month'],
                'total_energy' => round($this->applyMethod($energyVals, $method), 4),
                'total_resource' => round($this->applyMethod($resourceVals, $method), 4),
                'total_combined' => round($this->applyMethod($combinedVals, $method), 4),
                'eip_energy' => round($this->applyMethod($eipEnergyVals, $method), 4),
                'eip_resource' => round($this->applyMethod($eipResourceVals, $method), 4),
                'eip_combined' => round($this->applyMethod($eipCombinedVals, $method), 4),
                'count' => count($rows),
            ];
        }

        return $aggregated;
    }

    /**
     * Calculate confidence interval.
     */
    public function confidenceInterval(array $values, float $level = 0.95): array
    {
        $n = count($values);
        if ($n < 2) return ['lower' => 0, 'upper' => 0, 'mean' => 0];

        $mean = array_sum($values) / $n;
        $variance = 0;
        foreach ($values as $v) {
            $variance += ($v - $mean) ** 2;
        }
        $stdDev = sqrt($variance / ($n - 1));

        $zScores = ['0.90' => 1.645, '0.95' => 1.96, '0.99' => 2.576];
        $z = $zScores[(string) $level] ?? 1.96;
        $margin = $z * ($stdDev / sqrt($n));

        return [
            'lower' => round($mean - $margin, 4),
            'upper' => round($mean + $margin, 4),
            'mean' => round($mean, 4),
            'std_dev' => round($stdDev, 4),
            'margin' => round($margin, 4),
        ];
    }

    /**
     * Detect outliers using specified method.
     */
    public function detectOutliers(array $values, string $method = 'iqr'): array
    {
        if (count($values) < 4) return [];

        $sorted = $values;
        sort($sorted);
        $n = count($sorted);

        switch ($method) {
            case 'iqr':
                $q1 = $sorted[(int) floor($n * 0.25)];
                $q3 = $sorted[(int) floor($n * 0.75)];
                $iqr = $q3 - $q1;
                $lower = $q1 - 1.5 * $iqr;
                $upper = $q3 + 1.5 * $iqr;
                break;

            case 'zscore':
                $mean = array_sum($values) / $n;
                $stdDev = sqrt(array_sum(array_map(fn($v) => ($v - $mean) ** 2, $values)) / $n);
                $lower = $mean - 2 * $stdDev;
                $upper = $mean + 2 * $stdDev;
                break;

            case 'modified_zscore':
                $median = $sorted[(int) floor($n / 2)];
                $mad = $sorted; // median absolute deviation
                foreach ($mad as &$v) $v = abs($v - $median);
                sort($mad);
                $madMedian = $mad[(int) floor($n / 2)];
                $threshold = 3.5;
                $lower = $median - $threshold * $madMedian * 1.4826;
                $upper = $median + $threshold * $madMedian * 1.4826;
                break;

            default:
                return [];
        }

        $outliers = [];
        foreach ($values as $i => $v) {
            if ($v < $lower || $v > $upper) {
                $outliers[] = ['index' => $i, 'value' => $v];
            }
        }
        return $outliers;
    }

    /**
     * Seasonal decomposition (simple moving average method).
     */
    public function seasonalDecompose(array $monthlySeries): array
    {
        $n = count($monthlySeries);
        if ($n < 12) {
            return ['trend' => $monthlySeries, 'seasonal' => array_fill(0, $n, 0), 'residual' => array_fill(0, $n, 0)];
        }

        // 12-month centered moving average for trend
        $trend = array_fill(0, $n, null);
        for ($i = 6; $i < $n - 5; $i++) {
            $sum = 0;
            for ($j = $i - 6; $j <= $i + 5; $j++) {
                $sum += $monthlySeries[$j];
            }
            $trend[$i] = $sum / 12;
        }

        // Fill edges with nearest value
        for ($i = 0; $i < 6; $i++) $trend[$i] = $trend[6] ?? $monthlySeries[$i];
        for ($i = $n - 5; $i < $n; $i++) $trend[$i] = $trend[$n - 6] ?? $monthlySeries[$i];

        // Seasonal = original - trend
        $seasonal = [];
        $residual = [];
        for ($i = 0; $i < $n; $i++) {
            $seasonal[$i] = round($monthlySeries[$i] - ($trend[$i] ?? $monthlySeries[$i]), 4);
            $residual[$i] = 0; // simplified
        }

        // Round trend
        foreach ($trend as &$t) {
            $t = $t !== null ? round($t, 4) : 0;
        }

        return ['trend' => $trend, 'seasonal' => $seasonal, 'residual' => $residual];
    }

    /**
     * Generate rule-based recommendations.
     */
    public function generateRecommendations(array $resultData, array $allEipValues): array
    {
        $suggestions = [];

        if (empty($allEipValues)) return $suggestions;

        $mean = array_sum($allEipValues) / count($allEipValues);
        $trends = $this->detectTrends($allEipValues);

        // YoY comparison
        if (count($resultData) >= 2) {
            $lastYear = end($resultData);
            $prevYear = prev($resultData);
            if ($lastYear && $prevYear) {
                $lastEip = $lastYear['yearly_eip']['eip_energy'] ?? 0;
                $prevEip = $prevYear['yearly_eip']['eip_energy'] ?? 0;
                if ($prevEip > 0) {
                    $change = (($lastEip - $prevEip) / $prevEip) * 100;
                    if ($change > 10) {
                        $suggestions[] = [
                            'type' => 'warning',
                            'icon' => 'bi-exclamation-triangle',
                            'message' => 'EIP increased by ' . round($change, 1) . '% compared to previous year. Consider investigating energy sources.',
                            'action' => ['date_preset' => 'last_12', 'trend_filters' => ['increasing']],
                        ];
                    } elseif ($change < -10) {
                        $suggestions[] = [
                            'type' => 'success',
                            'icon' => 'bi-check-circle',
                            'message' => 'EIP decreased by ' . round(abs($change), 1) . '% compared to previous year. Good performance!',
                            'action' => null,
                        ];
                    }
                }
            }
        }

        // Anomalies detected
        if (!empty($trends['anomalies'])) {
            $suggestions[] = [
                'type' => 'info',
                'icon' => 'bi-search',
                'message' => count($trends['anomalies']) . ' anomalous month(s) detected. Click to filter anomalies only.',
                'action' => ['trend_filters' => ['anomalies']],
            ];
        }

        // Increasing trend
        if ($trends['trend'] === 'increasing') {
            $suggestions[] = [
                'type' => 'warning',
                'icon' => 'bi-graph-up-arrow',
                'message' => 'Overall increasing consumption trend detected. Consider reviewing energy efficiency measures.',
                'action' => ['trend_filters' => ['increasing'], 'overlays' => ['average', 'target']],
            ];
        }

        return $suggestions;
    }

    /**
     * Calculate quick insights for live preview.
     */
    public function getQuickInsights(array $params): array
    {
        $data = $this->loadMatrixData($params);
        $totalMonths = 0;
        $totalEnergy = 0;
        $totalResource = 0;
        $eipValues = [];

        foreach ($data['data'] as $yearData) {
            foreach ($yearData['months'] as $monthData) {
                $totalMonths++;
                $totalEnergy += $monthData['total_energy'];
                $totalResource += $monthData['total_resource'];
                $eipValues[] = $monthData['eip_energy'];
            }
        }

        $avgEip = count($eipValues) > 0 ? array_sum($eipValues) / count($eipValues) : 0;
        $trends = $this->detectTrends($eipValues);

        return [
            'total_months' => $totalMonths,
            'total_energy' => round($totalEnergy, 2),
            'total_resource' => round($totalResource, 2),
            'total_combined' => round($totalEnergy + $totalResource, 2),
            'avg_eip' => round($avgEip, 4),
            'trend' => $trends['trend'],
            'trend_slope' => $trends['slope'],
            'anomaly_count' => count($trends['anomalies']),
            'peak_value' => count($eipValues) > 0 ? round(max($eipValues), 4) : 0,
        ];
    }

    /**
     * Get months belonging to selected quarters.
     */
    private function getQuarterMonths(?array $quarters): ?array
    {
        if (!$quarters) return null;

        $map = [
            'Q1' => [1, 2, 3],
            'Q2' => [4, 5, 6],
            'Q3' => [7, 8, 9],
            'Q4' => [10, 11, 12],
        ];

        $months = [];
        foreach ($quarters as $q) {
            if (isset($map[$q])) {
                $months = array_merge($months, $map[$q]);
            }
        }
        return $months ?: null;
    }

    /**
     * Get the bucket key for aggregation grouping.
     */
    private function getBucketKey(int $month, string $monthKey, string $granularity): string
    {
        $year = substr($monthKey, 0, 4);

        switch ($granularity) {
            case 'quarterly':
                $q = ceil($month / 3);
                return "Q{$q}-{$year}";
            case 'bi_annual':
                $h = $month <= 6 ? 'H1' : 'H2';
                return "{$h}-{$year}";
            case 'annual':
                return $year;
            default:
                return $monthKey;
        }
    }

    /**
     * Apply aggregation method to array of values.
     */
    private function applyMethod(array $values, string $method): float
    {
        if (empty($values)) return 0;

        switch ($method) {
            case 'avg':
                return array_sum($values) / count($values);
            case 'max':
                return max($values);
            case 'min':
                return min($values);
            case 'median':
                sort($values);
                $n = count($values);
                $mid = (int) floor($n / 2);
                return $n % 2 === 0 ? ($values[$mid - 1] + $values[$mid]) / 2 : $values[$mid];
            case 'sum':
            default:
                return array_sum($values);
        }
    }

    /**
     * Build the EIP Total summary table.
     */
    private function buildEipTotalTable(array $result): array
    {
        $energyRow = ['metric' => 'Energy EIP', 'years' => []];
        $resourceRow = ['metric' => 'Resource EIP', 'years' => []];
        $combinedRow = ['metric' => 'Combined EIP', 'years' => []];

        foreach ($result as $yearData) {
            $energyRow['years'][$yearData['year']] = $yearData['yearly_eip']['eip_energy'];
            $resourceRow['years'][$yearData['year']] = $yearData['yearly_eip']['eip_resource'];
            $combinedRow['years'][$yearData['year']] = $yearData['yearly_eip']['eip_combined'];
        }

        return [$energyRow, $resourceRow, $combinedRow];
    }

    /**
     * Get weather data for overlays.
     */
    public function getWeatherData(string $startMonth, string $endMonth): array
    {
        return EipWeatherData::where('month', '>=', $startMonth)
            ->where('month', '<=', $endMonth)
            ->orderBy('month')
            ->get()
            ->toArray();
    }

    /**
     * Convert currency amount.
     */
    public function convertCurrency(float $amountMYR, string $targetCurrency): float
    {
        if ($targetCurrency === 'MYR') return $amountMYR;

        $rate = EipCurrencyRate::where('currency_code', $targetCurrency)
            ->orderByDesc('effective_date')
            ->value('rate_to_myr');

        return $rate > 0 ? round($amountMYR / $rate, 2) : $amountMYR;
    }

    /**
     * Get targets for overlay display.
     */
    public function getTargets(int $yearStart, int $yearEnd): array
    {
        return EipTarget::whereBetween('year', [$yearStart, $yearEnd])
            ->get()
            ->groupBy('year')
            ->toArray();
    }

    /**
     * Calculate data completeness for a month.
     */
    public function calculateCompleteness(int $year, int $month, array $sourceIds): float
    {
        $expected = count($sourceIds);
        if ($expected === 0) return 100;

        $monthKey = sprintf('%d-%02d', $year, $month);
        $actual = EnergyDataUsage::where('month', $monthKey)
            ->whereIn('energy_data_id', $sourceIds)
            ->where('usage_gj', '>', 0)
            ->count();

        return round(($actual / $expected) * 100, 1);
    }
}
