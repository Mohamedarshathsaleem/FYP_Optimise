@extends('layouts.dashboard')

@section('title', 'Utility Consumption Apportioning')

@section('content')

<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <p class="text-secondary small mb-1">Pages / Energy Review / Utility Consumption Apportioning</p>
        <h3 class="fw-bold">Utility Consumption Apportioning</h3>
    </div>
    <div class="d-flex align-items-center">
        <div class="input-group search-box me-3">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="text" class="form-control" placeholder="Search">
        </div>
        <img src="{{ asset('images/user.png') }}" alt="User" class="rounded-circle" style="width:40px;height:40px;">
    </div>
</div>

<!-- Instructions -->
<div class="card border-0 shadow-sm mb-4" id="instructionsCard" style="background:#e3f2fd; border-left:4px solid #2196f3 !important;">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-start">
            <div class="d-flex align-items-start">
                <div class="me-3">
                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width:30px;height:30px;">
                        <i class="bi bi-info-lg text-white"></i>
                    </div>
                </div>
                <div>
                    <h6 class="fw-bold text-primary mb-2">Instructions</h6>
                    <p class="mb-0 text-dark">
                        Select a <strong>Year</strong> and choose the <strong>Matrix</strong> (Table or Graph) to view utility consumption apportioning.
                        Data is auto-generated from Energy Data Management — no manual entry required.
                        Use the <strong>Filter</strong> button to customize chart types, data sources, and display options.
                    </p>
                </div>
            </div>
            <button class="btn-close" onclick="document.getElementById('instructionsCard').style.display='none'"></button>
        </div>
    </div>
</div>

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<!-- Category / Filter Section -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <div class="row g-3 align-items-end">
            <!-- Year -->
            <div class="col-md-3">
                <label class="form-label fw-bold">Category <span class="text-danger">*</span></label>
                <select id="yearSelect" class="form-select" onchange="updateContent()">
                    <option value="">Year</option>
                    @foreach($years as $y)
                        <option value="{{ $y }}" {{ (isset($year) && $year == $y) ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Matrix -->
            <div class="col-md-3">
                <label class="form-label fw-bold">&nbsp;</label>
                <select id="matrixSelect" class="form-select" onchange="updateContent()">
                    <option value="">Matrix</option>
                    <option value="Table">Table</option>
                    <option value="Graph">Graph</option>
                </select>
            </div>

            <!-- Filter button (visible in both Table and Graph mode) -->
            <div class="col-md-3">
                <label class="form-label fw-bold">&nbsp;</label>
                <button class="btn btn-primary" id="graphFilterBtn" onclick="openGraphFilterModal()" style="display:none; border-radius:25px;">
                    <i class="bi bi-funnel-fill me-2"></i>Filter
                </button>
            </div>
        </div>

        <!-- Content Area -->
        <div class="mt-4" id="contentArea">
            <div class="text-center py-5">
                <p class="text-muted" id="statusMessage">Please complete category</p>
            </div>
        </div>
    </div>
</div>

<!-- Graph Filter Modal (SEC-style with radio buttons) -->
<div class="modal fade" id="utilityGraphFilterModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 1200px;">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-body p-4">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="fw-bold mb-0" style="color: #4472C4;"><i class="bi bi-funnel me-2"></i>Filter</h4>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-secondary btn-sm" onclick="resetUtilityFilters()" title="Reset all"><i class="bi bi-arrow-counterclockwise"></i></button>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                </div>

                <!-- Tab Navigation -->
                <ul class="nav nav-tabs nav-fill mb-3" role="tablist" style="font-size:0.8rem;">
                    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#uc_tab_chart">Chart & Display</a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#uc_tab_data">Data Sources</a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#uc_tab_stats">Statistics & Trendline</a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#uc_tab_colors">Colors</a></li>
                </ul>

                <div class="tab-content" style="font-size: 0.85rem; min-height: 350px; max-height: 450px; overflow-y: auto;">

                    <!-- TAB 1: Chart & Display -->
                    <div class="tab-pane fade show active" id="uc_tab_chart">
                        <div class="row">
                            <div class="col-md-4">
                                <h6 class="fw-bold text-muted mb-3">Chart Type</h6>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="uc_chart_type" value="bar" id="uc_ct_bar" checked>
                                    <label class="form-check-label" for="uc_ct_bar"><i class="bi bi-bar-chart me-1"></i>Bar Chart</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="uc_chart_type" value="line" id="uc_ct_line">
                                    <label class="form-check-label" for="uc_ct_line"><i class="bi bi-graph-up me-1"></i>Line Chart</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="uc_chart_type" value="pie" id="uc_ct_pie">
                                    <label class="form-check-label" for="uc_ct_pie"><i class="bi bi-pie-chart me-1"></i>Pie Chart</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="uc_chart_type" value="stacked_bar" id="uc_ct_stacked">
                                    <label class="form-check-label" for="uc_ct_stacked"><i class="bi bi-bar-chart-steps me-1"></i>Stacked Bar</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="uc_chart_type" value="area" id="uc_ct_area">
                                    <label class="form-check-label" for="uc_ct_area"><i class="bi bi-graph-up me-1"></i>Area Chart</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="uc_chart_type" value="combo" id="uc_ct_combo">
                                    <label class="form-check-label" for="uc_ct_combo"><i class="bi bi-bar-chart-line me-1"></i>Combo (Bar+Line)</label>
                                </div>

                                <h6 class="fw-bold text-muted mb-3 mt-3">Time Period</h6>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="uc_time_period" value="monthly" id="uc_tp_monthly" checked>
                                    <label class="form-check-label" for="uc_tp_monthly">Monthly</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="uc_time_period" value="yearly" id="uc_tp_yearly">
                                    <label class="form-check-label" for="uc_tp_yearly">Yearly (Total)</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <h6 class="fw-bold text-muted mb-3">Display Options</h6>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="uc_show_labels">
                                    <label class="form-check-label" for="uc_show_labels">Show data labels</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="uc_show_legend" checked>
                                    <label class="form-check-label" for="uc_show_legend">Show legend</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="uc_show_grid" checked>
                                    <label class="form-check-label" for="uc_show_grid">Show grid lines</label>
                                </div>

                                <h6 class="fw-bold text-muted mb-3 mt-3">Table Options</h6>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="uc_show_avg_row" checked>
                                    <label class="form-check-label" for="uc_show_avg_row">Show average row</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="uc_show_pct_row" checked>
                                    <label class="form-check-label" for="uc_show_pct_row">Show apportioning % row</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <h6 class="fw-bold text-muted mb-3">Export</h6>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-outline-secondary btn-sm" onclick="exportChartPNG()"><i class="bi bi-image me-1"></i>PNG</button>
                                    <button class="btn btn-outline-secondary btn-sm" onclick="exportTableCSV()"><i class="bi bi-file-earmark-spreadsheet me-1"></i>CSV</button>
                                    <button class="btn btn-outline-success btn-sm" onclick="exportTableExcel()"><i class="bi bi-file-earmark-excel me-1"></i>Excel</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 2: Data Sources -->
                    <div class="tab-pane fade" id="uc_tab_data">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <button class="btn btn-outline-primary btn-sm me-2" onclick="document.querySelectorAll('.uc-data-check').forEach(function(c){c.checked=true;})">Select All</button>
                                <button class="btn btn-outline-secondary btn-sm" onclick="document.querySelectorAll('.uc-data-check').forEach(function(c){c.checked=false;})">Deselect All</button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="fw-bold text-muted mb-3"><i class="bi bi-lightning-charge me-1"></i>Energy</h6>
                                <div class="form-check mb-2">
                                    <input class="form-check-input uc-data-check" type="checkbox" id="uc_data_energy_gj" value="energy_gj" checked>
                                    <span class="d-inline-block rounded-circle me-1" style="width:10px;height:10px;background:#2563EB;"></span>
                                    <label class="form-check-label" for="uc_data_energy_gj">Energy (GJ)</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input uc-data-check" type="checkbox" id="uc_data_energy_cost" value="energy_cost">
                                    <span class="d-inline-block rounded-circle me-1" style="width:10px;height:10px;background:#D97706;"></span>
                                    <label class="form-check-label" for="uc_data_energy_cost">Energy Cost (RM)</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-bold text-muted mb-3"><i class="bi bi-droplet me-1"></i>Energy Resource</h6>
                                <div class="form-check mb-2">
                                    <input class="form-check-input uc-data-check" type="checkbox" id="uc_data_resource_gj" value="resource_gj" checked>
                                    <span class="d-inline-block rounded-circle me-1" style="width:10px;height:10px;background:#059669;"></span>
                                    <label class="form-check-label" for="uc_data_resource_gj">Energy Resource (GJ)</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input uc-data-check" type="checkbox" id="uc_data_resource_cost" value="resource_cost">
                                    <span class="d-inline-block rounded-circle me-1" style="width:10px;height:10px;background:#DC2626;"></span>
                                    <label class="form-check-label" for="uc_data_resource_cost">Energy Resource Cost (RM)</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 3: Statistics & Trendline -->
                    <div class="tab-pane fade" id="uc_tab_stats">
                        <div class="row">
                            <div class="col-md-4">
                                <h6 class="fw-bold text-muted mb-3">Statistics</h6>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="uc_show_min">
                                    <label class="form-check-label" for="uc_show_min">Show Minimum</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="uc_show_max">
                                    <label class="form-check-label" for="uc_show_max">Show Maximum</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="uc_show_avg" checked>
                                    <label class="form-check-label" for="uc_show_avg">Show Average</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <h6 class="fw-bold text-muted mb-3">Trendline</h6>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="uc_show_trendline">
                                    <label class="form-check-label" for="uc_show_trendline">Show Trendline</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="uc_show_r_squared">
                                    <label class="form-check-label" for="uc_show_r_squared">Show R² Value</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 4: Colors -->
                    <div class="tab-pane fade" id="uc_tab_colors">
                        <h6 class="fw-bold text-muted mb-3">Color Scheme</h6>
                        <div class="row g-3">
                            <div class="col-md-4 mb-3">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="uc_color_scheme" value="default" id="uc_cs_default" checked>
                                    <label class="form-check-label fw-semibold" for="uc_cs_default">Default</label>
                                </div>
                                <div class="d-flex gap-1 ms-4">
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#2563EB;"></span>
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#059669;"></span>
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#D97706;"></span>
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#DC2626;"></span>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="uc_color_scheme" value="pastel" id="uc_cs_pastel">
                                    <label class="form-check-label fw-semibold" for="uc_cs_pastel">Pastel</label>
                                </div>
                                <div class="d-flex gap-1 ms-4">
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#93C5FD;"></span>
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#6EE7B7;"></span>
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#FCD34D;"></span>
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#FCA5A5;"></span>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="uc_color_scheme" value="corporate" id="uc_cs_corporate">
                                    <label class="form-check-label fw-semibold" for="uc_cs_corporate">Corporate</label>
                                </div>
                                <div class="d-flex gap-1 ms-4">
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#1E3A5F;"></span>
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#2D6A4F;"></span>
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#B45309;"></span>
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#991B1B;"></span>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="uc_color_scheme" value="vibrant" id="uc_cs_vibrant">
                                    <label class="form-check-label fw-semibold" for="uc_cs_vibrant">Vibrant</label>
                                </div>
                                <div class="d-flex gap-1 ms-4">
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#3B82F6;"></span>
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#10B981;"></span>
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#F59E0B;"></span>
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#EF4444;"></span>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="uc_color_scheme" value="ocean" id="uc_cs_ocean">
                                    <label class="form-check-label fw-semibold" for="uc_cs_ocean">Ocean</label>
                                </div>
                                <div class="d-flex gap-1 ms-4">
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#0077B6;"></span>
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#00B4D8;"></span>
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#48CAE4;"></span>
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#90E0EF;"></span>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="uc_color_scheme" value="warm" id="uc_cs_warm">
                                    <label class="form-check-label fw-semibold" for="uc_cs_warm">Warm</label>
                                </div>
                                <div class="d-flex gap-1 ms-4">
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#E63946;"></span>
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#F4A261;"></span>
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#E9C46A;"></span>
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#2A9D8F;"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Footer Actions -->
                <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                    <button type="button" class="btn btn-outline-secondary px-4" onclick="resetUtilityFilters()" style="border-radius: 25px;">Clear All</button>
                    <button type="button" class="btn btn-primary px-5 py-2" onclick="applyGraphFilter()" style="border-radius: 25px; min-width: 200px;">Apply & View</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    #utilityGraphFilterModal .nav-tabs .nav-link { font-size: 0.8rem; padding: 0.5rem 0.75rem; }
    #utilityGraphFilterModal .nav-tabs .nav-link.active { color: #4472C4; border-bottom: 2px solid #4472C4; font-weight: 600; }
    @media (max-width: 768px) {
        #utilityGraphFilterModal .modal-dialog { max-width: 100%; margin: 0.5rem; }
        #utilityGraphFilterModal .tab-content { max-height: 60vh; }
    }
</style>

<script>
var csrfToken = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '';
var monthLabels = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

// Data from server (passed via Blade)
var serverData = @json(isset($rows) ? $rows : []);
var hasData = {{ isset($hasData) && $hasData ? 'true' : 'false' }};
var selectedYear = '{{ $year ?? '' }}';

// Summary data
var totalEnergyGj = {{ $totalEnergyGj ?? 0 }};
var totalResourceGj = {{ $totalResourceGj ?? 0 }};
var totalEnergyCost = {{ $totalEnergyCost ?? 0 }};
var totalResourceCost = {{ $totalResourceCost ?? 0 }};
var avgEnergyGj = {{ $avgEnergyGj ?? 0 }};
var avgResourceGj = {{ $avgResourceGj ?? 0 }};
var avgEnergyCost = {{ $avgEnergyCost ?? 0 }};
var avgResourceCost = {{ $avgResourceCost ?? 0 }};
var pctEnergyGj = {{ $pctEnergyGj ?? 0 }};
var pctResourceGj = {{ $pctResourceGj ?? 0 }};
var pctEnergyCost = {{ $pctEnergyCost ?? 0 }};
var pctResourceCost = {{ $pctResourceCost ?? 0 }};

// Chart instance
var utilityChart = null;

// Color schemes
var colorSchemes = {
    default: { energy_gj: '#2563EB', resource_gj: '#059669', energy_cost: '#D97706', resource_cost: '#DC2626' },
    pastel: { energy_gj: '#93C5FD', resource_gj: '#6EE7B7', energy_cost: '#FCD34D', resource_cost: '#FCA5A5' },
    corporate: { energy_gj: '#1E3A5F', resource_gj: '#2D6A4F', energy_cost: '#B45309', resource_cost: '#991B1B' },
    vibrant: { energy_gj: '#3B82F6', resource_gj: '#10B981', energy_cost: '#F59E0B', resource_cost: '#EF4444' },
    ocean: { energy_gj: '#0077B6', resource_gj: '#00B4D8', energy_cost: '#48CAE4', resource_cost: '#90E0EF' },
    warm: { energy_gj: '#E63946', resource_gj: '#F4A261', energy_cost: '#E9C46A', resource_cost: '#2A9D8F' }
};

var dataLabels = {
    energy_gj: 'Energy (GJ)',
    resource_gj: 'Energy Resource (GJ)',
    energy_cost: 'Energy Cost (RM)',
    resource_cost: 'Energy Resource Cost (RM)'
};

function formatNumber(num) {
    if (!num || num === 0) return '0.00';
    return parseFloat(num).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function formatCurrency(num) {
    if (!num || num === 0) return '0.00';
    return parseFloat(num).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

// ===== GET FILTER VALUES (SEC-style) =====
function getFilterValues() {
    var f = {};
    var chartRadio = document.querySelector('input[name="uc_chart_type"]:checked');
    f.chartType = chartRadio ? chartRadio.value : 'bar';

    var timeRadio = document.querySelector('input[name="uc_time_period"]:checked');
    f.timePeriod = timeRadio ? timeRadio.value : 'monthly';

    var colorRadio = document.querySelector('input[name="uc_color_scheme"]:checked');
    f.colorScheme = colorRadio ? colorRadio.value : 'default';

    f.showLabels = document.getElementById('uc_show_labels').checked;
    f.showLegend = document.getElementById('uc_show_legend').checked;
    f.showGrid = document.getElementById('uc_show_grid').checked;
    f.showAvgRow = document.getElementById('uc_show_avg_row').checked;
    f.showPctRow = document.getElementById('uc_show_pct_row').checked;
    f.showMin = document.getElementById('uc_show_min').checked;
    f.showMax = document.getElementById('uc_show_max').checked;
    f.showAvg = document.getElementById('uc_show_avg').checked;
    f.showTrendline = document.getElementById('uc_show_trendline').checked;
    f.showR2 = document.getElementById('uc_show_r_squared').checked;

    f.dataSources = [];
    document.querySelectorAll('.uc-data-check:checked').forEach(function(cb) {
        f.dataSources.push(cb.value);
    });
    if (f.dataSources.length === 0) f.dataSources = ['energy_gj', 'resource_gj'];

    return f;
}

// ===== PERSIST MATRIX IN SESSION STORAGE =====
window.updateContent = function() {
    var year = document.getElementById('yearSelect').value;
    var matrix = document.getElementById('matrixSelect').value;

    if (matrix) sessionStorage.setItem('utilityMatrixSelect', matrix);

    if (!year) {
        document.getElementById('contentArea').innerHTML = '<div class="text-center py-5"><p class="text-muted">Please complete category</p></div>';
        document.getElementById('graphFilterBtn').style.display = 'none';
        return;
    }
    if (!matrix) {
        document.getElementById('contentArea').innerHTML = '<div class="text-center py-5"><p class="text-muted">Please choose matrix</p></div>';
        document.getElementById('graphFilterBtn').style.display = 'none';
        return;
    }

    if (year !== selectedYear) {
        window.location.href = '/utility-apportioning?year=' + year;
        return;
    }

    if (!hasData) {
        document.getElementById('contentArea').innerHTML = '<div class="alert alert-info text-center py-4"><i class="bi bi-info-circle me-2"></i>No energy data found for <strong>' + year + '</strong>. Please ensure energy data has been entered in Energy Data Management.</div>';
        document.getElementById('graphFilterBtn').style.display = 'none';
        return;
    }

    // Show filter button in both Table and Graph mode
    document.getElementById('graphFilterBtn').style.display = '';

    if (matrix === 'Table') {
        displayTable();
    } else if (matrix === 'Graph') {
        displayGraph();
    }
};

// ===== TABLE VIEW =====
window.displayTable = function() {
    var f = getFilterValues();
    var colors = colorSchemes[f.colorScheme] || colorSchemes['default'];
    var html = '';

    // Determine visible columns based on data source filter
    var showEnergyGj = f.dataSources.indexOf('energy_gj') !== -1;
    var showResourceGj = f.dataSources.indexOf('resource_gj') !== -1;
    var showEnergyCost = f.dataSources.indexOf('energy_cost') !== -1;
    var showResourceCost = f.dataSources.indexOf('resource_cost') !== -1;

    var colCount = 1; // Month
    if (showEnergyGj) colCount++;
    if (showResourceGj) colCount++;
    if (showEnergyCost) colCount++;
    if (showResourceCost) colCount++;

    html += '<div class="card border-0 shadow-sm">';
    html += '<div class="card-header bg-white d-flex align-items-center justify-content-between px-4 py-3">';
    html += '<div><h6 class="fw-bold mb-0">Utility Consumption Apportioning Table</h6>';
    html += '<small class="text-muted">Monthly energy consumption and cost breakdown for ' + selectedYear + '</small></div>';
    html += '</div>';

    html += '<div class="table-responsive">';
    html += '<table class="table table-hover align-middle mb-0" id="utilityTable">';

    // Header
    html += '<thead>';
    html += '<tr style="background: linear-gradient(135deg, #4472C4 0%, #2E5AA5 100%); color: white;">';
    html += '<th rowspan="2" class="text-center py-3" style="min-width:80px;">Month</th>';

    var usageCols = 0;
    var costCols = 0;
    if (showEnergyGj) usageCols++;
    if (showResourceGj) usageCols++;
    if (showEnergyCost) costCols++;
    if (showResourceCost) costCols++;

    if (usageCols > 0) html += '<th colspan="' + usageCols + '" class="text-center py-3">Monthly Usage (GJ)</th>';
    if (costCols > 0) html += '<th colspan="' + costCols + '" class="text-center py-3">Monthly Cost (RM)</th>';
    html += '</tr>';

    html += '<tr style="background: rgba(68,114,196,0.7); color: white;">';
    if (showEnergyGj) html += '<th class="text-center py-2" style="min-width:120px;">Energy</th>';
    if (showResourceGj) html += '<th class="text-center py-2" style="min-width:120px;">Energy Resource</th>';
    if (showEnergyCost) html += '<th class="text-center py-2" style="min-width:120px;">Energy</th>';
    if (showResourceCost) html += '<th class="text-center py-2" style="min-width:120px;">Energy Resource</th>';
    html += '</tr></thead><tbody>';

    serverData.forEach(function(row) {
        html += '<tr>';
        html += '<td class="text-center fw-semibold py-2">' + row.month + '</td>';
        if (showEnergyGj) html += '<td class="text-end py-2">' + formatNumber(row.energy_gj) + '</td>';
        if (showResourceGj) html += '<td class="text-end py-2">' + formatNumber(row.resource_gj) + '</td>';
        if (showEnergyCost) html += '<td class="text-end py-2">' + formatCurrency(row.energy_cost) + '</td>';
        if (showResourceCost) html += '<td class="text-end py-2">' + formatCurrency(row.resource_cost) + '</td>';
        html += '</tr>';
    });

    // Average row
    if (f.showAvgRow) {
        html += '<tr style="background: #f8f9fa;">';
        html += '<td class="fw-bold py-2 text-center">Average Usage</td>';
        if (showEnergyGj) html += '<td class="text-end fw-bold py-2">' + formatNumber(avgEnergyGj) + '</td>';
        if (showResourceGj) html += '<td class="text-end fw-bold py-2">' + formatNumber(avgResourceGj) + '</td>';
        if (showEnergyCost) html += '<td class="text-end fw-bold py-2">' + formatCurrency(avgEnergyCost) + '</td>';
        if (showResourceCost) html += '<td class="text-end fw-bold py-2">' + formatCurrency(avgResourceCost) + '</td>';
        html += '</tr>';
    }

    // Apportioning % row
    if (f.showPctRow) {
        html += '<tr style="background: #e8f4f8;">';
        html += '<td class="fw-bold py-2 text-center">Apportioning (%)</td>';
        if (showEnergyGj) html += '<td class="text-end fw-bold py-2">' + pctEnergyGj.toFixed(2) + '%</td>';
        if (showResourceGj) html += '<td class="text-end fw-bold py-2">' + pctResourceGj.toFixed(2) + '%</td>';
        if (showEnergyCost) html += '<td class="text-end fw-bold py-2">' + pctEnergyCost.toFixed(2) + '%</td>';
        if (showResourceCost) html += '<td class="text-end fw-bold py-2">' + pctResourceCost.toFixed(2) + '%</td>';
        html += '</tr>';
    }

    html += '</tbody></table></div></div>';

    document.getElementById('contentArea').innerHTML = html;
};

// ===== GRAPH VIEW =====
window.displayGraph = function() {
    var html = '';

    html += '<div class="card border-0 shadow-sm">';
    html += '<div class="card-header bg-white d-flex align-items-center justify-content-between px-4 py-3">';
    html += '<div><h6 class="fw-bold mb-0" id="chartTitle">Utility Consumption Apportioning</h6>';
    html += '<small class="text-muted">Year: ' + selectedYear + '</small></div>';
    html += '</div>';
    html += '<div class="card-body p-4">';
    html += '<div style="position:relative;height:450px;"><canvas id="utilityChartCanvas"></canvas></div>';
    html += '</div></div>';

    document.getElementById('contentArea').innerHTML = html;

    setTimeout(function() {
        renderUtilityChart();
    }, 50);
};

// ===== RENDER CHART =====
function renderUtilityChart() {
    var canvas = document.getElementById('utilityChartCanvas');
    if (!canvas) return;
    if (utilityChart) utilityChart.destroy();

    var f = getFilterValues();
    var colors = colorSchemes[f.colorScheme] || colorSchemes['default'];
    var selectedSources = f.dataSources;

    var isDoughnut = (f.chartType === 'pie');
    var isStacked = (f.chartType === 'stacked_bar');
    var isArea = (f.chartType === 'area');
    var isCombo = (f.chartType === 'combo');
    var isLine = (f.chartType === 'line');

    var datasets = [];

    if (isDoughnut) {
        // Pie/Doughnut chart — show totals as slices
        var doughnutData = [];
        var doughnutLabels = [];
        var doughnutColors = [];

        if (f.timePeriod === 'yearly') {
            selectedSources.forEach(function(src) {
                var total = 0;
                serverData.forEach(function(r) { total += (r[src] || 0); });
                doughnutData.push(total);
                doughnutLabels.push(dataLabels[src]);
                doughnutColors.push(colors[src]);
            });
        } else {
            selectedSources.forEach(function(src) {
                var total = 0;
                serverData.forEach(function(r) { total += (r[src] || 0); });
                doughnutData.push(total);
                doughnutLabels.push(dataLabels[src]);
                doughnutColors.push(colors[src]);
            });
        }

        utilityChart = new Chart(canvas.getContext('2d'), {
            type: 'doughnut',
            data: { labels: doughnutLabels, datasets: [{ data: doughnutData, backgroundColor: doughnutColors, borderWidth: 2, borderColor: '#fff' }] },
            plugins: f.showLabels ? [ChartDataLabels] : [],
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: f.showLegend, position: 'bottom', labels: { padding: 15, font: { size: 12 } } },
                    datalabels: f.showLabels ? {
                        color: '#fff', font: { weight: 'bold', size: 14 },
                        formatter: function(value, ctx) {
                            var total = ctx.dataset.data.reduce(function(a, b) { return a + b; }, 0);
                            return total > 0 ? (value / total * 100).toFixed(1) + '%' : '';
                        }
                    } : { display: false }
                }
            }
        });
    } else {
        // Bar / Line / Area / Stacked / Combo
        var labels = f.timePeriod === 'yearly' ? [selectedYear] : monthLabels;

        selectedSources.forEach(function(src, idx) {
            var values;
            if (f.timePeriod === 'yearly') {
                var total = 0;
                serverData.forEach(function(r) { total += (r[src] || 0); });
                values = [total];
            } else {
                values = serverData.map(function(r) { return r[src] || 0; });
            }

            var actualType = isCombo ? (idx === 0 ? 'bar' : 'line') : undefined;
            var fillVal = isArea;

            var ds = {
                label: dataLabels[src],
                data: values,
                backgroundColor: colors[src] + ((isLine || isArea) ? '33' : 'E6'),
                borderColor: colors[src],
                borderWidth: (isLine || isArea || isCombo) ? 2 : 1,
                borderRadius: (!isLine && !isArea) ? 4 : 0,
                tension: 0.3,
                fill: fillVal
            };
            if (actualType) ds.type = actualType;
            datasets.push(ds);
        });

        // Statistical overlays
        if (f.timePeriod === 'monthly' && (f.showMin || f.showMax || f.showAvg || f.showTrendline)) {
            var firstValues = datasets.length > 0 ? datasets[0].data : [];
            var nonZero = firstValues.filter(function(v) { return v > 0; });

            if (nonZero.length > 0) {
                var minVal = Math.min.apply(null, nonZero);
                var maxVal = Math.max.apply(null, firstValues);
                var avgVal = firstValues.reduce(function(a, b) { return a + b; }, 0) / firstValues.length;

                if (f.showMin) {
                    datasets.push({ label: 'Min (' + formatNumber(minVal) + ')', data: Array(labels.length).fill(minVal), type: 'line', borderColor: '#17A2B8', borderDash: [4, 4], borderWidth: 1.5, pointRadius: 0, fill: false });
                }
                if (f.showMax) {
                    datasets.push({ label: 'Max (' + formatNumber(maxVal) + ')', data: Array(labels.length).fill(maxVal), type: 'line', borderColor: '#DC3545', borderDash: [4, 4], borderWidth: 1.5, pointRadius: 0, fill: false });
                }
                if (f.showAvg) {
                    datasets.push({ label: 'Avg (' + formatNumber(avgVal) + ')', data: Array(labels.length).fill(avgVal), type: 'line', borderColor: '#28A745', borderDash: [6, 3], borderWidth: 2, pointRadius: 0, fill: false });
                }
                if (f.showTrendline && firstValues.length >= 2) {
                    var n = firstValues.length, sx = 0, sy = 0, sxy = 0, sx2 = 0;
                    for (var i = 0; i < n; i++) { sx += i; sy += firstValues[i]; sxy += i * firstValues[i]; sx2 += i * i; }
                    var slope = (n * sxy - sx * sy) / Math.max(n * sx2 - sx * sx, 1);
                    var intercept = (sy - slope * sx) / n;
                    var trendData = [];
                    var ssTot = 0, ssRes = 0, mean = sy / n;
                    for (var i = 0; i < n; i++) {
                        var predicted = slope * i + intercept;
                        trendData.push(parseFloat(predicted.toFixed(2)));
                        ssTot += Math.pow(firstValues[i] - mean, 2);
                        ssRes += Math.pow(firstValues[i] - predicted, 2);
                    }
                    var r2 = ssTot > 0 ? (1 - ssRes / ssTot) : 0;
                    var trendLabel = 'Trendline' + (f.showR2 ? ' (R²=' + r2.toFixed(4) + ')' : '');
                    datasets.push({ label: trendLabel, data: trendData, type: 'line', borderColor: '#FFC107', borderDash: [8, 4], borderWidth: 2, pointRadius: 0, fill: false });
                }
            }
        }

        var actualChartType = isStacked ? 'bar' : (isArea ? 'line' : (isCombo ? 'bar' : (isLine ? 'line' : 'bar')));

        utilityChart = new Chart(canvas.getContext('2d'), {
            type: actualChartType,
            data: { labels: labels, datasets: datasets },
            plugins: f.showLabels ? [ChartDataLabels] : [],
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: f.showLegend, position: 'top', labels: { padding: 15, font: { size: 12 } } },
                    title: { display: true, text: 'Utility Consumption Apportioning (' + selectedYear + ')', font: { size: 16, weight: 'bold' } },
                    datalabels: f.showLabels ? {
                        anchor: 'end', align: 'top', font: { size: 9 },
                        formatter: function(v) { return v > 0 ? formatNumber(v) : ''; }
                    } : { display: false }
                },
                scales: {
                    y: { beginAtZero: true, title: { display: true, text: 'Value' }, grid: { display: f.showGrid }, stacked: isStacked },
                    x: { title: { display: true, text: f.timePeriod === 'monthly' ? 'Month' : 'Year' }, grid: { display: false }, stacked: isStacked }
                }
            }
        });
    }

    // Update chart title
    var titleEl = document.getElementById('chartTitle');
    if (titleEl) {
        var chartRadio = document.querySelector('input[name="uc_chart_type"]:checked');
        var chartTypeLabel = chartRadio ? chartRadio.parentElement.querySelector('label').textContent.trim() : 'Bar Chart';
        titleEl.textContent = 'Utility Consumption Apportioning - ' + chartTypeLabel;
    }
}

// ===== FILTER MODAL =====
function openGraphFilterModal() {
    var modal = new bootstrap.Modal(document.getElementById('utilityGraphFilterModal'));
    modal.show();
}

function applyGraphFilter() {
    var modal = bootstrap.Modal.getInstance(document.getElementById('utilityGraphFilterModal'));
    if (modal) modal.hide();

    var matrix = document.getElementById('matrixSelect').value;
    if (matrix === 'Table') {
        displayTable();
    } else if (matrix === 'Graph') {
        displayGraph();
    }
}

function resetUtilityFilters() {
    document.getElementById('uc_ct_bar').checked = true;
    document.getElementById('uc_tp_monthly').checked = true;
    document.getElementById('uc_cs_default').checked = true;
    document.getElementById('uc_show_labels').checked = false;
    document.getElementById('uc_show_legend').checked = true;
    document.getElementById('uc_show_grid').checked = true;
    document.getElementById('uc_show_avg_row').checked = true;
    document.getElementById('uc_show_pct_row').checked = true;
    document.getElementById('uc_show_min').checked = false;
    document.getElementById('uc_show_max').checked = false;
    document.getElementById('uc_show_avg').checked = true;
    document.getElementById('uc_show_trendline').checked = false;
    document.getElementById('uc_show_r_squared').checked = false;
    document.getElementById('uc_data_energy_gj').checked = true;
    document.getElementById('uc_data_resource_gj').checked = true;
    document.getElementById('uc_data_energy_cost').checked = false;
    document.getElementById('uc_data_resource_cost').checked = false;
}

// ===== EXPORT =====
window.exportTableCSV = function() {
    var f = getFilterValues();
    var headers = ['Month'];
    if (f.dataSources.indexOf('energy_gj') !== -1) headers.push('Energy (GJ)');
    if (f.dataSources.indexOf('resource_gj') !== -1) headers.push('Energy Resource (GJ)');
    if (f.dataSources.indexOf('energy_cost') !== -1) headers.push('Energy Cost (RM)');
    if (f.dataSources.indexOf('resource_cost') !== -1) headers.push('Energy Resource Cost (RM)');

    var rows = [headers];
    serverData.forEach(function(r) {
        var row = [r.month];
        if (f.dataSources.indexOf('energy_gj') !== -1) row.push(r.energy_gj);
        if (f.dataSources.indexOf('resource_gj') !== -1) row.push(r.resource_gj);
        if (f.dataSources.indexOf('energy_cost') !== -1) row.push(r.energy_cost);
        if (f.dataSources.indexOf('resource_cost') !== -1) row.push(r.resource_cost);
        rows.push(row);
    });

    var csv = rows.map(function(r) { return r.join(','); }).join('\n');
    var blob = new Blob([csv], { type: 'text/csv' });
    var a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = 'utility-apportioning-' + selectedYear + '.csv';
    a.click();
};

window.exportTableExcel = function() {
    var table = document.getElementById('utilityTable');
    if (!table) {
        // If in graph mode, export data as Excel
        var f = getFilterValues();
        var html = '<html><head><meta charset="UTF-8"></head><body><table border="1">';
        html += '<tr><th>Month</th>';
        f.dataSources.forEach(function(src) { html += '<th>' + dataLabels[src] + '</th>'; });
        html += '</tr>';
        serverData.forEach(function(r) {
            html += '<tr><td>' + r.month + '</td>';
            f.dataSources.forEach(function(src) { html += '<td>' + (r[src] || 0) + '</td>'; });
            html += '</tr>';
        });
        html += '</table></body></html>';
        var blob = new Blob([html], { type: 'application/vnd.ms-excel' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'utility-apportioning-' + selectedYear + '.xls';
        a.click();
        return;
    }
    var html = '<html><head><meta charset="UTF-8"></head><body>' + table.outerHTML + '</body></html>';
    var blob = new Blob([html], { type: 'application/vnd.ms-excel' });
    var a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = 'utility-apportioning-' + selectedYear + '.xls';
    a.click();
};

window.exportChartPNG = function() {
    var canvas = document.getElementById('utilityChartCanvas');
    if (!canvas) return;
    var a = document.createElement('a');
    a.href = canvas.toDataURL('image/png');
    a.download = 'utility-apportioning-' + selectedYear + '.png';
    a.click();
};

// ===== RESTORE MATRIX FROM SESSION STORAGE ON PAGE LOAD =====
document.addEventListener('DOMContentLoaded', function() {
    var savedMatrix = sessionStorage.getItem('utilityMatrixSelect');
    if (savedMatrix) {
        document.getElementById('matrixSelect').value = savedMatrix;
    }
    var year = document.getElementById('yearSelect').value;
    var matrix = document.getElementById('matrixSelect').value;
    if (year && matrix) {
        updateContent();
    }
});
</script>

@endsection
