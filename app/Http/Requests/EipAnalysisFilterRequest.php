<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EipAnalysisFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'year_start'           => 'required|integer',
            'year_end'             => 'required|integer|gte:year_start',
            'variable_type'        => 'required|exists:monthly_variables,id',

            // Date range controls
            'date_preset'          => 'nullable|in:last_3,last_6,last_12,ytd,custom',
            'custom_start'         => 'nullable|date_format:Y-m|required_if:date_preset,custom',
            'custom_end'           => 'nullable|date_format:Y-m|required_if:date_preset,custom',
            'quarters'             => 'nullable|array',
            'quarters.*'           => 'in:Q1,Q2,Q3,Q4',
            'compare_previous'     => 'nullable|boolean',
            'compare_last_year'    => 'nullable|boolean',

            // Source selection
            'energy_source_ids'    => 'nullable|array',
            'energy_source_ids.*'  => 'integer',
            'resource_source_ids'  => 'nullable|array',
            'resource_source_ids.*'=> 'integer',

            // Thresholds
            'min_consumption'      => 'nullable|numeric|min:0',
            'max_consumption'      => 'nullable|numeric|min:0',
            'min_cost'             => 'nullable|numeric|min:0',
            'max_cost'             => 'nullable|numeric|min:0',
            'pct_change_threshold' => 'nullable|numeric',
            'hide_zero'            => 'nullable|boolean',
            'target_exceedance'    => 'nullable|boolean',

            // Trend & anomaly
            'trend_filters'        => 'nullable|array',
            'trend_filters.*'      => 'in:increasing,decreasing,stable,anomalies,peak,low',
            'show_statistics'      => 'nullable|boolean',
            'anomaly_std_dev'      => 'nullable|numeric|min:1|max:4',

            // Granularity & aggregation
            'granularity'          => 'nullable|in:monthly,quarterly,bi_annual,annual,rolling_3,rolling_6,rolling_12',
            'aggregation_method'   => 'nullable|in:sum,avg,max,min,median',

            // Normalization & metrics
            'normalization_type'   => 'nullable|in:none,production,working_days,degree_days,area,employees,hours',
            'performance_metric'   => 'nullable|in:ei,sec,cost_per_unit,enpi',

            // Units & currency
            'energy_unit'          => 'nullable|in:J,kJ,MJ,GJ,Wh,kWh,MWh,GWh,BTU,MMBTU',
            'currency'             => 'nullable|string|max:3',

            // Chart & display
            'chart_type'           => 'nullable|in:bar,line,pie,stacked_bar,area,combo,heatmap,waterfall,box_plot',
            'overlays'             => 'nullable|array',
            'overlays.*'           => 'in:baseline,target,average,seu_threshold,best_month',
            'show_reading'         => 'nullable|boolean',
            'time_period'          => 'nullable|in:yearly,monthly',

            // Presets
            'preset_id'            => 'nullable|integer|exists:eip_filter_presets,id',

            // Export
            'export_format'        => 'nullable|in:xlsx,pdf,csv,png',
            'export_includes'      => 'nullable|array',
            'export_includes.*'    => 'in:filter_summary,chart,raw_data,statistics,benchmarks,trends',

            // Statistical (Phase 3)
            'confidence_level'     => 'nullable|in:0.90,0.95,0.99',
            'outlier_method'       => 'nullable|in:iqr,zscore,modified_zscore',
            'seasonal_adjust'      => 'nullable|boolean',

            // Scenario (Phase 3)
            'scenario_consumption_change' => 'nullable|numeric|between:-100,100',
            'scenario_projection_months'  => 'nullable|integer|between:1,24',
        ];
    }
}
