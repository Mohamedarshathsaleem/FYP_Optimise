@extends('layouts.dashboard')

@section('title', 'SEC Analysis')


@section('page-title', 'SEC Analysis')
@section('page-title-main', 'SEC Analysis')

@section('content')


@include('partials._header-dashboard')


<!-- Instructions Section -->
<div class="card border-0 shadow-sm mb-4" id="instructionsCard" style="background: #e3f2fd; border-left: 4px solid #2196f3;">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-start">
            <div class="d-flex align-items-start">
                <div class="me-3">
                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                        <i class="bi bi-info-lg text-white"></i>
                    </div>
                </div>
                <div>
                    <h6 class="fw-bold text-primary mb-2">Instructions</h6>
                    <p class="mb-0 text-dark">1. To analyze SEC, complete the desired year range, insert POE, and define the display matrix as either a table or a graph. Use the Filter button to control which energy types are displayed.</p>
                </div>
            </div>
            <button class="btn-close" onclick="closeInstructions()"></button>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <div class="row">
            <!-- Category (Year Start) -->
            <div class="col-md-3 mb-3">
                <label class="form-label fw-bold">Category <span class="text-danger">*</span></label>
                <select class="form-select" id="categorySelect" onchange="updateContent()">
                    <option value="">Start year</option>
                    @foreach($years as $year)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Year End -->
            <div class="col-md-3 mb-3">
                <label class="form-label fw-bold">&nbsp;</label>
                <select class="form-select" id="yearEndSelect" onchange="updateContent()">
                    <option value="">End year</option>
                    @foreach(array_reverse($years) as $year)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Insert POE -->
            <div class="col-md-3 mb-3">
                <label class="form-label fw-bold">&nbsp;</label>
                <select class="form-select" id="poeSelect" onchange="handlePOESelect()">
                    <option value="" selected>Insert POE</option>
                    <option value="Production">Production</option>
                    <option value="Sales">Sales</option>
                    <option value="Output">Output</option>
                </select>
            </div>

            <!-- Matrix -->
            <div class="col-md-3 mb-3">
                <label class="form-label fw-bold">&nbsp;</label>
                <select class="form-select" id="matrixSelect" onchange="updateContent()">
                    <option value="" selected>Matrix</option>
                    <option value="Table">Table</option>
                    <option value="Graph">Graph</option>
                </select>
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

<!-- Insert POE Modal (Supports Yearly or Monthly) -->
<div class="modal fade" id="poeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header border-0 pb-2">
                <h5 class="modal-title fw-bold text-dark">Insert POE <span id="poeYear"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- POE Mode Toggle -->
                <div class="d-flex gap-3 mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="poeMode" id="poeModeYearly" value="yearly" checked onchange="togglePoeMode()">
                        <label class="form-check-label fw-bold" for="poeModeYearly">Yearly POE (same % for all months)</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="poeMode" id="poeModeMonthly" value="monthly" onchange="togglePoeMode()">
                        <label class="form-check-label fw-bold" for="poeModeMonthly">Monthly POE (different % per month)</label>
                    </div>
                </div>

                <!-- Yearly POE Table -->
                <div id="poeYearlySection">
                    <div class="table-responsive">
                        <table class="table">
                            <thead style="background: linear-gradient(135deg, #4472C4 0%, #2E5AA5 100%); color: white;">
                                <tr>
                                    <th class="py-3">Product</th>
                                    <th class="py-3">Reading percentage (%)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($products as $product)
                                <tr>
                                    <td class="py-2">{{ $product->name }}</td>
                                    <td class="py-2">
                                        <input type="number" class="form-control poe-input"
                                               data-product-id="{{ $product->id }}"
                                               data-product-name="{{ $product->name }}"
                                               placeholder="0" min="0" max="100" step="0.01"
                                               oninput="updatePoeTotal()">
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-between mt-2 px-2">
                        <span class="fw-bold">Total:</span>
                        <span id="poeTotalDisplay" class="fw-bold">0%</span>
                    </div>
                </div>

                <!-- Monthly POE Table (hidden by default) -->
                <div id="poeMonthlySection" style="display:none;">
                    <small class="text-muted mb-2 d-block">Enter POE percentage for each product per month. Each month's total must equal 100%.</small>
                    <div class="table-responsive" style="max-height:400px;overflow-y:auto;">
                        <table class="table table-sm table-bordered" id="monthlyPoeTable">
                            <thead class="sticky-top" style="background: linear-gradient(135deg, #4472C4 0%, #2E5AA5 100%); color: white;">
                                <tr>
                                    <th class="py-2" style="min-width:100px;">Month</th>
                                    @foreach($products as $product)
                                    <th class="py-2 text-center" style="min-width:90px;">{{ $product->name }}</th>
                                    @endforeach
                                    <th class="py-2 text-center" style="min-width:70px;">Total</th>
                                </tr>
                            </thead>
                            <tbody id="monthlyPoeBody">
                                <!-- Populated dynamically -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="poeValidationMsg" class="text-danger small mt-1 text-center" style="display:none;">
                    POE must sum to exactly 100%
                </div>

                <div class="text-center mt-4">
                    <button type="button" class="btn btn-primary px-5 py-2" id="calculateSecBtn" onclick="calculateSEC()" style="border-radius: 10px; min-width: 200px;" disabled>Calculate SEC</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Graph Filter Modal -->
<div class="modal fade" id="graphFilterModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 1200px;">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-body p-4">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="fw-bold mb-0" style="color: #4472C4;"><i class="bi bi-funnel me-2"></i>Filter</h4>
                    <div class="d-flex align-items-center flex-grow-1 mx-4">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" placeholder="Search filters..." id="secFilterSearch" oninput="searchSecFilters(this.value)">
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-secondary btn-sm" onclick="resetAllSecFilters()" title="Reset all"><i class="bi bi-arrow-counterclockwise"></i></button>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                </div>

                <!-- Quick Insights Preview -->
                <div id="secInsightPanel" class="alert alert-light py-2 px-3 mb-3" style="font-size:0.8rem; border:1px solid #e0e0e0; border-radius:10px;">
                    <span class="text-muted"><i class="bi bi-lightbulb me-1"></i>Select filters and click Apply to see results</span>
                </div>

                <!-- Tab Navigation -->
                <ul class="nav nav-tabs nav-fill mb-3" id="secFilterTabs" role="tablist" style="font-size:0.8rem;">
                    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#sec_tab_chart">Chart & Display</a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#sec_tab_data">Data Sources</a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#sec_tab_stats">Statistics & Trendline</a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#sec_tab_colors">Colors</a></li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content" style="font-size: 0.85rem; min-height: 350px; max-height: 450px; overflow-y: auto;">

                    <!-- ========== TAB 1: CHART & DISPLAY ========== -->
                    <div class="tab-pane fade show active" id="sec_tab_chart">
                        <div class="row">
                            <div class="col-md-4">
                                <h6 class="fw-bold text-muted mb-3">Chart Type</h6>
                                @php $secChartTypes = [
                                    'bar'         => ['Bar Chart',       'bi-bar-chart'],
                                    'line'        => ['Line Chart',      'bi-graph-up'],
                                    'pie'         => ['Pie Chart',       'bi-pie-chart'],
                                    'stacked_bar' => ['Stacked Bar',     'bi-bar-chart-steps'],
                                    'area'        => ['Area Chart',      'bi-graph-up'],
                                    'combo'       => ['Combo (Bar+Line)','bi-bar-chart-line'],
                                ]; @endphp
                                @foreach($secChartTypes as $key => $info)
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="sec_chart_type" value="{{ $key }}" id="sec_ct_{{ $key }}" {{ $key === 'bar' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="sec_ct_{{ $key }}"><i class="bi {{ $info[1] }} me-1"></i>{{ $info[0] }}</label>
                                </div>
                                @endforeach

                                <h6 class="fw-bold text-muted mb-3 mt-3">Time Period</h6>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="sec_time_period" value="yearly" id="sec_tp_yearly" checked>
                                    <label class="form-check-label" for="sec_tp_yearly">Yearly</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="sec_time_period" value="monthly" id="sec_tp_monthly">
                                    <label class="form-check-label" for="sec_tp_monthly">Monthly</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <h6 class="fw-bold text-muted mb-3">Year Selection</h6>
                                <div class="d-flex flex-wrap gap-2 mb-2">
                                    @foreach($years as $year)
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input gf-year" type="checkbox" value="{{ $year }}" id="gf_year_{{ $year }}" checked>
                                        <label class="form-check-label" for="gf_year_{{ $year }}">{{ $year }}</label>
                                    </div>
                                    @endforeach
                                </div>
                                <div class="mb-3">
                                    <a href="#" class="small text-primary" onclick="event.preventDefault();document.querySelectorAll('.gf-year').forEach(function(c){c.checked=true;})">Select All</a>
                                    | <a href="#" class="small text-primary" onclick="event.preventDefault();document.querySelectorAll('.gf-year').forEach(function(c){c.checked=false;})">Clear All</a>
                                </div>

                                <h6 class="fw-bold text-muted mb-3">Energy Unit</h6>
                                @php $secUnits = [
                                    'GJ'    => 'Gigajoule (GJ)',
                                    'MJ'    => 'Megajoule (MJ)',
                                    'kWh'   => 'Kilowatt-hour (kWh)',
                                    'MWh'   => 'Megawatt-hour (MWh)',
                                    'BTU'   => 'British Thermal Unit (BTU)',
                                    'MMBTU' => 'Million BTU (MMBTU)',
                                ]; @endphp
                                @foreach($secUnits as $key => $label)
                                <div class="form-check mb-1">
                                    <input class="form-check-input" type="radio" name="gf_energy_unit" value="{{ $key }}" id="gf_unit_{{ $key }}" {{ $key === 'GJ' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="gf_unit_{{ $key }}">{{ $label }}</label>
                                </div>
                                @endforeach
                            </div>
                            <div class="col-md-4">
                                <h6 class="fw-bold text-muted mb-3">Display Options</h6>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="sec_show_reading">
                                    <label class="form-check-label" for="sec_show_reading">Show data labels</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="sec_show_legend" checked>
                                    <label class="form-check-label" for="sec_show_legend">Show legend</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="sec_show_grid" checked>
                                    <label class="form-check-label" for="sec_show_grid">Show grid lines</label>
                                </div>

                                <h6 class="fw-bold text-muted mb-3 mt-3">Export</h6>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-outline-secondary btn-sm" onclick="exportSecChartPNG()"><i class="bi bi-image me-1"></i>PNG</button>
                                    <button class="btn btn-outline-secondary btn-sm" onclick="exportSecCSV()"><i class="bi bi-file-earmark-spreadsheet me-1"></i>CSV</button>
                                    <button class="btn btn-outline-success btn-sm" onclick="exportSecExcel()"><i class="bi bi-file-earmark-excel me-1"></i>Excel</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ========== TAB 2: DATA SOURCES ========== -->
                    <div class="tab-pane fade" id="sec_tab_data">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <button class="btn btn-outline-primary btn-sm me-2" onclick="document.querySelectorAll('.gf-product,.gf-energy-source').forEach(function(c){c.checked=true;updateSecEnergyCount();})">Select All</button>
                                <button class="btn btn-outline-secondary btn-sm" onclick="document.querySelectorAll('.gf-product,.gf-energy-source').forEach(function(c){c.checked=false;updateSecEnergyCount();})">Deselect All</button>
                            </div>
                            <span class="badge bg-primary" id="secEnergySelectedCount">0 selected</span>
                        </div>
                        <input type="text" class="form-control form-control-sm mb-3" placeholder="Search data sources..." oninput="filterSecEnergySources(this.value)">
                        <div class="row">
                            <div class="col-md-4">
                                <h6 class="fw-bold text-muted mb-3"><i class="bi bi-box me-1"></i>Products</h6>
                                @foreach($products as $product)
                                <div class="form-check mb-2 sec-energy-item">
                                    <input class="form-check-input gf-product" type="checkbox" value="{{ $product->id }}" id="gf_prod_{{ $product->id }}" checked>
                                    <label class="form-check-label" for="gf_prod_{{ $product->id }}">{{ $product->name }}</label>
                                </div>
                                @endforeach
                                <div class="form-check mb-2">
                                    <input class="form-check-input gf-product" type="checkbox" value="total" id="gf_prod_total" checked>
                                    <label class="form-check-label" for="gf_prod_total"><em>Total Output</em></label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <h6 class="fw-bold text-muted mb-3"><i class="bi bi-lightning-charge me-1"></i>Energy</h6>
                                @foreach($energySources as $idx => $es)
                                <div class="form-check mb-2 sec-energy-item">
                                    <input class="form-check-input gf-energy-source" type="checkbox" value="energy_{{ $es->id }}" id="gf_es_{{ $es->id }}" checked onchange="updateSecEnergyCount()">
                                    <span class="d-inline-block rounded-circle me-1" style="width:10px;height:10px;background:{{ ['#2563EB','#DC2626','#059669','#D97706','#7C3AED','#DB2777'][$idx % 6] }};"></span>
                                    <label class="form-check-label" for="gf_es_{{ $es->id }}">{{ $es->energy_type }}</label>
                                </div>
                                @endforeach
                            </div>
                            <div class="col-md-4">
                                <h6 class="fw-bold text-muted mb-3"><i class="bi bi-droplet me-1"></i>Energy Resource</h6>
                                @foreach($resourceSources as $idx => $rs)
                                <div class="form-check mb-2 sec-energy-item">
                                    <input class="form-check-input gf-energy-source" type="checkbox" value="resource_{{ $rs->id }}" id="gf_rs_{{ $rs->id }}" checked onchange="updateSecEnergyCount()">
                                    <span class="d-inline-block rounded-circle me-1" style="width:10px;height:10px;background:{{ ['#0891B2','#EA580C','#4F46E5','#16A34A','#CA8A04','#9333EA'][$idx % 6] }};"></span>
                                    <label class="form-check-label" for="gf_rs_{{ $rs->id }}">{{ $rs->resource_type }}</label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- ========== TAB 3: STATISTICS & TRENDLINE ========== -->
                    <div class="tab-pane fade" id="sec_tab_stats">
                        <div class="row">
                            <div class="col-md-4">
                                <h6 class="fw-bold text-muted mb-3">Statistics</h6>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="gf_show_min">
                                    <label class="form-check-label" for="gf_show_min">Show Minimum</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="gf_show_max">
                                    <label class="form-check-label" for="gf_show_max">Show Maximum</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="gf_show_avg" checked>
                                    <label class="form-check-label" for="gf_show_avg">Show Average</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <h6 class="fw-bold text-muted mb-3">Trendline</h6>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="gf_show_trendline">
                                    <label class="form-check-label" for="gf_show_trendline">Show Trendline</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="gf_show_r_squared">
                                    <label class="form-check-label" for="gf_show_r_squared">Show R² Value</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <h6 class="fw-bold text-muted mb-3">X-Axis Selector</h6>
                                <select class="form-select form-select-sm" id="gf_x_axis">
                                    <option value="month">Month (default)</option>
                                    @foreach($products as $product)
                                    <option value="prod_{{ $product->id }}">{{ $product->name }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted d-block mt-2">Select what to plot on X-axis</small>
                            </div>
                        </div>
                    </div>

                    <!-- ========== TAB 4: COLORS ========== -->
                    <div class="tab-pane fade" id="sec_tab_colors">
                        <h6 class="fw-bold text-muted mb-3">Color Scheme</h6>
                        <div class="row g-3">
                            <div class="col-md-4 mb-3">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="sec_color_scheme" value="default" id="sec_cs_default" checked>
                                    <label class="form-check-label fw-semibold" for="sec_cs_default">Default</label>
                                </div>
                                <div class="d-flex gap-1 ms-4">
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#2563EB;"></span>
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#DC2626;"></span>
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#059669;"></span>
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#D97706;"></span>
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#7C3AED;"></span>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="sec_color_scheme" value="pastel" id="sec_cs_pastel">
                                    <label class="form-check-label fw-semibold" for="sec_cs_pastel">Pastel</label>
                                </div>
                                <div class="d-flex gap-1 ms-4">
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#93C5FD;"></span>
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#FCA5A5;"></span>
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#6EE7B7;"></span>
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#FCD34D;"></span>
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#C4B5FD;"></span>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="sec_color_scheme" value="corporate" id="sec_cs_corporate">
                                    <label class="form-check-label fw-semibold" for="sec_cs_corporate">Corporate</label>
                                </div>
                                <div class="d-flex gap-1 ms-4">
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#1E3A5F;"></span>
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#4472C4;"></span>
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#70AD47;"></span>
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#ED7D31;"></span>
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#FFC000;"></span>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="sec_color_scheme" value="vibrant" id="sec_cs_vibrant">
                                    <label class="form-check-label fw-semibold" for="sec_cs_vibrant">Vibrant</label>
                                </div>
                                <div class="d-flex gap-1 ms-4">
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#FF6B6B;"></span>
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#4ECDC4;"></span>
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#45B7D1;"></span>
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#96CEB4;"></span>
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#FFEAA7;"></span>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="sec_color_scheme" value="ocean" id="sec_cs_ocean">
                                    <label class="form-check-label fw-semibold" for="sec_cs_ocean">Ocean</label>
                                </div>
                                <div class="d-flex gap-1 ms-4">
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#0077B6;"></span>
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#00B4D8;"></span>
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#48CAE4;"></span>
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#90E0EF;"></span>
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#023E8A;"></span>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="sec_color_scheme" value="warm" id="sec_cs_warm">
                                    <label class="form-check-label fw-semibold" for="sec_cs_warm">Warm</label>
                                </div>
                                <div class="d-flex gap-1 ms-4">
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#E63946;"></span>
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#F4A261;"></span>
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#E9C46A;"></span>
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#2A9D8F;"></span>
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#264653;"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Footer Actions -->
                <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                    <button type="button" class="btn btn-outline-secondary px-4" onclick="resetAllSecFilters()" style="border-radius: 25px;">Clear All</button>
                    <button type="button" class="btn btn-primary px-5 py-2" onclick="applyGraphFilter()" style="border-radius: 25px; min-width: 200px;">Apply & View</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    #graphFilterModal .nav-tabs .nav-link { font-size: 0.8rem; padding: 0.5rem 0.75rem; }
    #graphFilterModal .nav-tabs .nav-link.active { color: #4472C4; border-bottom: 2px solid #4472C4; font-weight: 600; }
    @media (max-width: 768px) {
        #graphFilterModal .modal-dialog { max-width: 100%; margin: 0.5rem; }
        #graphFilterModal .tab-content { max-height: 60vh; }
    }
</style>

<script>
var csrfToken = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '';
var monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

function escapeHtml(str) {
    if (!str) return '';
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(str));
    return div.innerHTML;
}

// State
var poeAllocations = {};
var savedMonthlyPoe = {};
var matrixData = null;

window.closeInstructions = function() {
    document.getElementById('instructionsCard').style.display = 'none';
};

window.handlePOESelect = function() {
    if (document.getElementById('poeSelect').value) {
        openPOEModal();
    }
};

window.openPOEModal = function() {
    var yearStart = document.getElementById('categorySelect').value;
    var yearEnd = document.getElementById('yearEndSelect').value;
    var poeCategory = document.getElementById('poeSelect').value;
    document.getElementById('poeYear').textContent = yearStart && yearEnd ? '(' + yearStart + '-' + yearEnd + ')' : '';

    function showModal() {
        document.querySelectorAll('.poe-input').forEach(function(input) {
            var pid = input.dataset.productId;
            if (poeAllocations[pid]) {
                input.value = poeAllocations[pid];
            }
        });
        updatePoeTotal();
        var modal = new bootstrap.Modal(document.getElementById('poeModal'));
        modal.show();
    }

    if (yearStart && yearEnd && poeCategory) {
        fetch('/sec-analysis/monthly-poe?' + new URLSearchParams({
            poe_category: poeCategory,
            year_start: yearStart,
            year_end: yearEnd
        }), { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken } })
        .then(function(r) { return r.json(); })
        .then(function(res) {
            if (res.success) {
                savedMonthlyPoe = res.monthly || {};
                if (res.yearly && Object.keys(poeAllocations).length === 0) {
                    for (var pid in res.yearly) {
                        var vals = Object.values(res.yearly[pid]);
                        if (vals.length > 0) poeAllocations[pid] = vals[0];
                    }
                }
            }
            showModal();
        })
        .catch(function() { showModal(); });
    } else {
        showModal();
    }
};

window.updatePoeTotal = function() {
    var total = 0;
    document.querySelectorAll('.poe-input').forEach(function(input) {
        total += parseFloat(input.value) || 0;
    });

    var display = document.getElementById('poeTotalDisplay');
    var msg = document.getElementById('poeValidationMsg');
    var btn = document.getElementById('calculateSecBtn');

    display.textContent = total.toFixed(2) + '%';

    if (Math.abs(total - 100) < 0.01) {
        display.classList.remove('text-danger');
        display.classList.add('text-success');
        msg.style.display = 'none';
        btn.disabled = false;
    } else {
        display.classList.remove('text-success');
        display.classList.add('text-danger');
        msg.style.display = 'block';
        btn.disabled = true;
    }
};

// Toggle between Yearly and Monthly POE mode
window.togglePoeMode = function() {
    var mode = document.querySelector('input[name="poeMode"]:checked').value;
    if (mode === 'yearly') {
        document.getElementById('poeYearlySection').style.display = '';
        document.getElementById('poeMonthlySection').style.display = 'none';
    } else {
        document.getElementById('poeYearlySection').style.display = 'none';
        document.getElementById('poeMonthlySection').style.display = '';
        populateMonthlyPoeTable();
    }
};

// Populate the monthly POE table with rows for each month in the year range
function populateMonthlyPoeTable() {
    var yearStart = parseInt(document.getElementById('categorySelect').value) || new Date().getFullYear();
    var yearEnd = parseInt(document.getElementById('yearEndSelect').value) || yearStart;
    var tbody = document.getElementById('monthlyPoeBody');
    var products = @json($products->map(fn($p) => ['id' => $p->id, 'name' => $p->name]));
    var html = '';

    for (var y = yearStart; y <= yearEnd; y++) {
        for (var m = 1; m <= 12; m++) {
            var monthKey = y + '-' + String(m).padStart(2, '0');
            var label = monthNames[m - 1] + '-' + String(y).slice(2);
            html += '<tr>';
            html += '<td class="py-1 fw-semibold">' + label + '</td>';
            products.forEach(function(p) {
                var existingVal = (savedMonthlyPoe[p.id] && savedMonthlyPoe[p.id][monthKey]) ? savedMonthlyPoe[p.id][monthKey] : '';
                html += '<td class="py-1"><input type="number" class="form-control form-control-sm text-center mpoe-input" '
                    + 'data-product-id="' + p.id + '" data-month="' + monthKey + '" '
                    + 'value="' + existingVal + '" placeholder="0" min="0" max="100" step="0.01" '
                    + 'oninput="updateMonthlyPoeTotal(this)"></td>';
            });
            html += '<td class="py-1 text-center fw-bold mpoe-total" data-month="' + monthKey + '">0%</td>';
            html += '</tr>';
        }
    }
    tbody.innerHTML = html;
    tbody.querySelectorAll('.mpoe-input').forEach(function(inp) {
        if (inp.value) updateMonthlyPoeTotal(inp);
    });
}

window.updateMonthlyPoeTotal = function(input) {
    var monthKey = input.dataset.month;
    var total = 0;
    document.querySelectorAll('.mpoe-input[data-month="' + monthKey + '"]').forEach(function(inp) {
        total += parseFloat(inp.value) || 0;
    });
    var cell = document.querySelector('.mpoe-total[data-month="' + monthKey + '"]');
    if (cell) {
        cell.textContent = total.toFixed(2) + '%';
        cell.classList.remove('text-danger', 'text-success');
        cell.classList.add(Math.abs(total - 100) < 0.01 ? 'text-success' : 'text-danger');
    }
    // Enable/disable button
    var allValid = true;
    document.querySelectorAll('.mpoe-total').forEach(function(t) {
        var val = parseFloat(t.textContent);
        if (val > 0 && Math.abs(val - 100) > 0.01) allValid = false;
    });
    document.getElementById('calculateSecBtn').disabled = !allValid;
};

window.calculateSEC = function() {
    var mode = document.querySelector('input[name="poeMode"]:checked').value;
    var yearStart = parseInt(document.getElementById('categorySelect').value);
    var yearEnd = parseInt(document.getElementById('yearEndSelect').value);
    var poeCategory = document.getElementById('poeSelect').value;

    if (mode === 'yearly') {
        // Yearly mode - same as before but uses monthly POE endpoint
        var allocations = [];
        poeAllocations = {};
        document.querySelectorAll('.poe-input').forEach(function(input) {
            var productId = parseInt(input.dataset.productId);
            var percentage = parseFloat(input.value) || 0;
            allocations.push({ product_id: productId, percentage: percentage });
            poeAllocations[productId] = percentage;
        });

        var total = allocations.reduce(function(sum, a) { return sum + a.percentage; }, 0);
        if (Math.abs(total - 100) > 0.01) {
            alert('POE percentages must sum to exactly 100%. Current total: ' + total.toFixed(2) + '%');
            return;
        }

        fetch('/sec-analysis/monthly-poe', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify({
                mode: 'yearly',
                poe_category: poeCategory,
                year_start: yearStart,
                year_end: yearEnd,
                allocations: allocations
            })
        })
        .then(function(r) { return r.json(); })
        .then(function(res) {
            if (res.success) {
                bootstrap.Modal.getInstance(document.getElementById('poeModal')).hide();
                updateContent();
            }
        })
        .catch(function(err) { console.error('Failed to save POE', err); });

    } else {
        // Monthly mode - build per-product monthly allocations
        var productMonths = {};
        document.querySelectorAll('.mpoe-input').forEach(function(inp) {
            var pid = inp.dataset.productId;
            var month = inp.dataset.month;
            var val = parseFloat(inp.value) || 0;
            if (!productMonths[pid]) productMonths[pid] = {};
            productMonths[pid][month] = val;
        });

        var allocations = [];
        for (var pid in productMonths) {
            allocations.push({ product_id: parseInt(pid), months: productMonths[pid] });
        }

        // Also populate poeAllocations for display (use average)
        poeAllocations = {};
        allocations.forEach(function(a) {
            var vals = Object.values(a.months);
            var avg = vals.reduce(function(s, v) { return s + v; }, 0) / vals.length;
            poeAllocations[a.product_id] = avg;
        });

        fetch('/sec-analysis/monthly-poe', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify({
                mode: 'monthly',
                poe_category: poeCategory,
                year_start: yearStart,
                year_end: yearEnd,
                allocations: allocations
            })
        })
        .then(function(r) { return r.json(); })
        .then(function(res) {
            if (res.success) {
                bootstrap.Modal.getInstance(document.getElementById('poeModal')).hide();
                updateContent();
            }
        })
        .catch(function(err) { console.error('Failed to save monthly POE', err); });
    }
};

window.updateStatus = function() {
    var category = document.getElementById('categorySelect').value;
    var yearEnd = document.getElementById('yearEndSelect').value;
    var poe = document.getElementById('poeSelect').value;
    var matrix = document.getElementById('matrixSelect').value;
    var msg = document.getElementById('statusMessage');

    if (!msg) return;

    if (!category) {
        msg.textContent = 'Please complete category';
    } else if (!yearEnd) {
        msg.textContent = 'Please select year range';
    } else if (!poe || Object.keys(poeAllocations).length === 0) {
        msg.textContent = 'Please insert POE';
    } else if (!matrix) {
        msg.textContent = 'Please select matrix';
    } else {
        msg.textContent = 'Loading...';
    }
};

window.updateContent = function() {
    updateStatus();

    var category = document.getElementById('categorySelect').value;
    var yearEnd = document.getElementById('yearEndSelect').value;
    var matrix = document.getElementById('matrixSelect').value;
    var poe = document.getElementById('poeSelect').value;

    if (category && yearEnd && matrix && poe && Object.keys(poeAllocations).length > 0) {
        loadMatrixData(matrix);
    }
};

// Track current view state for filtering
var currentMatrixType = null;

function loadMatrixData(matrixType) {
    currentMatrixType = matrixType;
    var yearStart = document.getElementById('categorySelect').value;
    var yearEnd = document.getElementById('yearEndSelect').value;
    var poeCategory = document.getElementById('poeSelect').value;

    var params = new URLSearchParams();
    params.append('year_start', yearStart);
    params.append('year_end', yearEnd);
    params.append('poe_category', poeCategory);

    fetch('/sec-analysis/data/matrix?' + params.toString(), {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(function(response) { return response.json(); })
    .then(function(result) {
        if (result.success) {
            matrixData = result;
            if (matrixType === 'Table') {
                displayTable(result);
            } else if (matrixType === 'Graph') {
                displayGraph(result);
            }
        } else {
            document.getElementById('contentArea').innerHTML =
                '<div class="text-center py-5"><p class="text-danger">Failed to load data.</p></div>';
        }
    })
    .catch(function(err) {
        console.error('Error loading matrix data:', err);
        document.getElementById('contentArea').innerHTML =
            '<div class="text-center py-5"><p class="text-danger">Network error. Please check your connection.</p></div>';
    });
}

window.displayTable = function(result) {
    renderFilteredTable(result);
};

window.renderFilteredTable = function(result) {
    var contentArea = document.getElementById('contentArea');

    // Read selected years from filter modal checkboxes
    var selectedYears = [];
    document.querySelectorAll('.gf-year:checked').forEach(function(cb) {
        selectedYears.push(parseInt(cb.value));
    });
    // If no year checkboxes are checked, default to all years in data
    if (selectedYears.length === 0) {
        selectedYears = result.data.map(function(d) { return d.year; });
    }

    // Read selected products from filter modal checkboxes
    var selectedProductIds = [];
    document.querySelectorAll('.gf-product:checked').forEach(function(cb) {
        if (cb.value !== 'total') {
            selectedProductIds.push(cb.value);
        }
    });
    // If no product checkboxes are checked, default to all products
    if (selectedProductIds.length === 0) {
        selectedProductIds = Object.keys(result.product_names);
    }

    // Read selected energy/resource sources from filter modal checkboxes
    var selectedEnergyIds = [];
    var selectedResourceIds = [];
    document.querySelectorAll('.gf-energy-source:checked').forEach(function(cb) {
        var val = cb.value;
        if (val.startsWith('energy_')) {
            selectedEnergyIds.push(val.replace('energy_', ''));
        } else if (val.startsWith('resource_')) {
            selectedResourceIds.push(val.replace('resource_', ''));
        }
    });
    // If none checked, default to all
    if (selectedEnergyIds.length === 0 && selectedResourceIds.length === 0) {
        selectedEnergyIds = Object.keys(result.energy_source_names);
        selectedResourceIds = Object.keys(result.resource_source_names);
    } else if (selectedEnergyIds.length === 0) {
        selectedEnergyIds = Object.keys(result.energy_source_names);
    } else if (selectedResourceIds.length === 0) {
        selectedResourceIds = Object.keys(result.resource_source_names);
    }

    // Filter data by selected years
    var filteredData = result.data.filter(function(d) {
        return selectedYears.indexOf(d.year) !== -1;
    });

    // Filter product names to only selected ones
    var filteredProductNames = {};
    selectedProductIds.forEach(function(pid) {
        if (result.product_names[pid]) {
            filteredProductNames[pid] = result.product_names[pid];
        }
    });

    // Filter energy/resource source names to only selected ones
    var filteredEnergyNames = {};
    selectedEnergyIds.forEach(function(eid) {
        if (result.energy_source_names[eid]) {
            filteredEnergyNames[eid] = result.energy_source_names[eid];
        }
    });
    var filteredResourceNames = {};
    selectedResourceIds.forEach(function(rid) {
        if (result.resource_source_names[rid]) {
            filteredResourceNames[rid] = result.resource_source_names[rid];
        }
    });

    // Filter sec_total_table by selected years and products
    var filteredSecTotal = result.sec_total_table.filter(function(row) {
        if (row.product_id === null) return true; // always include total row
        return selectedProductIds.indexOf(String(row.product_id)) !== -1;
    }).map(function(row) {
        var filteredYears = {};
        selectedYears.forEach(function(y) {
            if (row.years[y] !== undefined) {
                filteredYears[y] = row.years[y];
            }
        });
        return { product_id: row.product_id, product_name: row.product_name, years: filteredYears };
    });

    var html = '';

    // Filter button bar
    html += '<div class="d-flex justify-content-end mb-3">';
    html += '<button class="btn btn-primary btn-sm d-flex align-items-center gap-2" onclick="openGraphFilterModal()" style="border-radius: 8px; padding: 8px 18px; font-weight: 600;">';
    html += '<i class="bi bi-funnel-fill"></i> Filter';
    html += '</button>';
    html += '</div>';

    html += buildSecTotalTable(filteredSecTotal, filteredData, selectedEnergyIds, selectedResourceIds);
    html += buildMatrixTable(filteredData, filteredEnergyNames, filteredResourceNames, filteredProductNames, selectedEnergyIds, selectedResourceIds);
    contentArea.innerHTML = html;
};

function buildSecTotalTable(secTotalTable, data, filteredEnergyIds, filteredResourceIds) {
    var years = data.map(function(d) { return d.year; });
    var allEnergyIds = data.length > 0 && data[0].months.length > 0 ? Object.keys(data[0].months[0].energy || {}) : [];
    var allResourceIds = data.length > 0 && data[0].months.length > 0 ? Object.keys(data[0].months[0].resource || {}) : [];
    var isFiltered = filteredEnergyIds.length !== allEnergyIds.length || filteredResourceIds.length !== allResourceIds.length;

    // Card wrapper with collapsible header
    var html = '<div class="card border-0 shadow-sm mb-4">';
    html += '<div class="card-header bg-white d-flex align-items-center justify-content-between px-4 py-3" style="cursor: pointer; user-select: none;" onclick="toggleSecTotal()">';
    html += '<div>';
    html += '<h6 class="fw-bold mb-0">SEC Total Table</h6>';
    html += '<small class="text-muted">Annual SEC values by product' + (isFiltered ? ' <span class="badge bg-warning text-dark">Filtered</span>' : '') + '</small>';
    html += '</div>';
    html += '<i class="bi bi-chevron-down" id="secTotalChevron" style="transition: transform 0.3s;"></i>';
    html += '</div>';

    html += '<div id="secTotalCollapse">';
    html += '<div class="table-responsive"><table class="table table-hover align-middle mb-0">';
    html += '<thead class="table-light">';
    html += '<tr>';
    html += '<th class="py-3">Output</th>';
    years.forEach(function(y) { html += '<th class="py-3 text-center">' + y + '</th>'; });
    html += '</tr></thead><tbody>';

    if (isFiltered) {
        // Recalculate SEC total table from filtered energy types
        // For each product, SEC = filtered total energy / production
        var productIds = [];
        secTotalTable.forEach(function(row) { if (row.product_id !== null) productIds.push(String(row.product_id)); });

        // Calculate per-year, per-product SEC from filtered energy data
        data.forEach(function(yearData) {
            // Sum filtered energy consumption for the year
            var yearlyFilteredCombined = 0;
            yearData.months.forEach(function(m) {
                filteredEnergyIds.forEach(function(eid) { yearlyFilteredCombined += parseFloat(m.energy[eid] || 0); });
                filteredResourceIds.forEach(function(rid) { yearlyFilteredCombined += parseFloat(m.resource[rid] || 0); });
            });
        });

        secTotalTable.forEach(function(row) {
            var isTotal = row.product_id === null;
            var cellClass = isTotal ? ' fw-bold' : '';
            html += '<tr>';
            html += '<td class="py-2' + cellClass + '">' + row.product_name + '</td>';
            years.forEach(function(y) {
                var yd = data.find(function(d) { return d.year === y; });
                if (!yd) { html += '<td class="py-2 text-center' + cellClass + '">-</td>'; return; }

                var filteredCombined = 0;
                var totalProd = 0;
                yd.months.forEach(function(m) {
                    filteredEnergyIds.forEach(function(eid) { filteredCombined += parseFloat(m.energy[eid] || 0); });
                    filteredResourceIds.forEach(function(rid) { filteredCombined += parseFloat(m.resource[rid] || 0); });
                    if (isTotal) {
                        Object.keys(m.production || {}).forEach(function(pid) { totalProd += parseFloat(m.production[pid] || 0); });
                    } else {
                        totalProd += parseFloat(m.production[row.product_id] || 0);
                    }
                });
                var secVal = totalProd > 0 ? filteredCombined / totalProd : 0;
                html += '<td class="py-2 text-center' + cellClass + '">' + (secVal > 0 ? secVal.toFixed(2) : '-') + '</td>';
            });
            html += '</tr>';
        });
    } else {
        secTotalTable.forEach(function(row) {
            var isTotal = row.product_id === null;
            var cellClass = isTotal ? ' fw-bold' : '';
            html += '<tr>';
            html += '<td class="py-2' + cellClass + '">' + row.product_name + '</td>';
            years.forEach(function(y) {
                var val = row.years[y] !== undefined ? parseFloat(row.years[y]).toFixed(2) : '-';
                html += '<td class="py-2 text-center' + cellClass + '">' + val + '</td>';
            });
            html += '</tr>';
        });
    }

    html += '</tbody></table></div>';
    html += '</div>';
    html += '</div>';
    return html;
}

function buildMatrixTable(data, energySourceNames, resourceSourceNames, productNames, filteredEnergyIds, filteredResourceIds) {
    var energyIds = Object.keys(energySourceNames);
    var resourceIds = Object.keys(resourceSourceNames);
    var productIds = Object.keys(productNames);

    var numEnergy = energyIds.length;
    var numResource = resourceIds.length;
    var numProducts = productIds.length;

    // Detect if energy types are filtered (not all selected)
    var allEnergyIds = data.length > 0 && data[0].months.length > 0 ? Object.keys(data[0].months[0].energy || {}) : [];
    var allResourceIds = data.length > 0 && data[0].months.length > 0 ? Object.keys(data[0].months[0].resource || {}) : [];
    var isEnergyFiltered = filteredEnergyIds && filteredResourceIds &&
        (filteredEnergyIds.length !== allEnergyIds.length || filteredResourceIds.length !== allResourceIds.length);

    // Card wrapper with collapsible header
    var html = '<div class="card border-0 shadow-sm">';
    html += '<div class="card-header bg-white d-flex align-items-center justify-content-between px-4 py-3" style="cursor: pointer; user-select: none;" onclick="toggleSecCalc()">';
    html += '<div>';
    html += '<h6 class="fw-bold mb-0">SEC Calculation Table</h6>';
    html += '<small class="text-muted">Detailed monthly energy, resource, and SEC values' + (isEnergyFiltered ? ' <span class="badge bg-warning text-dark">Filtered</span>' : '') + '</small>';
    html += '</div>';
    html += '<i class="bi bi-chevron-down" id="secCalcChevron" style="transition: transform 0.3s;"></i>';
    html += '</div>';

    html += '<div id="secCalcCollapse">';
    html += '<div class="table-responsive"><table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">';

    // Header row 1 - group headers
    html += '<thead class="table-light">';
    html += '<tr>';
    html += '<th rowspan="2" class="text-center" style="min-width: 70px;">Month</th>';
    html += '<th colspan="' + (numEnergy + 1) + '" class="text-center">Energy</th>';
    html += '<th colspan="' + (numResource + 1) + '" class="text-center">Energy Resource</th>';
    html += '<th rowspan="2" class="text-center" style="min-width: 100px;">Total Energy Consumption</th>';
    html += '<th colspan="' + (numProducts + 1) + '" class="text-center">Production</th>';
    html += '<th colspan="' + (numProducts + 1) + '" class="text-center">SEC Production</th>';
    html += '</tr>';

    // Header row 2 - sub-headers
    html += '<tr>';
    energyIds.forEach(function(id) {
        html += '<th class="text-center" style="min-width: 80px; font-size: 0.8rem;">' + energySourceNames[id] + '</th>';
    });
    html += '<th class="text-center" style="min-width: 90px; font-size: 0.8rem;">Total</th>';
    resourceIds.forEach(function(id) {
        html += '<th class="text-center" style="min-width: 80px; font-size: 0.8rem;">' + resourceSourceNames[id] + '</th>';
    });
    html += '<th class="text-center" style="min-width: 100px; font-size: 0.8rem;">Total</th>';
    productIds.forEach(function(id) {
        html += '<th class="text-center" style="min-width: 80px; font-size: 0.8rem;">' + productNames[id] + '</th>';
    });
    html += '<th class="text-center" style="min-width: 100px; font-size: 0.8rem;">Total</th>';
    productIds.forEach(function(id) {
        html += '<th class="text-center" style="min-width: 80px; font-size: 0.8rem;">' + productNames[id] + '</th>';
    });
    html += '<th class="text-center" style="min-width: 100px; font-size: 0.8rem;">Total</th>';
    html += '</tr></thead><tbody>';

    // Data rows
    var rowIdx = 0;
    data.forEach(function(yearData) {
        // Accumulators for yearly totals
        var yearlyProdByProduct = {};
        productIds.forEach(function(id) { yearlyProdByProduct[id] = 0; });
        var yearlyTotalProd = 0;
        var yearlyEnergyBySource = {};
        energyIds.forEach(function(id) { yearlyEnergyBySource[id] = 0; });
        var yearlyResourceBySource = {};
        resourceIds.forEach(function(id) { yearlyResourceBySource[id] = 0; });
        var yearlyFilteredEnergy = 0;
        var yearlyFilteredResource = 0;
        var yearlyFilteredCombined = 0;

        yearData.months.forEach(function(m) {
            var monthLabel = monthNames[m.month - 1] + '-' + String(yearData.year).slice(2);

            // Recalculate energy total from filtered sources
            var filteredTotalEnergy = 0;
            energyIds.forEach(function(id) { filteredTotalEnergy += parseFloat(m.energy[id] || 0); });

            // Recalculate resource total from filtered sources
            var filteredTotalResource = 0;
            resourceIds.forEach(function(id) { filteredTotalResource += parseFloat(m.resource[id] || 0); });

            // Recalculate total combined from filtered sources
            var filteredTotalCombined = filteredTotalEnergy + filteredTotalResource;

            // Dynamically sum production total from filtered products only
            var filteredTotalProd = 0;
            productIds.forEach(function(id) { filteredTotalProd += parseFloat(m.production[id] || 0); });

            // Recalculate SEC per product: filteredTotalCombined / product_production
            // And SEC total: filteredTotalCombined / filteredTotalProd
            var filteredSecTotal = filteredTotalProd > 0 ? filteredTotalCombined / filteredTotalProd : 0;

            // Accumulate yearly
            productIds.forEach(function(id) {
                yearlyProdByProduct[id] += parseFloat(m.production[id] || 0);
            });
            yearlyTotalProd += filteredTotalProd;
            energyIds.forEach(function(id) { yearlyEnergyBySource[id] += parseFloat(m.energy[id] || 0); });
            resourceIds.forEach(function(id) { yearlyResourceBySource[id] += parseFloat(m.resource[id] || 0); });
            yearlyFilteredEnergy += filteredTotalEnergy;
            yearlyFilteredResource += filteredTotalResource;
            yearlyFilteredCombined += filteredTotalCombined;

            html += '<tr>';
            html += '<td class="text-center fw-semibold">' + monthLabel + '</td>';

            // Energy columns
            energyIds.forEach(function(id) { html += '<td class="text-end">' + formatNum(m.energy[id]) + '</td>'; });
            html += '<td class="text-end fw-bold" style="background: rgba(68,114,196,0.06);">' + formatNum(filteredTotalEnergy) + '</td>';

            // Resource columns
            resourceIds.forEach(function(id) { html += '<td class="text-end">' + formatNum(m.resource[id]) + '</td>'; });
            html += '<td class="text-end fw-bold" style="background: rgba(68,114,196,0.06);">' + formatNum(filteredTotalResource) + '</td>';

            // Total Energy Consumption (recalculated)
            html += '<td class="text-end fw-bold" style="background: rgba(68,114,196,0.15);">' + formatNum(filteredTotalCombined) + '</td>';

            // Production columns
            productIds.forEach(function(id) { html += '<td class="text-end">' + formatNum(m.production[id]) + '</td>'; });
            html += '<td class="text-end fw-bold" style="background: rgba(68,114,196,0.06);">' + formatNum(filteredTotalProd) + '</td>';

            // SEC columns (recalculated per product)
            productIds.forEach(function(id) {
                var prodVal = parseFloat(m.production[id] || 0);
                var secPerProduct = prodVal > 0 ? filteredTotalCombined / prodVal : 0;
                html += '<td class="text-end">' + formatSec(isEnergyFiltered ? secPerProduct : m.sec[id]) + '</td>';
            });
            html += '<td class="text-end fw-bold" style="background: rgba(68,114,196,0.06);">' + formatSec(filteredSecTotal) + '</td>';

            html += '</tr>';
            rowIdx++;
        });

        // Yearly total row (recalculated from filtered data)
        var yearlySecTotal = yearlyTotalProd > 0 ? yearlyFilteredCombined / yearlyTotalProd : 0;

        var rowBg = 'background: rgba(68,114,196,0.12);';
        var darkBg = 'background: rgba(68,114,196,0.22);';
        html += '<tr>';
        html += '<td class="text-center fw-bold" style="' + rowBg + '">Total/' + yearData.year + '</td>';

        energyIds.forEach(function(id) { html += '<td class="text-end fw-semibold" style="' + rowBg + '">' + formatNum(yearlyEnergyBySource[id]) + '</td>'; });
        html += '<td class="text-end fw-bold" style="' + darkBg + '">' + formatNum(yearlyFilteredEnergy) + '</td>';
        resourceIds.forEach(function(id) { html += '<td class="text-end fw-semibold" style="' + rowBg + '">' + formatNum(yearlyResourceBySource[id]) + '</td>'; });
        html += '<td class="text-end fw-bold" style="' + darkBg + '">' + formatNum(yearlyFilteredResource) + '</td>';
        html += '<td class="text-end fw-bold" style="' + darkBg + '">' + formatNum(yearlyFilteredCombined) + '</td>';
        productIds.forEach(function(id) { html += '<td class="text-end fw-semibold" style="' + rowBg + '">' + formatNum(yearlyProdByProduct[id]) + '</td>'; });
        html += '<td class="text-end fw-bold" style="' + darkBg + '">' + formatNum(yearlyTotalProd) + '</td>';
        productIds.forEach(function(id) {
            var prodVal = yearlyProdByProduct[id];
            var secPerProduct = prodVal > 0 ? yearlyFilteredCombined / prodVal : 0;
            html += '<td class="text-end fw-semibold" style="' + rowBg + '">' + formatSec(isEnergyFiltered ? secPerProduct : yearData.yearly_sec[id]) + '</td>';
        });
        html += '<td class="text-end fw-bold" style="' + darkBg + '">' + formatSec(yearlySecTotal) + '</td>';
        html += '</tr>';
        rowIdx++;
    });

    html += '</tbody></table></div>';
    html += '</div>';
    html += '</div>';
    return html;
}

function formatNum(num) {
    if (!num || num === 0) return '0';
    return parseFloat(num).toLocaleString('en-US', { maximumFractionDigits: 0 });
}

function formatSec(num) {
    if (!num || num === 0) return '0.0000';
    return parseFloat(num).toFixed(4);
}

// ===== GRAPH FUNCTIONALITY =====
var secChart = null;
var graphFilterResult = null;

// Unit conversion factors from GJ
var unitConversions = {
    'J': 1000000000, 'kJ': 1000000, 'MJ': 1000, 'GJ': 1,
    'Wh': 277777778, 'kWh': 277778, 'MWh': 277.778, 'GWh': 0.277778,
    'BTU': 947817120, 'MMBTU': 947.817
};

var chartColorSchemes = {
    default:   ['#2563EB','#DC2626','#059669','#D97706','#7C3AED','#DB2777','#0891B2','#EA580C','#4F46E5','#16A34A','#CA8A04','#9333EA'],
    pastel:    ['#93C5FD','#FCA5A5','#6EE7B7','#FCD34D','#C4B5FD','#FBCFE8','#67E8F9','#FDBA74','#A5B4FC','#86EFAC','#FDE047','#D8B4FE'],
    corporate: ['#1E3A5F','#4472C4','#70AD47','#ED7D31','#FFC000','#5B9BD5','#A5A5A5','#264478','#9DC3E6','#548235','#BF8F00','#7030A0'],
    vibrant:   ['#FF6B6B','#4ECDC4','#45B7D1','#96CEB4','#FFEAA7','#DDA0DD','#98D8C8','#F7DC6F','#BB8FCE','#82E0AA','#F8C471','#AED6F1'],
    ocean:     ['#0077B6','#00B4D8','#48CAE4','#90E0EF','#023E8A','#0096C7','#CAF0F8','#ADE8F4','#03045E','#0077B6','#00B4D8','#48CAE4'],
    warm:      ['#E63946','#F4A261','#E9C46A','#2A9D8F','#264653','#D62828','#F77F00','#FCBF49','#EAE2B7','#003049','#C1121F','#669BBC']
};
function getActiveColors() {
    var r = document.querySelector('input[name="sec_color_scheme"]:checked');
    var scheme = r ? r.value : 'default';
    return chartColorSchemes[scheme] || chartColorSchemes['default'];
}
var chartColors = chartColorSchemes['default'];

window.displayGraph = function(result) {
    graphFilterResult = result;
    chartColors = getActiveColors();
    var f = getFilterValues();
    var allYears = f.years.length > 0 ? f.years : result.data.map(function(d) { return d.year; });
    var allProducts = f.products.length > 0 ? f.products : (function() {
        var p = Object.keys(result.product_names); p.push('total'); return p;
    })();
    var conv = unitConversions[f.energyUnit] || 1;
    renderChart(result, allYears, allProducts, f.chartType, f.timePeriod, conv, f.energyUnit + '/tonne', f.showReading);
    updateSecEnergyCount();
};

// Helper function to get all filter values
window.getFilterValues = function() {
    var filters = {};

    filters.years = [];
    document.querySelectorAll('.gf-year:checked').forEach(function(cb) { filters.years.push(parseInt(cb.value)); });

    filters.products = [];
    document.querySelectorAll('.gf-product:checked').forEach(function(cb) { filters.products.push(cb.value); });

    filters.energySources = [];
    document.querySelectorAll('.gf-energy-source:checked').forEach(function(cb) { filters.energySources.push(cb.value); });

    var unitRadio = document.querySelector('input[name="gf_energy_unit"]:checked');
    filters.energyUnit = unitRadio ? unitRadio.value : 'GJ';

    var chartRadio = document.querySelector('input[name="sec_chart_type"]:checked');
    filters.chartType = chartRadio ? chartRadio.value : 'bar';

    var showReadingEl = document.getElementById('sec_show_reading');
    filters.showReading = showReadingEl ? showReadingEl.checked : false;

    var showLegendEl = document.getElementById('sec_show_legend');
    filters.showLegend = showLegendEl ? showLegendEl.checked : true;

    var showGridEl = document.getElementById('sec_show_grid');
    filters.showGrid = showGridEl ? showGridEl.checked : true;

    var timePeriodRadio = document.querySelector('input[name="sec_time_period"]:checked');
    filters.timePeriod = timePeriodRadio ? timePeriodRadio.value : 'yearly';

    var colorRadio = document.querySelector('input[name="sec_color_scheme"]:checked');
    filters.colorScheme = colorRadio ? colorRadio.value : 'default';

    return filters;
};

window.applyGraphFilter = function() {
    var modal = bootstrap.Modal.getInstance(document.getElementById('graphFilterModal'));
    if (modal) modal.hide();

    if (!matrixData) return;

    var filters = getFilterValues();
    chartColors = getActiveColors();

    if (currentMatrixType === 'Table') {
        renderFilteredTable(matrixData);
        return;
    }

    if (!graphFilterResult) return;

    var selectedYears = filters.years.length > 0 ? filters.years : graphFilterResult.data.map(function(d) { return d.year; });
    var selectedProducts = filters.products.length > 0 ? filters.products : (function() {
        var p = Object.keys(graphFilterResult.product_names); p.push('total'); return p;
    })();
    var convFactor = unitConversions[filters.energyUnit] || 1;
    renderChart(graphFilterResult, selectedYears, selectedProducts, filters.chartType, filters.timePeriod, convFactor, filters.energyUnit + '/tonne', filters.showReading);
};

window.openGraphFilterModal = function() {
    updateSecEnergyCount();
    var modal = new bootstrap.Modal(document.getElementById('graphFilterModal'));
    modal.show();
};

// ===== FILTER HELPERS =====
window.updateSecEnergyCount = function() {
    var total = document.querySelectorAll('.gf-energy-source').length;
    var checked = document.querySelectorAll('.gf-energy-source:checked').length;
    var el = document.getElementById('secEnergySelectedCount');
    if (el) el.textContent = checked + ' / ' + total + ' selected';
};
window.filterSecEnergySources = function(term) {
    term = term.toLowerCase();
    document.querySelectorAll('.sec-energy-item').forEach(function(el) {
        el.style.display = el.textContent.toLowerCase().includes(term) ? '' : 'none';
    });
};
window.searchSecFilters = function(term) {
    term = term.toLowerCase();
    document.querySelectorAll('#graphFilterModal .tab-pane .form-check-label, #graphFilterModal .tab-pane h6').forEach(function(el) {
        var parent = el.closest('.form-check') || el;
        parent.style.display = term === '' ? '' : (el.textContent.toLowerCase().includes(term) ? '' : 'none');
    });
};
window.resetAllSecFilters = function() {
    document.querySelectorAll('.gf-year').forEach(function(cb) { cb.checked = true; });
    document.querySelectorAll('.gf-product').forEach(function(cb) { cb.checked = true; });
    document.querySelectorAll('.gf-energy-source').forEach(function(cb) { cb.checked = true; });
    var r = document.getElementById('sec_ct_bar'); if (r) r.checked = true;
    var t = document.getElementById('sec_tp_yearly'); if (t) t.checked = true;
    var u = document.getElementById('gf_unit_GJ'); if (u) u.checked = true;
    var sr = document.getElementById('sec_show_reading'); if (sr) sr.checked = false;
    var sl = document.getElementById('sec_show_legend'); if (sl) sl.checked = true;
    var sg = document.getElementById('sec_show_grid'); if (sg) sg.checked = true;
    var sm = document.getElementById('gf_show_min'); if (sm) sm.checked = false;
    var sx = document.getElementById('gf_show_max'); if (sx) sx.checked = false;
    var sa = document.getElementById('gf_show_avg'); if (sa) sa.checked = true;
    var st = document.getElementById('gf_show_trendline'); if (st) st.checked = false;
    var sq = document.getElementById('gf_show_r_squared'); if (sq) sq.checked = false;
    var sc = document.getElementById('sec_cs_default'); if (sc) sc.checked = true;
    updateSecEnergyCount();
};

// ===== EXPORT =====
window.exportSecChartPNG = function() {
    var c = document.getElementById('secChart'); if (!c) return;
    var a = document.createElement('a'); a.download = 'sec-analysis-chart.png'; a.href = c.toDataURL('image/png'); a.click();
};

window.exportSecCSV = function() {
    if (!matrixData) { alert('Load data first.'); return; }
    var f = getFilterValues();
    var selectedYears = f.years.length > 0 ? f.years : matrixData.data.map(function(d) { return d.year; });
    var filteredData = matrixData.data.filter(function(d) { return selectedYears.indexOf(d.year) !== -1; });
    var pNames = matrixData.product_names;
    var pIds = Object.keys(pNames);

    var rows = ['Month,' + pIds.map(function(id) { return pNames[id] + ' SEC'; }).join(',') + ',Total SEC'];
    filteredData.forEach(function(yd) {
        yd.months.forEach(function(m) {
            var label = monthNames[m.month - 1] + '-' + String(yd.year).slice(2);
            var cols = [label];
            pIds.forEach(function(id) { cols.push(parseFloat(m.sec[id] || 0).toFixed(4)); });
            cols.push(parseFloat(m.sec_total || 0).toFixed(4));
            rows.push(cols.join(','));
        });
    });

    var blob = new Blob([rows.join('\n')], { type: 'text/csv' });
    var a = document.createElement('a'); a.download = 'sec-analysis.csv'; a.href = URL.createObjectURL(blob); a.click(); URL.revokeObjectURL(a.href);
};

window.exportSecExcel = function() {
    if (!matrixData) { alert('Load data first.'); return; }
    var f = getFilterValues();
    var selectedYears = f.years.length > 0 ? f.years : matrixData.data.map(function(d) { return d.year; });
    var filteredData = matrixData.data.filter(function(d) { return selectedYears.indexOf(d.year) !== -1; });
    var pNames = matrixData.product_names;
    var pIds = Object.keys(pNames);
    var eNames = matrixData.energy_source_names;
    var eIds = Object.keys(eNames);
    var rNames = matrixData.resource_source_names;
    var rIds = Object.keys(rNames);

    var html = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel">';
    html += '<head><meta charset="UTF-8"><style>th{background:#4472C4;color:white;padding:4px 8px;} td{padding:4px 8px;border:1px solid #ddd;} .total{background:#d9e1f2;font-weight:bold;}</style></head><body>';
    html += '<table border="1">';
    html += '<thead><tr><th>Month</th>';
    eIds.forEach(function(id) { html += '<th>' + eNames[id] + '</th>'; });
    html += '<th>Energy Total</th>';
    rIds.forEach(function(id) { html += '<th>' + rNames[id] + '</th>'; });
    html += '<th>Resource Total</th><th>Total Energy</th>';
    pIds.forEach(function(id) { html += '<th>' + pNames[id] + ' (Prod)</th>'; });
    html += '<th>Total Prod</th>';
    pIds.forEach(function(id) { html += '<th>' + pNames[id] + ' SEC</th>'; });
    html += '<th>Total SEC</th></tr></thead><tbody>';

    filteredData.forEach(function(yd) {
        yd.months.forEach(function(m) {
            var label = monthNames[m.month - 1] + '-' + yd.year;
            html += '<tr><td>' + label + '</td>';
            eIds.forEach(function(id) { html += '<td>' + (parseFloat(m.energy[id] || 0).toFixed(2)) + '</td>'; });
            html += '<td>' + (parseFloat(m.total_energy || 0).toFixed(2)) + '</td>';
            rIds.forEach(function(id) { html += '<td>' + (parseFloat(m.resource[id] || 0).toFixed(2)) + '</td>'; });
            html += '<td>' + (parseFloat(m.total_resource || 0).toFixed(2)) + '</td>';
            html += '<td>' + (parseFloat(m.total_combined || 0).toFixed(2)) + '</td>';
            pIds.forEach(function(id) { html += '<td>' + (parseFloat(m.production[id] || 0).toFixed(2)) + '</td>'; });
            html += '<td>' + (parseFloat(m.total_production || 0).toFixed(2)) + '</td>';
            pIds.forEach(function(id) { html += '<td>' + (parseFloat(m.sec[id] || 0).toFixed(4)) + '</td>'; });
            html += '<td>' + (parseFloat(m.sec_total || 0).toFixed(4)) + '</td></tr>';
        });
        var yt = yd.yearly_totals;
        html += '<tr class="total"><td>Total/' + yd.year + '</td>';
        eIds.forEach(function(id) { html += '<td>' + (parseFloat(yt.energy && yt.energy[id] || 0).toFixed(2)) + '</td>'; });
        html += '<td>' + (parseFloat(yt.total_energy || 0).toFixed(2)) + '</td>';
        rIds.forEach(function(id) { html += '<td>' + (parseFloat(yt.resource && yt.resource[id] || 0).toFixed(2)) + '</td>'; });
        html += '<td>' + (parseFloat(yt.total_resource || 0).toFixed(2)) + '</td>';
        html += '<td>' + (parseFloat(yt.total_combined || 0).toFixed(2)) + '</td>';
        pIds.forEach(function(id) { html += '<td>' + (parseFloat(yt.production && yt.production[id] || 0).toFixed(2)) + '</td>'; });
        html += '<td>' + (parseFloat(yt.total_production || 0).toFixed(2)) + '</td>';
        pIds.forEach(function(id) { html += '<td>' + (parseFloat(yd.yearly_sec && yd.yearly_sec[id] || 0).toFixed(4)) + '</td>'; });
        html += '<td>' + (parseFloat(yd.yearly_sec_total || 0).toFixed(4)) + '</td></tr>';
    });
    html += '</tbody></table></body></html>';

    var blob = new Blob([html], { type: 'application/vnd.ms-excel' });
    var a = document.createElement('a'); a.download = 'sec-analysis.xls'; a.href = URL.createObjectURL(blob); a.click(); URL.revokeObjectURL(a.href);
};

window.toggleSecTotal = function() {
    var el = document.getElementById('secTotalCollapse');
    var chevron = document.getElementById('secTotalChevron');
    if (el.style.display === 'none') {
        el.style.display = '';
        chevron.style.transform = 'rotate(0deg)';
    } else {
        el.style.display = 'none';
        chevron.style.transform = 'rotate(-90deg)';
    }
};

window.toggleSecCalc = function() {
    var el = document.getElementById('secCalcCollapse');
    var chevron = document.getElementById('secCalcChevron');
    if (el.style.display === 'none') {
        el.style.display = '';
        chevron.style.transform = 'rotate(0deg)';
    } else {
        el.style.display = 'none';
        chevron.style.transform = 'rotate(-90deg)';
    }
};

function renderChart(result, selectedYears, selectedProducts, chartType, timePeriod, convFactor, unitLabel, showReading) {
    var contentArea = document.getElementById('contentArea');
    var html = '';

    // Title bar with Filter button
    html += '<div class="d-flex justify-content-between align-items-start mb-4">';
    html += '<div>';
    html += '<h4 class="fw-bold mb-1" style="color: #2E5AA5;">SEC Analytic Graph</h4>';
    html += '<p class="text-muted mb-0" style="font-size: 0.9rem;">Statistics</p>';
    html += '</div>';
    html += '<button class="btn btn-primary btn-sm d-flex align-items-center gap-2" onclick="openGraphFilterModal()" style="border-radius: 8px; padding: 8px 18px; font-weight: 600;">';
    html += '<i class="bi bi-funnel-fill"></i> Filter';
    html += '</button>';
    html += '</div>';

    html += '<div style="position: relative; height: 500px;"><canvas id="secChart"></canvas></div>';
    contentArea.innerHTML = html;

    var data = result.data;
    var productNames = result.product_names;

    // Filter data by selected years
    var filteredData = data.filter(function(d) { return selectedYears.indexOf(d.year) !== -1; });

    if (filteredData.length === 0) {
        contentArea.innerHTML = '<div class="text-center py-5"><p class="text-muted">No data for selected years.</p></div>';
        return;
    }

    if (chartType === 'pie') {
        renderPieChart(filteredData, selectedProducts, productNames, convFactor, unitLabel, showReading);
        return;
    }

    var labels = [];
    var datasets = [];

    var colors = getActiveColors();
    var isFill = chartType === 'area';

    if (timePeriod === 'monthly') {
        filteredData.forEach(function(yearData) {
            yearData.months.forEach(function(m) {
                labels.push(monthNames[m.month - 1] + '-' + String(yearData.year).slice(2));
            });
        });

        var colorIdx = 0;
        selectedProducts.forEach(function(pid) {
            var values = [];
            filteredData.forEach(function(yearData) {
                yearData.months.forEach(function(m) {
                    var val = pid === 'total' ? parseFloat(m.sec_total) * convFactor : parseFloat(m.sec[pid] || 0) * convFactor;
                    values.push(parseFloat(val.toFixed(4)));
                });
            });
            var name = pid === 'total' ? 'Total Production Output' : (productNames[pid] || 'Product ' + pid);
            var col = colors[colorIdx % colors.length];
            datasets.push({ label: name, data: values, backgroundColor: col + (chartType === 'bar' ? 'E6' : '40'), borderColor: col, borderWidth: 2, fill: isFill, tension: 0.3, type: chartType === 'combo' ? (colorIdx === 0 ? 'bar' : 'line') : undefined });
            colorIdx++;
        });

    } else {
        labels = filteredData.map(function(d) { return String(d.year); });

        var colorIdx = 0;
        selectedProducts.forEach(function(pid) {
            var values = filteredData.map(function(yearData) {
                var val = pid === 'total' ? parseFloat(yearData.yearly_sec_total) * convFactor : parseFloat(yearData.yearly_sec[pid] || 0) * convFactor;
                return parseFloat(val.toFixed(4));
            });
            var name = pid === 'total' ? 'Total Production Output' : (productNames[pid] || 'Product ' + pid);
            var col = colors[colorIdx % colors.length];
            datasets.push({ label: name, data: values, backgroundColor: col + (chartType === 'bar' ? 'E6' : '40'), borderColor: col, borderWidth: 2, fill: isFill, tension: 0.3, type: chartType === 'combo' ? (colorIdx === 0 ? 'bar' : 'line') : undefined });
            colorIdx++;
        });
    }

    // Add statistics overlays (min, max, avg, trendline)
    var showMin = document.getElementById('gf_show_min') && document.getElementById('gf_show_min').checked;
    var showMax = document.getElementById('gf_show_max') && document.getElementById('gf_show_max').checked;
    var showAvg = document.getElementById('gf_show_avg') && document.getElementById('gf_show_avg').checked;
    var showTrend = document.getElementById('gf_show_trendline') && document.getElementById('gf_show_trendline').checked;
    var showR2 = document.getElementById('gf_show_r_squared') && document.getElementById('gf_show_r_squared').checked;

    if (datasets.length > 0) {
        var firstData = datasets[0].data;
        var nonZero = firstData.filter(function(v) { return v > 0; });
        if (nonZero.length > 0) {
            var minVal = Math.min.apply(null, nonZero);
            var maxVal = Math.max.apply(null, firstData);
            var avgVal = firstData.reduce(function(a, b) { return a + b; }, 0) / firstData.length;

            if (showMin) {
                datasets.push({ label: 'Min (' + minVal.toFixed(2) + ')', data: Array(labels.length).fill(minVal), type: 'line', borderColor: '#17A2B8', borderDash: [4, 4], borderWidth: 1.5, pointRadius: 0, fill: false });
            }
            if (showMax) {
                datasets.push({ label: 'Max (' + maxVal.toFixed(2) + ')', data: Array(labels.length).fill(maxVal), type: 'line', borderColor: '#DC3545', borderDash: [4, 4], borderWidth: 1.5, pointRadius: 0, fill: false });
            }
            if (showAvg) {
                datasets.push({ label: 'Avg (' + avgVal.toFixed(2) + ')', data: Array(labels.length).fill(avgVal), type: 'line', borderColor: '#28A745', borderDash: [6, 3], borderWidth: 2, pointRadius: 0, fill: false });
            }
            if (showTrend) {
                // Linear regression trendline
                var n = firstData.length, sx = 0, sy = 0, sxy = 0, sx2 = 0;
                for (var i = 0; i < n; i++) { sx += i; sy += firstData[i]; sxy += i * firstData[i]; sx2 += i * i; }
                var slope = (n * sxy - sx * sy) / Math.max(n * sx2 - sx * sx, 1);
                var intercept = (sy - slope * sx) / n;
                var trendData = [];
                var ssTot = 0, ssRes = 0, mean = sy / n;
                for (var i = 0; i < n; i++) {
                    var predicted = slope * i + intercept;
                    trendData.push(parseFloat(predicted.toFixed(4)));
                    ssTot += Math.pow(firstData[i] - mean, 2);
                    ssRes += Math.pow(firstData[i] - predicted, 2);
                }
                var r2 = ssTot > 0 ? (1 - ssRes / ssTot) : 0;
                var trendLabel = 'Trendline' + (showR2 ? ' (R²=' + r2.toFixed(4) + ')' : '');
                datasets.push({ label: trendLabel, data: trendData, type: 'line', borderColor: '#FFC107', borderDash: [8, 4], borderWidth: 2, pointRadius: 0, fill: false });
            }
        }
    }

    var activeColors = getActiveColors();
    var showLegend = document.getElementById('sec_show_legend') ? document.getElementById('sec_show_legend').checked : true;
    var showGrid = document.getElementById('sec_show_grid') ? document.getElementById('sec_show_grid').checked : true;
    var actualType = chartType === 'stacked_bar' ? 'bar' : (chartType === 'area' ? 'line' : (chartType === 'combo' ? 'bar' : chartType));

    var ctx = document.getElementById('secChart').getContext('2d');
    if (secChart) secChart.destroy();

    var plugins = [];
    if (showReading) plugins.push(ChartDataLabels);

    secChart = new Chart(ctx, {
        type: actualType,
        data: { labels: labels, datasets: datasets },
        plugins: plugins,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: showLegend, position: 'top' },
                title: {
                    display: true,
                    text: 'SEC Analysis (' + unitLabel + ')',
                    font: { size: 16, weight: 'bold' }
                },
                datalabels: showReading ? {
                    anchor: 'end', align: 'top', font: { size: 10 },
                    formatter: function(value) { return value > 0 ? value.toFixed(2) : ''; }
                } : false
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: { display: true, text: unitLabel },
                    grid: { display: showGrid },
                    stacked: chartType === 'stacked_bar'
                },
                x: {
                    title: { display: true, text: timePeriod === 'monthly' ? 'Month' : 'Year' },
                    grid: { display: false },
                    stacked: chartType === 'stacked_bar'
                }
            }
        }
    });
}

function renderPieChart(filteredData, selectedProducts, productNames, convFactor, unitLabel, showReading) {
    var contentArea = document.getElementById('contentArea');
    var numYears = filteredData.length;
    var colClass = numYears <= 2 ? 'col-md-6' : (numYears <= 3 ? 'col-md-4' : 'col-md-3');

    // Title bar with Filter button
    var html = '';
    html += '<div class="d-flex justify-content-between align-items-start mb-4">';
    html += '<div>';
    html += '<h4 class="fw-bold mb-1" style="color: #2E5AA5;">SEC Analytic Graph</h4>';
    html += '<p class="text-muted mb-0" style="font-size: 0.9rem;">Statistics</p>';
    html += '</div>';
    html += '<button class="btn btn-primary btn-sm d-flex align-items-center gap-2" onclick="openGraphFilterModal()" style="border-radius: 8px; padding: 8px 18px; font-weight: 600;">';
    html += '<i class="bi bi-funnel-fill"></i> Filter';
    html += '</button>';
    html += '</div>';

    // Build shared legend from selectedProducts (same labels across all year pie charts)
    var _secLegendHtml = '<div class="d-flex justify-content-center flex-wrap gap-4 mb-3 pie-shared-legend">';
    var _secCI = 0;
    selectedProducts.forEach(function(pid) {
        if (pid === 'total') return;
        var name = productNames[pid] || 'Product ' + pid;
        _secLegendHtml += '<span class="d-flex align-items-center gap-2"><span style="width:12px;height:12px;background:' + chartColors[_secCI % chartColors.length] + ';border-radius:2px;display:inline-block;"></span><small>' + escapeHtml(name) + '</small></span>';
        _secCI++;
    });
    _secLegendHtml += '</div>';
    html += _secLegendHtml;

    html += '<div class="row">';
    filteredData.forEach(function(yearData, idx) {
        html += '<div class="' + colClass + ' mb-4"><div style="position: relative; height: 350px;"><canvas id="pieChart_' + idx + '"></canvas></div></div>';
    });
    html += '</div>';
    contentArea.innerHTML = html;

    filteredData.forEach(function(yearData, idx) {
        var labels = [];
        var values = [];
        var colors = [];
        var colorIdx = 0;

        selectedProducts.forEach(function(pid) {
            if (pid === 'total') return; // skip total for pie
            var val;
            val = parseFloat(yearData.yearly_sec[pid] || 0) * convFactor;
            var name = productNames[pid] || 'Product ' + pid;
            labels.push(name);
            values.push(parseFloat(val.toFixed(4)));
            colors.push(chartColors[colorIdx % chartColors.length]);
            colorIdx++;
        });

        var ctx = document.getElementById('pieChart_' + idx).getContext('2d');
        var plugins = [];
        if (showReading) plugins.push(ChartDataLabels);

        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: colors,
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            plugins: plugins,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    title: {
                        display: true,
                        text: 'SEC ' + yearData.year + ' (' + unitLabel + ')',
                        font: { size: 14, weight: 'bold' }
                    },
                    datalabels: showReading ? {
                        color: '#fff',
                        font: { size: 11, weight: 'bold' },
                        formatter: function(value, context) {
                            var total = context.dataset.data.reduce(function(a, b) { return a + b; }, 0);
                            var pct = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                            return pct + '%';
                        }
                    } : false
                }
            }
        });
    });
}
</script>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    updateStatus();
    updateSecEnergyCount();
});
</script>
@endsection
