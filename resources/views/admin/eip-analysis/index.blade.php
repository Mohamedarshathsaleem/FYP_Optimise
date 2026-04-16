@extends('layouts.dashboard')

@section('title', 'EIP Analysis')


@section('page-title', 'EIP Analysis')
@section('page-title-main', 'Energy Intensity Performance (EIP) Analysis')

@section('content')


@include('partials._header-dashboard')

<!-- Instructions -->
<div class="card border-0 shadow-sm mb-4" id="instructionsCard" style="background:#e3f2fd;border-left:4px solid #2196f3">
    <div class="card-body p-4 d-flex justify-content-between align-items-start">
        <div class="d-flex align-items-start">
            <div class="me-3">
                <div class="bg-primary text-white rounded-circle d-flex justify-content-center align-items-center" style="width:30px;height:30px;">
                    <i class="bi bi-info-lg"></i>
                </div>
            </div>
            <div>
                <h6 class="fw-bold text-primary mb-2">Instructions</h6>
                <p class="mb-0">1. To analyze EIP, complete the desired year range, choose the variable type, and define the display matrix as either a table or a graph. All energy types are loaded by default — use the Filter button to control which energy types are displayed and included in calculations.</p>
            </div>
        </div>
        <button class="btn-close" onclick="closeInstructions()"></button>
    </div>
</div>

<!-- Active Filter Chips -->
<div id="activeFilterChips" class="mb-3 d-flex flex-wrap gap-2" style="display:none!important;"></div>

<!-- Main Filters -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <div class="row g-3">
            <div class="col-md-2">
                <label class="form-label fw-bold">Category <span class="text-danger">*</span></label>
                <select class="form-select" id="yearStart" onchange="updateContent()">
                    <option value="">Start year</option>
                    @foreach($years as $year)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold">&nbsp;</label>
                <select class="form-select" id="yearEnd" onchange="updateContent()">
                    <option value="">End year</option>
                    @foreach(array_reverse($years) as $year)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold">&nbsp;</label>
                <select class="form-select" id="variableType" onchange="updateContent()">
                    <option value="">Variable type</option>
                    @foreach($variables as $var)
                        <option value="{{ $var->id }}">{{ $var->variable_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold">&nbsp;</label>
                <select class="form-select" id="matrixSelect" onchange="updateContent()">
                    <option value="">Matrix</option>
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

<!-- Statistics Panel (shown below chart when enabled) -->
<div id="statisticsPanel" class="card border-0 shadow-sm mt-3" style="display:none;">
    <div class="card-body p-3">
        <h6 class="fw-bold text-muted mb-3"><i class="bi bi-bar-chart-line me-2"></i>Statistical Summary</h6>
        <div class="row" id="statisticsContent"></div>
    </div>
</div>

<!-- Variable Graph Card (shown when regression loaded) -->
<div id="varGraphCard" class="card border-0 shadow-sm mt-3" style="display:none;">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-start mb-3">
            <div>
                <h4 class="fw-bold mb-1" style="color:#2E5AA5;">Variable Graph</h4>
                <p class="text-muted mb-0" style="font-size:0.9rem;" id="varGraphSubtitle">Scatter plot with regression</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-primary btn-sm" onclick="openGraphFilterModal()" style="border-radius:8px;"><i class="bi bi-funnel-fill me-1"></i>Filter</button>
                <button class="btn btn-outline-secondary btn-sm" onclick="document.getElementById('varGraphCard').style.display='none'"><i class="bi bi-x-lg"></i></button>
            </div>
        </div>
        <div id="varGraphInfo" class="alert alert-light py-2 px-3 mb-3" style="font-size:0.8rem;border:1px solid #e0e0e0;border-radius:10px;display:none;"></div>
        <div style="position:relative;height:500px;"><canvas id="varGraphCanvas"></canvas></div>
    </div>
</div>

<!-- ======================== ENHANCED GRAPH FILTER MODAL ======================== -->
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
                            <input type="text" class="form-control" placeholder="Search filters..." id="graphFilterSearch" oninput="searchFilters(this.value)">
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-secondary btn-sm" onclick="resetAllFilters()" title="Reset all"><i class="bi bi-arrow-counterclockwise"></i></button>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                </div>

                <!-- Quick Insights Preview -->
                <div id="insightPanel" class="alert alert-light py-2 px-3 mb-3" style="font-size:0.8rem; border:1px solid #e0e0e0; border-radius:10px;">
                    <span class="text-muted"><i class="bi bi-lightbulb me-1"></i>Select filters and click Apply to see results</span>
                </div>

                <!-- Tab Navigation -->
                <ul class="nav nav-tabs nav-fill mb-3" id="filterTabs" role="tablist" style="font-size:0.8rem;">
                    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab_chart">Chart & Display</a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab_energy">Energy Sources</a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab_vargraph">Variable Graph</a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab_colors">Colors</a></li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content" style="font-size: 0.85rem; min-height: 350px; max-height: 450px; overflow-y: auto;">

                    <!-- ========== TAB 1: CHART & DISPLAY ========== -->
                    <div class="tab-pane fade show active" id="tab_chart">
                        <div class="row">
                            <div class="col-md-4">
                                <h6 class="fw-bold text-muted mb-3">Chart Type</h6>
                                @php $chartTypes = [
                                    'bar' => ['Bar Chart', 'bi-bar-chart'],
                                    'line' => ['Line Chart', 'bi-graph-up'],
                                    'pie' => ['Pie Chart', 'bi-pie-chart'],
                                    'stacked_bar' => ['Stacked Bar', 'bi-bar-chart-steps'],
                                    'area' => ['Area Chart', 'bi-graph-up'],
                                    'combo' => ['Combo (Bar+Line)', 'bi-bar-chart-line'],
                                ]; @endphp
                                @foreach($chartTypes as $key => $info)
                                <div class="form-check mb-2">
                                    <input class="form-check-input gf-chart-type" type="radio" name="gf_chart_type" value="{{ $key }}" id="gf_ct_{{ $key }}" {{ $key === 'bar' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="gf_ct_{{ $key }}"><i class="bi {{ $info[1] }} me-1"></i>{{ $info[0] }}</label>
                                </div>
                                @endforeach

                                <h6 class="fw-bold text-muted mb-3 mt-3">Time Period</h6>
                                <div class="form-check mb-2">
                                    <input class="form-check-input gf-time-period" type="radio" name="gf_time_period" value="yearly" id="gf_tp_yearly" checked>
                                    <label class="form-check-label" for="gf_tp_yearly">Yearly</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input gf-time-period" type="radio" name="gf_time_period" value="monthly" id="gf_tp_monthly">
                                    <label class="form-check-label" for="gf_tp_monthly">Monthly</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <h6 class="fw-bold text-muted mb-3">Year Selection</h6>
                                <div id="gf_year_checkboxes">
                                    <small class="text-muted">Load data first to see available years</small>
                                </div>

                                <h6 class="fw-bold text-muted mb-3 mt-3">Energy Unit</h6>
                                @php $units = [
                                    'GJ' => 'Gigajoule (GJ)',
                                    'MJ' => 'Megajoule (MJ)',
                                    'kWh' => 'Kilowatt-hour (kWh)',
                                    'MWh' => 'Megawatt-hour (MWh)',
                                    'BTU' => 'British Thermal Unit (BTU)',
                                    'MMBTU' => 'Million BTU (MMBTU)',
                                ]; @endphp
                                @foreach($units as $key => $label)
                                <div class="form-check mb-1">
                                    <input class="form-check-input" type="radio" name="gf_energy_unit" value="{{ $key }}" id="gf_unit_{{ $key }}" {{ $key === 'GJ' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="gf_unit_{{ $key }}">{{ $label }}</label>
                                </div>
                                @endforeach
                            </div>
                            <div class="col-md-4">
                                <h6 class="fw-bold text-muted mb-3">Display Options</h6>
                                <div class="form-check mb-2">
                                    <input class="form-check-input gf-show-reading" type="checkbox" id="gf_show_reading">
                                    <label class="form-check-label" for="gf_show_reading">Show data labels</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="gf_show_legend" checked>
                                    <label class="form-check-label" for="gf_show_legend">Show legend</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="gf_show_grid" checked>
                                    <label class="form-check-label" for="gf_show_grid">Show grid lines</label>
                                </div>

                                <h6 class="fw-bold text-muted mb-3 mt-3">Export</h6>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-outline-secondary btn-sm" onclick="exportChartPNG()"><i class="bi bi-image me-1"></i>PNG</button>
                                    <button class="btn btn-outline-secondary btn-sm" onclick="exportDataCSV()"><i class="bi bi-file-earmark-spreadsheet me-1"></i>CSV</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ========== TAB 2: ENERGY SOURCES ========== -->
                    <div class="tab-pane fade" id="tab_energy">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <button class="btn btn-outline-primary btn-sm me-2" onclick="toggleAllEnergySources(true)">Select All</button>
                                <button class="btn btn-outline-secondary btn-sm" onclick="toggleAllEnergySources(false)">Deselect All</button>
                            </div>
                            <span class="badge bg-primary" id="energySelectedCount">0 selected</span>
                        </div>
                        <input type="text" class="form-control form-control-sm mb-3" placeholder="Search energy sources..." oninput="filterEnergySources(this.value)">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="fw-bold text-muted mb-3"><i class="bi bi-lightning-charge me-1"></i>Energy</h6>
                                @foreach($energySources as $idx => $es)
                                <div class="form-check mb-2 energy-source-item">
                                    <input class="form-check-input gf-energy-source" type="checkbox" value="energy_{{ $es->id }}" id="gf_es_{{ $es->id }}" checked onchange="updateEnergySourceCount()">
                                    <span class="d-inline-block rounded-circle me-1" style="width:10px;height:10px;background:{{ ['#2563EB','#DC2626','#059669','#D97706','#7C3AED','#DB2777'][$idx % 6] }};"></span>
                                    <label class="form-check-label" for="gf_es_{{ $es->id }}">{{ $es->energy_type }}</label>
                                </div>
                                @endforeach
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-bold text-muted mb-3"><i class="bi bi-droplet me-1"></i>Energy Resource</h6>
                                @foreach($resourceSources as $idx => $rs)
                                <div class="form-check mb-2 energy-source-item">
                                    <input class="form-check-input gf-energy-source" type="checkbox" value="resource_{{ $rs->id }}" id="gf_rs_{{ $rs->id }}" checked onchange="updateEnergySourceCount()">
                                    <span class="d-inline-block rounded-circle me-1" style="width:10px;height:10px;background:{{ ['#0891B2','#EA580C','#4F46E5','#16A34A','#CA8A04','#9333EA'][$idx % 6] }};"></span>
                                    <label class="form-check-label" for="gf_rs_{{ $rs->id }}">{{ $rs->resource_type }}</label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- ========== TAB 3: VARIABLE GRAPH ========== -->
                    <div class="tab-pane fade" id="tab_vargraph">
                        <div class="row">
                            <!-- X-Axis Variable Selector -->
                            <div class="col-md-4">
                                <h6 class="fw-bold text-muted mb-2">X-Axis Variables</h6>
                                <small class="text-muted d-block mb-2">Select one or more variables for X-axis</small>
                                <input type="text" class="form-control form-control-sm mb-2" placeholder="Search variables..." oninput="filterVarItems(this.value, '.vg-xvar-item')">
                                <div style="max-height:250px;overflow-y:auto;">
                                    @foreach($variables as $var)
                                    <div class="form-check mb-2 vg-xvar-item">
                                        <input class="form-check-input vg-xvar" type="checkbox" value="{{ $var->id }}" id="vg_xvar_{{ $var->id }}" data-unit="{{ $var->variable_unit ?? '' }}">
                                        <label class="form-check-label" for="vg_xvar_{{ $var->id }}">
                                            {{ $var->variable_name }}
                                            @if($var->variable_unit)
                                                <small class="text-muted">({{ $var->variable_unit }})</small>
                                            @endif
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                                <div class="mt-2">
                                    <a href="#" class="small text-primary" onclick="event.preventDefault();document.querySelectorAll('.vg-xvar').forEach(function(c){c.checked=false;})">Clear All</a>
                                </div>
                            </div>

                            <!-- Y-Axis Energy/Resource Selector -->
                            <div class="col-md-4">
                                <h6 class="fw-bold text-muted mb-2">Y-Axis Energy Sources</h6>
                                <small class="text-muted d-block mb-2">Sum of selected sources = Y value (GJ)</small>
                                <input type="text" class="form-control form-control-sm mb-2" placeholder="Search energy..." oninput="filterVarItems(this.value, '.vg-ysrc-item')">
                                <div style="max-height:250px;overflow-y:auto;">
                                    <div class="mb-2 fw-bold small text-primary"><i class="bi bi-lightning-charge me-1"></i>Energy</div>
                                    @foreach($energySources as $es)
                                    <div class="form-check mb-1 vg-ysrc-item">
                                        <input class="form-check-input vg-y-energy" type="checkbox" value="{{ $es->id }}" id="vg_ye_{{ $es->id }}" checked>
                                        <label class="form-check-label" for="vg_ye_{{ $es->id }}">{{ $es->energy_type }}</label>
                                    </div>
                                    @endforeach
                                    <div class="mb-2 mt-3 fw-bold small text-info"><i class="bi bi-droplet me-1"></i>Energy Resource</div>
                                    @foreach($resourceSources as $rs)
                                    <div class="form-check mb-1 vg-ysrc-item">
                                        <input class="form-check-input vg-y-resource" type="checkbox" value="{{ $rs->id }}" id="vg_yr_{{ $rs->id }}" checked>
                                        <label class="form-check-label" for="vg_yr_{{ $rs->id }}">{{ $rs->resource_type }}</label>
                                    </div>
                                    @endforeach
                                </div>
                                <div class="mt-2">
                                    <a href="#" class="small text-primary" onclick="event.preventDefault();document.querySelectorAll('.vg-y-energy,.vg-y-resource').forEach(function(c){c.checked=true;})">Select All</a>
                                    | <a href="#" class="small text-primary" onclick="event.preventDefault();document.querySelectorAll('.vg-y-energy,.vg-y-resource').forEach(function(c){c.checked=false;})">Clear All</a>
                                </div>
                            </div>

                            <!-- Regression Options -->
                            <div class="col-md-4">
                                <h6 class="fw-bold text-muted mb-2">Regression Options</h6>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="vg_show_trendline" checked>
                                    <label class="form-check-label" for="vg_show_trendline">Show Trendline</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="vg_show_r2" checked>
                                    <label class="form-check-label" for="vg_show_r2">Show R² Value</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="vg_show_equation" checked>
                                    <label class="form-check-label" for="vg_show_equation">Show Equation</label>
                                </div>

                                <h6 class="fw-bold text-muted mb-2 mt-3">Display</h6>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="vg_show_labels">
                                    <label class="form-check-label" for="vg_show_labels">Show Data Labels</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="vg_show_grid" checked>
                                    <label class="form-check-label" for="vg_show_grid">Show Grid Lines</label>
                                </div>

                                <div class="mt-3">
                                    <button class="btn btn-success btn-sm w-100" onclick="loadRegressionGraph()">
                                        <i class="bi bi-graph-up me-1"></i> Load Variable Graph
                                    </button>
                                </div>

                                <div class="mt-3">
                                    <h6 class="fw-bold text-muted mb-2">Export</h6>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-outline-secondary btn-sm" onclick="exportVarGraphPNG()"><i class="bi bi-image me-1"></i>PNG</button>
                                        <button class="btn btn-outline-secondary btn-sm" onclick="exportVarGraphCSV()"><i class="bi bi-file-earmark-spreadsheet me-1"></i>CSV</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ========== TAB 4: COLORS ========== -->
                    <div class="tab-pane fade" id="tab_colors">
                        <h6 class="fw-bold text-muted mb-3">Color Scheme</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="eip_color_scheme" value="default" id="eip_cs_default" checked>
                                    <label class="form-check-label" for="eip_cs_default">Default</label>
                                </div>
                                <div class="d-flex gap-1 ms-4">
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#2563EB;"></span>
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#DC2626;"></span>
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#059669;"></span>
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#D97706;"></span>
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#7C3AED;"></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="eip_color_scheme" value="pastel" id="eip_cs_pastel">
                                    <label class="form-check-label" for="eip_cs_pastel">Pastel</label>
                                </div>
                                <div class="d-flex gap-1 ms-4">
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#93C5FD;"></span>
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#FCA5A5;"></span>
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#6EE7B7;"></span>
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#FCD34D;"></span>
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#C4B5FD;"></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="eip_color_scheme" value="corporate" id="eip_cs_corporate">
                                    <label class="form-check-label" for="eip_cs_corporate">Corporate</label>
                                </div>
                                <div class="d-flex gap-1 ms-4">
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#1E3A5F;"></span>
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#4472C4;"></span>
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#70AD47;"></span>
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#ED7D31;"></span>
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#FFC000;"></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="eip_color_scheme" value="vibrant" id="eip_cs_vibrant">
                                    <label class="form-check-label" for="eip_cs_vibrant">Vibrant</label>
                                </div>
                                <div class="d-flex gap-1 ms-4">
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#FF6B6B;"></span>
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#4ECDC4;"></span>
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#45B7D1;"></span>
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#96CEB4;"></span>
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#FFEAA7;"></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="eip_color_scheme" value="ocean" id="eip_cs_ocean">
                                    <label class="form-check-label" for="eip_cs_ocean">Ocean</label>
                                </div>
                                <div class="d-flex gap-1 ms-4">
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#0077B6;"></span>
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#00B4D8;"></span>
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#48CAE4;"></span>
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#90E0EF;"></span>
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#023E8A;"></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="eip_color_scheme" value="warm" id="eip_cs_warm">
                                    <label class="form-check-label" for="eip_cs_warm">Warm</label>
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
                    <button type="button" class="btn btn-outline-secondary px-4" onclick="resetAllFilters()" style="border-radius: 25px;">Clear All</button>
                    <button type="button" class="btn btn-primary px-5 py-2" onclick="applyGraphFilter()" style="border-radius: 25px; min-width: 200px;">Apply & View</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Mobile Floating Filter Button -->
<button class="btn btn-primary rounded-circle d-md-none position-fixed shadow-lg" style="bottom:20px;right:20px;width:56px;height:56px;z-index:1050;" onclick="openGraphFilterModal()">
    <i class="bi bi-funnel-fill"></i>
</button>

<style>
    #graphFilterModal .nav-tabs .nav-link { font-size: 0.8rem; padding: 0.5rem 0.75rem; }
    #graphFilterModal .nav-tabs .nav-link.active { color: #4472C4; border-bottom: 2px solid #4472C4; font-weight: 600; }
    .filter-chip { background: #E9EFFF; color: #4472C4; border-radius: 20px; padding: 4px 12px; font-size: 0.75rem; display: inline-flex; align-items: center; gap: 4px; }
    .filter-chip .btn-close { font-size: 0.5rem; }
    @media (max-width: 768px) {
        #graphFilterModal .modal-dialog { max-width: 100%; margin: 0.5rem; }
        #graphFilterModal .tab-content { max-height: 60vh; }
        #graphFilterModal .row > .col-md-4, #graphFilterModal .row > .col-md-6 { margin-bottom: 1rem; }
    }
</style>

<script>
var csrfToken = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '';
var monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
var matrixData = null;
var currentMatrixType = null;
var eipChart = null;
var graphFilterResult = null;
var _eipTargets = @json($targets ?? []);

var unitConversions = {
    'J': 1000000000, 'kJ': 1000000, 'MJ': 1000, 'GJ': 1,
    'Wh': 277777778, 'kWh': 277778, 'MWh': 277.778, 'GWh': 0.277778,
    'BTU': 947817120, 'MMBTU': 947.817
};
var chartColorSchemes = {
    default: ['#2563EB','#DC2626','#059669','#D97706','#7C3AED','#DB2777','#0891B2','#EA580C','#4F46E5','#16A34A','#CA8A04','#9333EA'],
    pastel: ['#93C5FD','#FCA5A5','#6EE7B7','#FCD34D','#C4B5FD','#FBCFE8','#67E8F9','#FDBA74','#A5B4FC','#86EFAC','#FDE047','#D8B4FE'],
    corporate: ['#1E3A5F','#4472C4','#70AD47','#ED7D31','#FFC000','#5B9BD5','#A5A5A5','#264478','#9DC3E6','#548235','#BF8F00','#7030A0'],
    vibrant: ['#FF6B6B','#4ECDC4','#45B7D1','#96CEB4','#FFEAA7','#DDA0DD','#98D8C8','#F7DC6F','#BB8FCE','#82E0AA','#F8C471','#AED6F1'],
    ocean: ['#0077B6','#00B4D8','#48CAE4','#90E0EF','#023E8A','#0096C7','#CAF0F8','#ADE8F4','#03045E','#0077B6','#00B4D8','#48CAE4'],
    warm: ['#E63946','#F4A261','#E9C46A','#2A9D8F','#264653','#D62828','#F77F00','#FCBF49','#EAE2B7','#003049','#C1121F','#669BBC']
};
function getActiveColors() {
    var colorRadio = document.querySelector('input[name="eip_color_scheme"]:checked');
    var scheme = colorRadio ? colorRadio.value : 'default';
    return chartColorSchemes[scheme] || chartColorSchemes['default'];
}
var chartColors = chartColorSchemes['default'];

// ===== UTILITY =====
window.closeInstructions = function() { document.getElementById('instructionsCard').style.display = 'none'; };
function formatNum(num) { if (!num || num === 0) return '0'; return parseFloat(num).toLocaleString('en-US', { maximumFractionDigits: 0 }); }
function formatEip(num) { if (!num || num === 0) return '0.0000'; return parseFloat(num).toFixed(4); }

// ===== FILTER SEARCH =====
window.searchFilters = function(term) {
    term = term.toLowerCase();
    document.querySelectorAll('#graphFilterModal .tab-pane .form-check-label, #graphFilterModal .tab-pane h6').forEach(function(el) {
        var parent = el.closest('.form-check') || el;
        if (term === '') { parent.style.display = ''; return; }
        parent.style.display = el.textContent.toLowerCase().includes(term) ? '' : 'none';
    });
};

// ===== ENERGY SOURCE HELPERS =====
window.toggleAllEnergySources = function(checked) {
    document.querySelectorAll('.gf-energy-source').forEach(function(cb) { cb.checked = checked; });
    updateEnergySourceCount();
};
window.filterEnergySources = function(term) {
    term = term.toLowerCase();
    document.querySelectorAll('.energy-source-item').forEach(function(el) { el.style.display = el.textContent.toLowerCase().includes(term) ? '' : 'none'; });
};
window.updateEnergySourceCount = function() {
    var total = document.querySelectorAll('.gf-energy-source').length;
    var checked = document.querySelectorAll('.gf-energy-source:checked').length;
    document.getElementById('energySelectedCount').textContent = checked + ' / ' + total + ' selected';
};

// ===== DATE PRESET (no-op: date filter tabs removed) =====
window.setDatePreset = function(preset) {};

// ===== POPULATE YEAR CHECKBOXES =====
function populateYearCheckboxes(dataArray) {
    var container = document.getElementById('gf_year_checkboxes');
    if (!container || !dataArray || dataArray.length === 0) return;
    var years = dataArray.map(function(d) { return d.year; });
    var html = '<div class="d-flex flex-wrap gap-2">';
    years.forEach(function(y) {
        html += '<div class="form-check form-check-inline">'
            + '<input class="form-check-input gf-year" type="checkbox" value="' + y + '" id="gf_year_' + y + '" checked>'
            + '<label class="form-check-label" for="gf_year_' + y + '">' + y + '</label>'
            + '</div>';
    });
    html += '</div>';
    html += '<div class="mt-2"><a href="#" class="small text-primary" onclick="event.preventDefault();document.querySelectorAll(\'.gf-year\').forEach(function(c){c.checked=true;})">Select All</a>'
        + ' | <a href="#" class="small text-primary" onclick="event.preventDefault();document.querySelectorAll(\'.gf-year\').forEach(function(c){c.checked=false;})">Clear All</a></div>';
    container.innerHTML = html;
}

// ===== RESET ALL FILTERS =====
window.resetAllFilters = function() {
    // Years - select all
    document.querySelectorAll('.gf-year').forEach(function(cb) { cb.checked = true; });
    // Energy sources - select all
    document.querySelectorAll('.gf-energy-source').forEach(function(cb) { cb.checked = true; });
    // Chart type - reset to bar
    var barRadio = document.getElementById('gf_ct_bar');
    if (barRadio) barRadio.checked = true;
    // Display options
    var showReading = document.getElementById('gf_show_reading');
    if (showReading) showReading.checked = false;
    var showLegend = document.getElementById('gf_show_legend');
    if (showLegend) showLegend.checked = true;
    var showGrid = document.getElementById('gf_show_grid');
    if (showGrid) showGrid.checked = true;
    // Time period - reset to yearly
    var yearlyRadio = document.getElementById('gf_tp_yearly');
    if (yearlyRadio) yearlyRadio.checked = true;
    // Energy unit - reset to GJ
    var gjRadio = document.getElementById('gf_unit_GJ');
    if (gjRadio) gjRadio.checked = true;
    // Color scheme - reset to default
    var defaultColor = document.getElementById('eip_cs_default');
    if (defaultColor) defaultColor.checked = true;
    updateEnergySourceCount();
};

// ===== GET ALL FILTER VALUES =====
window.getFilterValues = function() {
    var f = {};
    // Year checkboxes
    f.years = []; document.querySelectorAll('.gf-year:checked').forEach(function(cb) { f.years.push(parseInt(cb.value)); });
    // Energy sources
    f.energySources = []; document.querySelectorAll('.gf-energy-source:checked').forEach(function(cb) { f.energySources.push(cb.value); });
    // Chart type
    var chartRadio = document.querySelector('input[name="gf_chart_type"]:checked');
    f.chartType = chartRadio ? chartRadio.value : 'bar';
    // Display options
    var showReadingEl = document.getElementById('gf_show_reading');
    f.showReading = showReadingEl ? showReadingEl.checked : false;
    var showLegendEl = document.getElementById('gf_show_legend');
    f.showLegend = showLegendEl ? showLegendEl.checked : true;
    var showGridEl = document.getElementById('gf_show_grid');
    f.showGrid = showGridEl ? showGridEl.checked : true;
    // Time period
    var tpRadio = document.querySelector('input[name="gf_time_period"]:checked');
    f.timePeriod = tpRadio ? tpRadio.value : 'yearly';
    // Energy unit
    var unitRadio = document.querySelector('input[name="gf_energy_unit"]:checked');
    f.energyUnit = unitRadio ? unitRadio.value : 'GJ';
    // Colors
    var colorRadio = document.querySelector('input[name="eip_color_scheme"]:checked');
    f.colorScheme = colorRadio ? colorRadio.value : 'default';
    // Defaults for removed filters
    f.datePreset = null;
    f.customStart = null;
    f.customEnd = null;
    f.quarters = [];
    f.normalization = 'none';
    f.hideZero = false;
    f.minConsumption = null;
    f.maxConsumption = null;
    f.overlays = [];
    f.manualTarget = null;
    f.showStatistics = false;
    f.trendFilters = [];
    f.granularity = 'monthly';
    f.aggregationMethod = 'sum';
    return f;
};

// ===== STATUS & CONTENT =====
window.updateStatus = function() {
    var y1 = document.getElementById('yearStart').value, y2 = document.getElementById('yearEnd').value;
    var v = document.getElementById('variableType').value, matrix = document.getElementById('matrixSelect').value;
    var msg = document.getElementById('statusMessage');
    if (!msg) return;
    if (!y1) msg.textContent = 'Please complete category';
    else if (!y2) msg.textContent = 'Please select year range';
    else if (!v) msg.textContent = 'Please select variable type';
    else if (!matrix) msg.textContent = 'Please select matrix';
    else msg.textContent = 'Loading...';
};
window.updateContent = function() {
    updateStatus();
    var y1 = document.getElementById('yearStart').value, y2 = document.getElementById('yearEnd').value;
    var v = document.getElementById('variableType').value, matrix = document.getElementById('matrixSelect').value;
    if (y1 && y2 && v && matrix) loadMatrixData(matrix);
};

// ===== LOAD MATRIX DATA =====
function loadMatrixData(matrixType) {
    currentMatrixType = matrixType;
    var y1 = document.getElementById('yearStart').value, y2 = document.getElementById('yearEnd').value;
    var v = document.getElementById('variableType').value;
    var filters = getFilterValues();
    var body = {
        year_start: parseInt(y1), year_end: parseInt(y2), variable_type: parseInt(v),
        energy_source_ids: filters.energySources.filter(function(s) { return s.startsWith('energy_'); }).map(function(s) { return parseInt(s.replace('energy_', '')); }),
        resource_source_ids: filters.energySources.filter(function(s) { return s.startsWith('resource_'); }).map(function(s) { return parseInt(s.replace('resource_', '')); }),
        date_preset: filters.datePreset, custom_start: filters.customStart, custom_end: filters.customEnd,
        quarters: filters.quarters.length < 4 ? filters.quarters : null,
        normalization_type: filters.normalization, hide_zero: filters.hideZero,
        min_consumption: filters.minConsumption, max_consumption: filters.maxConsumption,
    };
    fetch('/eip-analysis/data/matrix', {
        method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        body: JSON.stringify(body)
    })
    .then(function(r) {
        if (!r.ok) throw new Error('HTTP ' + r.status + ': ' + r.statusText);
        return r.json();
    })
    .then(function(result) {
        if (result.success) {
            matrixData = result;
            // Store which energy/resource source IDs were used in this fetch
            matrixData._filterEnergyIds = body.energy_source_ids || [];
            matrixData._filterResourceIds = body.resource_source_ids || [];
            // Populate year checkboxes in filter modal
            populateYearCheckboxes(result.data);
            if (matrixType === 'Table') displayTable(result);
            else if (matrixType === 'Graph') displayGraph(result);
        } else {
            document.getElementById('contentArea').innerHTML = '<div class="text-center py-5"><p class="text-danger">Failed to load data: ' + (result.message || 'Unknown error') + '</p></div>';
        }
    })
    .catch(function(err) {
        console.error('Fetch error:', err);
        document.getElementById('contentArea').innerHTML = '<div class="text-center py-5"><p class="text-danger">Error: ' + err.message + '</p></div>';
    });
}

// ===== APPLY RECOMMENDATION =====
window.applyRecommendation = function(action) {
    if (!action) return;
    if (action.date_preset) setDatePreset(action.date_preset);
    if (action.trend_filters) {
        document.querySelectorAll('.gf-trend').forEach(function(cb) { cb.checked = false; });
        action.trend_filters.forEach(function(t) {
            var id = 'gf_trend_' + (t === 'increasing' ? 'inc' : t === 'decreasing' ? 'dec' : t === 'anomalies' ? 'anomaly' : t);
            var el = document.getElementById(id); if (el) el.checked = true;
        });
    }
    if (action.overlays) { action.overlays.forEach(function(o) { var el = document.querySelector('.gf-overlay[value="' + o + '"]'); if (el) el.checked = true; }); }
};

// ===== TABLE =====
window.displayTable = function(result) { renderFilteredTable(result); };
window.renderFilteredTable = function(result) {
    var contentArea = document.getElementById('contentArea');
    var selectedYears = [];
    document.querySelectorAll('.gf-year:checked').forEach(function(cb) { selectedYears.push(parseInt(cb.value)); });
    if (selectedYears.length === 0) selectedYears = result.data.map(function(d) { return d.year; });
    var filteredData = result.data.filter(function(d) { return selectedYears.indexOf(d.year) !== -1; });
    var html = '<div class="d-flex justify-content-end mb-3"><button class="btn btn-primary btn-sm d-flex align-items-center gap-2" onclick="openGraphFilterModal()" style="border-radius:8px;padding:8px 18px;font-weight:600;"><i class="bi bi-funnel-fill"></i> Filter</button></div>';
    html += buildEipTotalTable(result.eip_total_table, filteredData);
    html += buildMatrixTable(filteredData, result.energy_source_names, result.resource_source_names, result.variable_type);
    contentArea.innerHTML = html;
};

function buildEipTotalTable(eipTotalTable, data) {
    var years = data.map(function(d) { return d.year; });
    var html = '<div class="card border-0 shadow-sm mb-4"><div class="card-header bg-white d-flex align-items-center justify-content-between px-4 py-3" style="cursor:pointer;" onclick="toggleEipTotal()"><div><h6 class="fw-bold mb-0">EIP Total Table</h6><small class="text-muted">Annual EIP values by metric</small></div><i class="bi bi-chevron-down" id="eipTotalChevron" style="transition:transform 0.3s;"></i></div>';
    html += '<div id="eipTotalCollapse"><div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead class="table-light"><tr><th class="py-3">Metric</th>';
    years.forEach(function(y) { html += '<th class="py-3 text-center">' + y + '</th>'; });
    html += '</tr></thead><tbody>';
    eipTotalTable.forEach(function(row) { html += '<tr><td class="py-2 fw-bold">' + row.metric + '</td>'; years.forEach(function(y) { html += '<td class="py-2 text-center">' + (row.years[y] !== undefined ? parseFloat(row.years[y]).toFixed(4) : '-') + '</td>'; }); html += '</tr>'; });
    html += '</tbody></table></div></div></div>';
    return html;
}

function buildMatrixTable(data, esNames, rsNames, varTypeName) {
    var eIds = Object.keys(esNames), rIds = Object.keys(rsNames);
    var html = '<div class="card border-0 shadow-sm"><div class="card-header bg-white d-flex align-items-center justify-content-between px-4 py-3" style="cursor:pointer;" onclick="toggleEipCalc()"><div><h6 class="fw-bold mb-0">EIP Calculation Table</h6><small class="text-muted">Detailed monthly values</small></div><i class="bi bi-chevron-down" id="eipCalcChevron" style="transition:transform 0.3s;"></i></div>';
    html += '<div id="eipCalcCollapse"><div class="table-responsive"><table class="table table-hover align-middle mb-0" style="font-size:0.85rem;">';
    html += '<thead class="table-light"><tr><th rowspan="3" class="text-center align-middle" style="min-width:70px;">Month</th>';
    html += '<th colspan="' + (eIds.length + 1) + '" class="text-center">Energy</th>';
    html += '<th colspan="' + (rIds.length + 1) + '" class="text-center">Energy Resource</th>';
    html += '<th class="text-center">Variable</th><th colspan="2" class="text-center">EIP</th></tr><tr>';
    eIds.forEach(function(id) { html += '<th class="text-center" style="min-width:80px;font-size:0.8rem;">' + esNames[id] + '</th>'; });
    html += '<th class="text-center fw-bold" style="background:rgba(68,114,196,0.06);font-size:0.8rem;">Total</th>';
    rIds.forEach(function(id) { html += '<th class="text-center" style="min-width:80px;font-size:0.8rem;">' + rsNames[id] + '</th>'; });
    html += '<th class="text-center fw-bold" style="background:rgba(68,114,196,0.06);font-size:0.8rem;">Total</th>';
    html += '<th class="text-center" style="font-size:0.8rem;">' + varTypeName + '</th>';
    html += '<th class="text-center fw-bold" style="background:rgba(68,114,196,0.06);font-size:0.8rem;">Energy</th>';
    html += '<th class="text-center fw-bold" style="background:rgba(68,114,196,0.06);font-size:0.8rem;">Resource</th></tr>';
    html += '<tr style="font-size:0.75rem;color:#6c757d;">';
    eIds.forEach(function() { html += '<th class="text-center">GJ</th>'; });
    html += '<th class="text-center" style="background:rgba(68,114,196,0.06);">GJ</th>';
    rIds.forEach(function() { html += '<th class="text-center">GJ</th>'; });
    html += '<th class="text-center" style="background:rgba(68,114,196,0.06);">GJ</th><th class="text-center">m\u00B2</th>';
    html += '<th class="text-center" style="background:rgba(68,114,196,0.06);">GJ/m\u00B2</th><th class="text-center" style="background:rgba(68,114,196,0.06);">GJ/m\u00B2</th></tr></thead><tbody>';

    data.forEach(function(yd) {
        yd.months.forEach(function(m) {
            html += '<tr><td class="text-center fw-semibold">' + monthNames[m.month - 1] + '-' + String(yd.year).slice(2) + '</td>';
            eIds.forEach(function(id) { html += '<td class="text-end">' + formatNum(m.energy[id]) + '</td>'; });
            html += '<td class="text-end fw-bold" style="background:rgba(68,114,196,0.06);">' + formatNum(m.total_energy) + '</td>';
            rIds.forEach(function(id) { html += '<td class="text-end">' + formatNum(m.resource[id]) + '</td>'; });
            html += '<td class="text-end fw-bold" style="background:rgba(68,114,196,0.06);">' + formatNum(m.total_resource) + '</td>';
            html += '<td class="text-end">' + formatNum(m.variable_value) + '</td>';
            html += '<td class="text-end fw-bold" style="background:rgba(68,114,196,0.06);">' + formatEip(m.eip_energy) + '</td>';
            html += '<td class="text-end fw-bold" style="background:rgba(68,114,196,0.06);">' + formatEip(m.eip_resource) + '</td></tr>';
        });
        var yt = yd.yearly_totals, yeip = yd.yearly_eip, rb = 'background:rgba(68,114,196,0.12);', db = 'background:rgba(68,114,196,0.22);';
        html += '<tr><td class="text-center fw-bold" style="' + rb + '">Total/' + yd.year + '</td>';
        eIds.forEach(function(id) { html += '<td class="text-end fw-semibold" style="' + rb + '">' + formatNum(yt.energy[id]) + '</td>'; });
        html += '<td class="text-end fw-bold" style="' + db + '">' + formatNum(yt.total_energy) + '</td>';
        rIds.forEach(function(id) { html += '<td class="text-end fw-semibold" style="' + rb + '">' + formatNum(yt.resource[id]) + '</td>'; });
        html += '<td class="text-end fw-bold" style="' + db + '">' + formatNum(yt.total_resource) + '</td>';
        html += '<td class="text-end fw-semibold" style="' + rb + '">' + formatNum(yt.total_variable) + '</td>';
        html += '<td class="text-end fw-bold" style="' + db + '">' + formatEip(yeip.eip_energy) + '</td>';
        html += '<td class="text-end fw-bold" style="' + db + '">' + formatEip(yeip.eip_resource) + '</td></tr>';
    });
    html += '</tbody></table></div></div></div>';
    return html;
}

// ===== GRAPH =====
window.displayGraph = function(result) {
    graphFilterResult = result;
    var allYears = result.data.map(function(d) { return d.year; });
    var f = getFilterValues();
    chartColors = getActiveColors();
    renderChart(result, allYears, f.chartType, f.timePeriod, unitConversions[f.energyUnit] || 1, f.energyUnit + '/m\u00B2', f.showReading, f);
};
window.applyGraphFilter = function() {
    var modal = bootstrap.Modal.getInstance(document.getElementById('graphFilterModal'));
    if (modal) modal.hide();
    var f = getFilterValues();
    chartColors = getActiveColors();

    // Check if energy sources changed - needs server re-fetch
    var newEnergyIds = f.energySources.filter(function(s) { return s.startsWith('energy_'); }).map(function(s) { return parseInt(s.replace('energy_', '')); });
    var newResourceIds = f.energySources.filter(function(s) { return s.startsWith('resource_'); }).map(function(s) { return parseInt(s.replace('resource_', '')); });
    var prevEnergyIds = matrixData ? (matrixData._filterEnergyIds || []) : [];
    var prevResourceIds = matrixData ? (matrixData._filterResourceIds || []) : [];
    var sourcesChanged = JSON.stringify(newEnergyIds.sort()) !== JSON.stringify(prevEnergyIds.sort()) ||
                         JSON.stringify(newResourceIds.sort()) !== JSON.stringify(prevResourceIds.sort());

    if (sourcesChanged) {
        // Energy sources changed - re-fetch from server, then render
        loadMatrixData(currentMatrixType);
        return;
    }

    if (!matrixData) return;

    // Get selected years (fall back to all years if none checked)
    var selectedYears = f.years;
    if (selectedYears.length === 0) {
        selectedYears = matrixData.data.map(function(d) { return d.year; });
    }

    if (currentMatrixType === 'Table') {
        renderFilteredTable(matrixData);
    } else {
        if (!graphFilterResult) return;
        renderChart(graphFilterResult, selectedYears, f.chartType, f.timePeriod, unitConversions[f.energyUnit] || 1, f.energyUnit + '/m\u00B2', f.showReading, f);
    }
};
window.openGraphFilterModal = function() { updateEnergySourceCount(); new bootstrap.Modal(document.getElementById('graphFilterModal')).show(); };
window.toggleEipTotal = function() { var el = document.getElementById('eipTotalCollapse'), ch = document.getElementById('eipTotalChevron'); if (el.style.display === 'none') { el.style.display = ''; ch.style.transform = 'rotate(0deg)'; } else { el.style.display = 'none'; ch.style.transform = 'rotate(-90deg)'; } };
window.toggleEipCalc = function() { var el = document.getElementById('eipCalcCollapse'), ch = document.getElementById('eipCalcChevron'); if (el.style.display === 'none') { el.style.display = ''; ch.style.transform = 'rotate(0deg)'; } else { el.style.display = 'none'; ch.style.transform = 'rotate(-90deg)'; } };

// ===== CLIENT-SIDE THRESHOLD FILTERING =====
function applyClientThresholds(values, labels, filters) {
    if (!filters) return { values: values, labels: labels };
    var nv = [], nl = [];
    for (var i = 0; i < values.length; i++) {
        var v = values[i];
        if (filters.hideZero && v === 0) continue;
        if (filters.minConsumption !== null && v < filters.minConsumption) continue;
        if (filters.maxConsumption !== null && v > filters.maxConsumption) continue;
        if (filters.pctChangeThreshold !== null && i > 0) { var pct = values[i-1] > 0 ? Math.abs((v - values[i-1]) / values[i-1]) * 100 : 0; if (pct < filters.pctChangeThreshold) continue; }
        nv.push(v); nl.push(labels[i]);
    }
    return { values: nv, labels: nl };
}

// ===== CLIENT-SIDE TREND DETECTION =====
function detectClientTrends(values) {
    var n = values.length;
    if (n < 3) return { trend: 'stable', slope: 0, mean: 0, stdDev: 0, anomalies: [], peakIdx: 0, lowIdx: 0 };
    var sx = 0, sy = 0, sxy = 0, sx2 = 0;
    for (var i = 0; i < n; i++) { sx += i; sy += values[i]; sxy += i * values[i]; sx2 += i * i; }
    var slope = (n * sxy - sx * sy) / Math.max(n * sx2 - sx * sx, 1);
    var mean = sy / n, variance = 0;
    for (var i = 0; i < n; i++) variance += Math.pow(values[i] - mean, 2);
    var stdDev = Math.sqrt(variance / n);
    var anomalies = [];
    for (var i = 0; i < n; i++) { if (stdDev > 0 && Math.abs(values[i] - mean) / stdDev > 2) anomalies.push(i); }
    var peakIdx = values.indexOf(Math.max.apply(null, values));
    var nonZero = values.filter(function(v) { return v > 0; });
    var lowIdx = nonZero.length > 0 ? values.indexOf(Math.min.apply(null, nonZero)) : 0;
    var trend = (mean > 0 ? Math.abs(slope) / mean : 0) < 0.01 ? 'stable' : (slope > 0 ? 'increasing' : 'decreasing');
    return { trend: trend, slope: slope, mean: mean, stdDev: stdDev, anomalies: anomalies, peakIdx: peakIdx, lowIdx: lowIdx };
}

// ===== STATISTICS PANEL =====
function renderStatisticsPanel(values, trends) {
    var panel = document.getElementById('statisticsPanel'), content = document.getElementById('statisticsContent');
    if (!values || values.length === 0) { panel.style.display = 'none'; return; }
    var sorted = values.slice().sort(function(a, b) { return a - b; }), n = sorted.length;
    var median = n % 2 === 0 ? (sorted[n/2-1] + sorted[n/2]) / 2 : sorted[Math.floor(n/2)];
    var cv = trends.mean > 0 ? ((trends.stdDev / trends.mean) * 100).toFixed(1) : '0';
    var ti = trends.trend === 'increasing' ? '<i class="bi bi-graph-up-arrow text-danger"></i>' : trends.trend === 'decreasing' ? '<i class="bi bi-graph-down-arrow text-success"></i>' : '<i class="bi bi-dash-lg text-warning"></i>';
    content.innerHTML = '<div class="col-md-2 text-center"><small class="text-muted d-block">Mean</small><strong>' + trends.mean.toFixed(4) + '</strong></div><div class="col-md-2 text-center"><small class="text-muted d-block">Median</small><strong>' + median.toFixed(4) + '</strong></div><div class="col-md-2 text-center"><small class="text-muted d-block">Std Dev</small><strong>' + trends.stdDev.toFixed(4) + '</strong></div><div class="col-md-2 text-center"><small class="text-muted d-block">Min / Max</small><strong>' + sorted[0].toFixed(4) + ' / ' + sorted[n-1].toFixed(4) + '</strong></div><div class="col-md-2 text-center"><small class="text-muted d-block">CV</small><strong>' + cv + '%</strong></div><div class="col-md-2 text-center"><small class="text-muted d-block">Trend</small>' + ti + ' <strong>' + trends.trend + '</strong></div>';
    panel.style.display = '';
}

// ===== AGGREGATION (client-side) =====
function aggregateClientData(values, labels, granularity, method) {
    if (granularity === 'monthly' || !granularity) return { values: values, labels: labels };
    if (granularity === 'rolling_3' || granularity === 'rolling_12') {
        var ws = granularity === 'rolling_3' ? 3 : 12, rv = [], rl = [];
        for (var i = ws - 1; i < values.length; i++) { var s = 0; for (var j = i - ws + 1; j <= i; j++) s += values[j]; rv.push(s / ws); rl.push(labels[i]); }
        return { values: rv, labels: rl };
    }
    var bs = granularity === 'quarterly' ? 3 : (granularity === 'bi_annual' ? 6 : 12), av = [], al = [];
    for (var i = 0; i < values.length; i += bs) {
        var ch = values.slice(i, i + bs), r;
        if (method === 'avg') r = ch.reduce(function(a,b){return a+b;},0) / ch.length;
        else if (method === 'max') r = Math.max.apply(null, ch);
        else if (method === 'min') r = Math.min.apply(null, ch);
        else if (method === 'median') { var so = ch.slice().sort(function(a,b){return a-b;}); r = so.length%2===0 ? (so[so.length/2-1]+so[so.length/2])/2 : so[Math.floor(so.length/2)]; }
        else r = ch.reduce(function(a,b){return a+b;},0);
        av.push(r); al.push(labels[i] + (bs > 1 && labels[i+bs-1] ? ' - ' + labels[i+bs-1] : ''));
    }
    return { values: av, labels: al };
}

// ===== MAIN CHART RENDER =====
function renderChart(result, selectedYears, chartType, timePeriod, convFactor, unitLabel, showReading, filters) {
    filters = filters || {};
    var contentArea = document.getElementById('contentArea');
    var html = '<div class="d-flex justify-content-between align-items-start mb-4"><div><h4 class="fw-bold mb-1" style="color:#2E5AA5;">EIP Analytic Graph</h4><p class="text-muted mb-0" style="font-size:0.9rem;">Statistics</p></div><button class="btn btn-primary btn-sm d-flex align-items-center gap-2" onclick="openGraphFilterModal()" style="border-radius:8px;padding:8px 18px;font-weight:600;"><i class="bi bi-funnel-fill"></i> Filter</button></div>';
    html += '<div style="position:relative;height:500px;"><canvas id="eipChart"></canvas></div>';
    contentArea.innerHTML = html;

    var data = result.data, filteredData = data.filter(function(d) { return selectedYears.indexOf(d.year) !== -1; });
    if (filteredData.length === 0) {
        contentArea.innerHTML = '<div class="d-flex justify-content-end mb-3"><button class="btn btn-primary btn-sm d-flex align-items-center gap-2" onclick="openGraphFilterModal()" style="border-radius:8px;padding:8px 18px;font-weight:600;"><i class="bi bi-funnel-fill"></i> Filter</button></div><div class="text-center py-5"><p class="text-muted">No data for selected years. Use the Filter button to adjust year selection.</p></div>';
        var sp = document.getElementById('statisticsPanel'); if (sp) sp.style.display = 'none';
        return;
    }

    var actualChartType = chartType;
    if (chartType === 'stacked_bar') actualChartType = 'bar';
    else if (chartType === 'area') actualChartType = 'line';
    else if (chartType === 'combo') actualChartType = 'bar';
    if (chartType === 'pie') { renderPieChart(filteredData, convFactor, unitLabel, showReading); return; }

    var labels = [], eipEV = [], eipRV = [];
    if (timePeriod === 'monthly') {
        filteredData.forEach(function(yd) { yd.months.forEach(function(m) { labels.push(monthNames[m.month-1]+'-'+String(yd.year).slice(2)); eipEV.push(parseFloat((m.eip_energy*convFactor).toFixed(4))); eipRV.push(parseFloat((m.eip_resource*convFactor).toFixed(4))); }); });
    } else {
        labels = filteredData.map(function(d) { return String(d.year); });
        filteredData.forEach(function(yd) { eipEV.push(parseFloat((yd.yearly_eip.eip_energy*convFactor).toFixed(4))); eipRV.push(parseFloat((yd.yearly_eip.eip_resource*convFactor).toFixed(4))); });
    }

    if (filters.granularity && filters.granularity !== 'monthly' && timePeriod === 'monthly') {
        var aE = aggregateClientData(eipEV, labels, filters.granularity, filters.aggregationMethod);
        var aR = aggregateClientData(eipRV, labels, filters.granularity, filters.aggregationMethod);
        eipEV = aE.values; eipRV = aR.values; labels = aE.labels;
    }

    var thr = applyClientThresholds(eipEV, labels, filters);
    var idxMap = []; thr.labels.forEach(function(l) { idxMap.push(labels.indexOf(l)); });
    eipEV = thr.values; labels = thr.labels; eipRV = idxMap.map(function(i) { return eipRV[i]; });

    var trends = detectClientTrends(eipEV);
    var isFill = chartType === 'area';
    var datasets = [
        { label: 'Energy EIP', data: eipEV, type: chartType === 'combo' ? 'bar' : undefined, backgroundColor: chartColors[0] + (actualChartType === 'bar' ? 'E6' : '40'), borderColor: chartColors[0], borderWidth: 2, fill: isFill, tension: 0.3 },
        { label: 'Resource EIP', data: eipRV, type: chartType === 'combo' ? 'line' : undefined, backgroundColor: chartColors[3] + (actualChartType === 'bar' ? 'E6' : '40'), borderColor: chartColors[3], borderWidth: 2, fill: isFill, tension: 0.3 }
    ];

    // Overlays
    if (filters.overlays && filters.overlays.length > 0) {
        if (filters.overlays.indexOf('average') !== -1) { var avg = eipEV.reduce(function(a,b){return a+b;},0)/eipEV.length; datasets.push({label:'Average',data:Array(labels.length).fill(avg),type:'line',borderColor:'#28A745',borderDash:[5,5],borderWidth:2,pointRadius:0,fill:false}); }
        if (filters.overlays.indexOf('target') !== -1) { var tv = filters.manualTarget || 0; if (!tv && _eipTargets.length > 0) tv = parseFloat(_eipTargets[0].target_value||0)*convFactor; if (tv > 0) datasets.push({label:'Target',data:Array(labels.length).fill(tv),type:'line',borderColor:'#DC3545',borderDash:[10,5],borderWidth:2,pointRadius:0,fill:false}); }
        if (filters.overlays.indexOf('best_month') !== -1) { var nz = eipEV.filter(function(v){return v>0;}); if (nz.length>0) datasets.push({label:'Best Month',data:Array(labels.length).fill(Math.min.apply(null,nz)),type:'line',borderColor:'#17A2B8',borderDash:[2,6],borderWidth:1.5,pointRadius:0,fill:false}); }
        if (filters.overlays.indexOf('seu_threshold') !== -1) { var sv = 0; _eipTargets.forEach(function(t){if(t.seu_threshold) sv=parseFloat(t.seu_threshold)*convFactor;}); if (sv>0) datasets.push({label:'SEU Threshold',data:Array(labels.length).fill(sv),type:'line',borderColor:'#FFC107',borderDash:[8,4],borderWidth:2,pointRadius:0,fill:false}); }
    }

    // Anomaly coloring
    if (trends.anomalies.length > 0 && filters.trendFilters && filters.trendFilters.indexOf('anomalies') !== -1) {
        datasets[0].backgroundColor = eipEV.map(function(v, i) { return trends.anomalies.indexOf(i) !== -1 ? '#DC354599' : chartColors[0] + 'E6'; });
    }

    var ctx = document.getElementById('eipChart').getContext('2d');
    if (eipChart) eipChart.destroy();
    var plugins = []; if (showReading) plugins.push(ChartDataLabels);
    var opts = {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: filters.showLegend !== false, position: 'top' }, title: { display: true, text: 'EIP Analysis (' + unitLabel + ')' + (filters.granularity && filters.granularity !== 'monthly' ? ' - ' + filters.granularity.replace(/_/g,' ') : ''), font: { size: 16, weight: 'bold' } }, datalabels: showReading ? { anchor: 'end', align: 'top', font: { size: 10 }, formatter: function(v) { return v > 0 ? v.toFixed(4) : ''; } } : false },
        scales: { y: { beginAtZero: true, title: { display: true, text: unitLabel }, grid: { display: filters.showGrid !== false }, stacked: chartType === 'stacked_bar' }, x: { title: { display: true, text: timePeriod === 'monthly' ? 'Period' : 'Year' }, grid: { display: false }, stacked: chartType === 'stacked_bar' } }
    };
    eipChart = new Chart(ctx, { type: actualChartType, data: { labels: labels, datasets: datasets }, plugins: plugins, options: opts });

    if (filters.showStatistics) renderStatisticsPanel(eipEV, trends); else document.getElementById('statisticsPanel').style.display = 'none';
    updateInsightPanel(eipEV, trends, labels.length);
}

function renderPieChart(filteredData, convFactor, unitLabel, showReading) {
    var contentArea = document.getElementById('contentArea'), numYears = filteredData.length;
    var colClass = numYears <= 2 ? 'col-md-6' : (numYears <= 3 ? 'col-md-4' : 'col-md-3');
    var html = '<div class="d-flex justify-content-between align-items-start mb-4"><div><h4 class="fw-bold mb-1" style="color:#2E5AA5;">EIP Analytic Graph</h4><p class="text-muted mb-0" style="font-size:0.9rem;">Statistics</p></div><button class="btn btn-primary btn-sm d-flex align-items-center gap-2" onclick="openGraphFilterModal()" style="border-radius:8px;padding:8px 18px;font-weight:600;"><i class="bi bi-funnel-fill"></i> Filter</button></div><div class="row">';
    filteredData.forEach(function(yd, idx) { html += '<div class="' + colClass + ' mb-4"><div style="position:relative;height:350px;"><canvas id="pieChart_' + idx + '"></canvas></div></div>'; });
    html += '</div>'; contentArea.innerHTML = html;
    filteredData.forEach(function(yd, idx) {
        var vals = [parseFloat((yd.yearly_eip.eip_energy*convFactor).toFixed(4)), parseFloat((yd.yearly_eip.eip_resource*convFactor).toFixed(4))];
        var ctx = document.getElementById('pieChart_'+idx).getContext('2d'), pl = []; if (showReading) pl.push(ChartDataLabels);
        new Chart(ctx, { type:'pie', data:{labels:['Energy EIP','Resource EIP'],datasets:[{data:vals,backgroundColor:[chartColors[0],chartColors[3]],borderWidth:2,borderColor:'#fff'}]}, plugins:pl, options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{position:'bottom',labels:{font:{size:11}}},title:{display:true,text:'EIP '+yd.year+' ('+unitLabel+')',font:{size:14,weight:'bold'}},datalabels:showReading?{color:'#fff',font:{size:11,weight:'bold'},formatter:function(v,c){var t=c.dataset.data.reduce(function(a,b){return a+b;},0);return t>0?((v/t)*100).toFixed(1)+'%':'0%';}}:false}} });
    });
}

// ===== INSIGHT PANEL =====
function updateInsightPanel(values, trends, total) {
    if (!values || values.length === 0) return;
    var avg = values.reduce(function(a,b){return a+b;},0)/values.length;
    var arrow = trends.trend === 'increasing' ? '<span class="text-danger">&#9650;</span>' : trends.trend === 'decreasing' ? '<span class="text-success">&#9660;</span>' : '<span class="text-warning">&#9644;</span>';
    document.getElementById('insightPanel').innerHTML = '<i class="bi bi-bar-chart me-1"></i><strong>' + total + '</strong> periods | Avg EIP: <strong>' + avg.toFixed(4) + '</strong> | Trend: ' + arrow + ' <strong>' + trends.trend + '</strong> | Anomalies: <strong>' + trends.anomalies.length + '</strong>';
}

// ===== EXPORT =====
window.exportChartPNG = function() { var c = document.getElementById('eipChart'); if (!c) return; var a = document.createElement('a'); a.download = 'eip-analysis-chart.png'; a.href = c.toDataURL('image/png'); a.click(); };
window.exportDataCSV = function() { var y1 = document.getElementById('yearStart').value, y2 = document.getElementById('yearEnd').value, v = document.getElementById('variableType').value; if (!y1||!y2||!v) { alert('Please select year range and variable type first.'); return; } window.location.href = '/eip-analysis/export?year_start='+y1+'&year_end='+y2+'&variable_type='+v; };

// ===== PRESETS =====
window.saveFilterPreset = function() {
    var name = document.getElementById('presetName').value;
    if (!name) { alert('Please enter a preset name.'); return; }
    fetch('/eip-analysis/presets', { method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrfToken}, body:JSON.stringify({name:name,description:document.getElementById('presetDescription').value,filters:getFilterValues()}) })
    .then(function(r){return r.json();}).then(function(res) {
        if (res.success) { alert('Preset saved!'); document.getElementById('presetName').value = ''; document.getElementById('presetDescription').value = '';
            var el = document.getElementById('userPresetsList');
            el.innerHTML = '<div class="d-flex align-items-center justify-content-between p-2 mb-2 border rounded"><div style="cursor:pointer;" onclick="loadPreset('+res.preset.id+')"><i class="bi bi-star me-2"></i><strong>'+name+'</strong></div><button class="btn btn-outline-danger btn-sm" onclick="deletePreset('+res.preset.id+')"><i class="bi bi-trash"></i></button></div>' + el.innerHTML;
        }
    });
};
window.loadPreset = function(id) {
    fetch('/eip-analysis/presets/'+id).then(function(r){return r.json();}).then(function(res) { if (res.success && res.preset && res.preset.filters) applyPresetToUI(res.preset.filters); });
};
window.applyPresetToUI = function(f) {
    if (f.energySources) document.querySelectorAll('.gf-energy-source').forEach(function(cb) { cb.checked = f.energySources.indexOf(cb.value) !== -1; });
    if (f.chartType) { var el = document.querySelector('input[name="gf_chart_type"][value="'+f.chartType+'"]'); if (el) el.checked = true; }
    if (f.timePeriod) { var el = document.querySelector('input[name="gf_time_period"][value="'+f.timePeriod+'"]'); if (el) el.checked = true; }
    if (f.colorScheme) { var el = document.querySelector('input[name="eip_color_scheme"][value="'+f.colorScheme+'"]'); if (el) el.checked = true; }
    var sr = document.getElementById('gf_show_reading'); if (sr && f.showReading !== undefined) sr.checked = f.showReading;
    var sl = document.getElementById('gf_show_legend'); if (sl && f.showLegend !== undefined) sl.checked = f.showLegend;
    var sg = document.getElementById('gf_show_grid'); if (sg && f.showGrid !== undefined) sg.checked = f.showGrid;
    updateEnergySourceCount();
};
window.deletePreset = function(id) { if (!confirm('Delete this preset?')) return; fetch('/eip-analysis/presets/'+id,{method:'DELETE',headers:{'X-CSRF-TOKEN':csrfToken}}).then(function(r){return r.json();}).then(function(res){if(res.success)location.reload();}); };
window.togglePresetFavorite = function(id) { fetch('/eip-analysis/presets/'+id+'/favorite',{method:'POST',headers:{'X-CSRF-TOKEN':csrfToken}}).then(function(r){return r.json();}).then(function(res){if(res.success)location.reload();}); };

// ===== VARIABLE GRAPH (X/Y Regression) =====
var varGraphChart = null;
var varGraphData = null;

window.filterVarItems = function(term, selector) {
    term = term.toLowerCase();
    document.querySelectorAll(selector).forEach(function(el) {
        el.style.display = el.textContent.toLowerCase().includes(term) ? '' : 'none';
    });
};

window.loadRegressionGraph = function() {
    var y1 = document.getElementById('yearStart').value;
    var y2 = document.getElementById('yearEnd').value;
    if (!y1 || !y2) { alert('Please select a year range first.'); return; }

    // Gather X variable IDs
    var xVarIds = [];
    document.querySelectorAll('.vg-xvar:checked').forEach(function(cb) { xVarIds.push(parseInt(cb.value)); });
    if (xVarIds.length === 0) { alert('Please select at least one X-axis variable.'); return; }

    // Gather Y energy/resource IDs
    var yEnergyIds = [];
    document.querySelectorAll('.vg-y-energy:checked').forEach(function(cb) { yEnergyIds.push(parseInt(cb.value)); });
    var yResourceIds = [];
    document.querySelectorAll('.vg-y-resource:checked').forEach(function(cb) { yResourceIds.push(parseInt(cb.value)); });
    if (yEnergyIds.length === 0 && yResourceIds.length === 0) { alert('Please select at least one Y-axis energy source.'); return; }

    // Close modal
    var modal = bootstrap.Modal.getInstance(document.getElementById('graphFilterModal'));
    if (modal) modal.hide();

    // Show loading
    var card = document.getElementById('varGraphCard');
    card.style.display = '';
    document.getElementById('varGraphInfo').style.display = 'none';
    document.getElementById('varGraphSubtitle').textContent = 'Loading...';

    fetch('/eip-analysis/data/regression', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        body: JSON.stringify({
            year_start: parseInt(y1),
            year_end: parseInt(y2),
            x_variable_ids: xVarIds,
            y_energy_ids: yEnergyIds,
            y_resource_ids: yResourceIds
        })
    })
    .then(function(r) {
        if (!r.ok) throw new Error('HTTP ' + r.status);
        return r.json();
    })
    .then(function(res) {
        if (res.success) {
            varGraphData = res;
            renderVariableGraph(res, xVarIds);
        } else {
            document.getElementById('varGraphSubtitle').textContent = 'Error: ' + (res.message || 'Failed to load');
        }
    })
    .catch(function(err) {
        document.getElementById('varGraphSubtitle').textContent = 'Error: ' + err.message;
    });
};

function renderVariableGraph(res, xVarIds) {
    var points = res.points;
    var xNames = res.x_variable_names;
    var regression = res.regression;
    var showTrendline = document.getElementById('vg_show_trendline').checked;
    var showR2 = document.getElementById('vg_show_r2').checked;
    var showEquation = document.getElementById('vg_show_equation').checked;
    var showLabels = document.getElementById('vg_show_labels').checked;
    var showGrid = document.getElementById('vg_show_grid').checked;

    // For single X variable: scatter plot X vs Y
    // For multiple X variables: show each as separate scatter series sharing Y
    var colors = getActiveColors();

    if (xVarIds.length === 1) {
        renderSingleVarScatter(points, xVarIds[0], xNames, regression, colors, showTrendline, showR2, showEquation, showLabels, showGrid);
    } else {
        renderMultiVarScatter(points, xVarIds, xNames, colors, showLabels, showGrid);
    }
}

function renderSingleVarScatter(points, xId, xNames, regression, colors, showTrendline, showR2, showEquation, showLabels, showGrid) {
    var xLabel = xNames[xId] || 'Variable';
    var scatterData = [];
    var nonZeroPoints = [];

    points.forEach(function(p) {
        var xVal = p.x_values[xId] || 0;
        var yVal = p.y_value || 0;
        if (xVal > 0 || yVal > 0) {
            scatterData.push({ x: xVal, y: yVal });
            nonZeroPoints.push(p);
        }
    });

    document.getElementById('varGraphSubtitle').textContent = xLabel + ' vs Energy Consumption (GJ)';

    // Build info panel
    var infoHtml = '';
    if (regression) {
        var parts = [];
        if (showEquation) parts.push('<strong>Equation:</strong> ' + regression.equation);
        if (showR2) parts.push('<strong>R²:</strong> ' + regression.r_squared.toFixed(4));
        parts.push('<strong>Points:</strong> ' + scatterData.length);
        infoHtml = '<i class="bi bi-info-circle me-1"></i>' + parts.join(' | ');
    } else {
        infoHtml = '<i class="bi bi-info-circle me-1"></i><strong>Points:</strong> ' + scatterData.length + ' (multiple X variables: no single regression)';
    }
    var infoEl = document.getElementById('varGraphInfo');
    infoEl.innerHTML = infoHtml;
    infoEl.style.display = '';

    var datasets = [{
        label: xLabel + ' vs Energy (GJ)',
        data: scatterData,
        backgroundColor: colors[0] + 'CC',
        borderColor: colors[0],
        borderWidth: 1,
        pointRadius: 5,
        pointHoverRadius: 7
    }];

    // Add trendline dataset
    if (showTrendline && regression && scatterData.length > 1) {
        var xVals = scatterData.map(function(d) { return d.x; });
        var xMin = Math.min.apply(null, xVals);
        var xMax = Math.max.apply(null, xVals);
        var margin = (xMax - xMin) * 0.05;
        datasets.push({
            label: 'Trendline' + (showEquation ? ' (' + regression.equation + ')' : '') + (showR2 ? ' R²=' + regression.r_squared.toFixed(4) : ''),
            data: [
                { x: xMin - margin, y: regression.slope * (xMin - margin) + regression.intercept },
                { x: xMax + margin, y: regression.slope * (xMax + margin) + regression.intercept }
            ],
            type: 'line',
            borderColor: '#DC2626',
            borderWidth: 2,
            borderDash: [6, 3],
            pointRadius: 0,
            fill: false
        });
    }

    var ctx = document.getElementById('varGraphCanvas').getContext('2d');
    if (varGraphChart) varGraphChart.destroy();

    var plugins = [];
    if (showLabels) plugins.push(ChartDataLabels);

    varGraphChart = new Chart(ctx, {
        type: 'scatter',
        data: { datasets: datasets },
        plugins: plugins,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: true, position: 'top' },
                title: {
                    display: true,
                    text: xLabel + ' vs Energy Consumption',
                    font: { size: 16, weight: 'bold' }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            var d = context.raw;
                            return xLabel + ': ' + d.x.toLocaleString() + ' | Energy: ' + d.y.toLocaleString() + ' GJ';
                        }
                    }
                },
                datalabels: showLabels ? {
                    anchor: 'end', align: 'top', font: { size: 9 },
                    formatter: function(v) { return v.y ? v.y.toFixed(2) : ''; }
                } : false
            },
            scales: {
                x: {
                    title: { display: true, text: xLabel },
                    grid: { display: showGrid },
                    beginAtZero: true
                },
                y: {
                    title: { display: true, text: 'Energy Consumption (GJ)' },
                    grid: { display: showGrid },
                    beginAtZero: true
                }
            }
        }
    });
}

function renderMultiVarScatter(points, xVarIds, xNames, colors, showLabels, showGrid) {
    document.getElementById('varGraphSubtitle').textContent = 'Multiple Variables vs Energy Consumption (GJ)';

    var infoEl = document.getElementById('varGraphInfo');
    infoEl.innerHTML = '<i class="bi bi-info-circle me-1"></i><strong>Variables:</strong> ' + xVarIds.map(function(id) { return xNames[id]; }).join(', ') + ' | <strong>Points per variable:</strong> ' + points.length + ' | <em>Select a single X variable for regression analysis</em>';
    infoEl.style.display = '';

    var datasets = [];
    xVarIds.forEach(function(xId, idx) {
        var scatterData = [];
        points.forEach(function(p) {
            var xVal = p.x_values[xId] || 0;
            var yVal = p.y_value || 0;
            if (xVal > 0 || yVal > 0) {
                scatterData.push({ x: xVal, y: yVal });
            }
        });
        datasets.push({
            label: xNames[xId] || ('Variable ' + xId),
            data: scatterData,
            backgroundColor: (colors[idx % colors.length]) + 'CC',
            borderColor: colors[idx % colors.length],
            borderWidth: 1,
            pointRadius: 5,
            pointHoverRadius: 7
        });
    });

    var ctx = document.getElementById('varGraphCanvas').getContext('2d');
    if (varGraphChart) varGraphChart.destroy();

    var plugins = [];
    if (showLabels) plugins.push(ChartDataLabels);

    varGraphChart = new Chart(ctx, {
        type: 'scatter',
        data: { datasets: datasets },
        plugins: plugins,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: true, position: 'top' },
                title: {
                    display: true,
                    text: 'Variable Comparison vs Energy Consumption',
                    font: { size: 16, weight: 'bold' }
                },
                datalabels: showLabels ? {
                    anchor: 'end', align: 'top', font: { size: 9 },
                    formatter: function(v) { return v.y ? v.y.toFixed(2) : ''; }
                } : false
            },
            scales: {
                x: {
                    title: { display: true, text: 'Variable Value' },
                    grid: { display: showGrid },
                    beginAtZero: true
                },
                y: {
                    title: { display: true, text: 'Energy Consumption (GJ)' },
                    grid: { display: showGrid },
                    beginAtZero: true
                }
            }
        }
    });
}

window.exportVarGraphPNG = function() {
    var c = document.getElementById('varGraphCanvas');
    if (!c) return;
    var a = document.createElement('a');
    a.download = 'eip-variable-graph.png';
    a.href = c.toDataURL('image/png');
    a.click();
};

window.exportVarGraphCSV = function() {
    if (!varGraphData || !varGraphData.points) { alert('Load variable graph data first.'); return; }
    var points = varGraphData.points;
    var xNames = varGraphData.x_variable_names;
    var xIds = Object.keys(xNames);

    var rows = ['Month,' + xIds.map(function(id) { return xNames[id]; }).join(',') + ',Energy (GJ)'];
    points.forEach(function(p) {
        var cols = [p.month];
        xIds.forEach(function(id) { cols.push(p.x_values[id] || 0); });
        cols.push(p.y_value);
        rows.push(cols.join(','));
    });

    if (varGraphData.regression) {
        rows.push('');
        rows.push('Regression');
        rows.push('Equation,' + varGraphData.regression.equation);
        rows.push('R²,' + varGraphData.regression.r_squared);
        rows.push('Slope,' + varGraphData.regression.slope);
        rows.push('Intercept,' + varGraphData.regression.intercept);
    }

    var blob = new Blob([rows.join('\n')], { type: 'text/csv' });
    var a = document.createElement('a');
    a.download = 'eip-variable-graph.csv';
    a.href = URL.createObjectURL(blob);
    a.click();
    URL.revokeObjectURL(a.href);
};
</script>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    updateStatus();
    updateEnergySourceCount();
});
</script>
@endsection
