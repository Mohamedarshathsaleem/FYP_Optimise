@extends('layouts.dashboard')

@section('title', 'Load Apportioning')

@section('content')

<!-- Header Section -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <p class="text-secondary small mb-1">Pages / Energy Review / Load Apportioning</p>
        <h3 class="fw-bold">Load Apportioning</h3>
    </div>
    <div class="d-flex align-items-center">
        <div class="input-group search-box me-3">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="text" class="form-control" placeholder="Search">
        </div>
        <img src="{{ asset('images/user.png') }}" class="rounded-circle" alt="User" style="width:40px;height:40px;">
    </div>
</div>

<!-- Instructions Card -->
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
                        Select a <strong>Year</strong>, choose the <strong>Approach</strong> (e.g. Equipment Types, Building/Blocks),
                        pick one or more <strong>Energy Types</strong>, and select the <strong>Unit</strong> mode.
                        Then add row entries for each energy type table, click <strong>Calculate</strong> to compute values,
                        and <strong>Save</strong> to persist the data.
                    </p>
                </div>
            </div>
            <button class="btn-close" onclick="document.getElementById('instructionsCard').style.display='none'"></button>
        </div>
    </div>
</div>

<!-- ========== FILTER / CATEGORY SECTION ========== -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <div class="row g-3 align-items-end">

            <!-- Year -->
            <div class="col-md-2">
                <label class="form-label fw-bold">Year <span class="text-danger">*</span></label>
                <select class="form-select" id="yearSelect" onchange="checkFiltersComplete(); autoLoadData()">
                    <option value="">Choose</option>
                    @foreach($years->sortDesc() as $year)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Approach -->
            <div class="col-md-2">
                <label class="form-label fw-bold">Approach <span class="text-danger">*</span></label>
                <select class="form-select" id="approachSelect" onchange="handleApproachChange(); autoLoadData()">
                    <option value="">Choose</option>
                    @foreach($approaches as $approach)
                        <option value="{{ $approach->id }}">{{ $approach->name }}</option>
                    @endforeach
                    <option value="__custom__">+ Add new approach...</option>
                </select>
                <div id="customApproachWrapper" style="display:none;" class="mt-2">
                    <div class="input-group">
                        <input type="text" class="form-control form-control-sm" id="customApproachInput" placeholder="Type new approach name">
                        <button class="btn btn-primary btn-sm" onclick="saveCustomApproach()"><i class="bi bi-check-lg"></i></button>
                        <button class="btn btn-outline-secondary btn-sm" onclick="cancelCustomApproach()"><i class="bi bi-x-lg"></i></button>
                    </div>
                </div>
            </div>

            <!-- Type of Energy -->
            <div class="col-md-2">
                <label class="form-label fw-bold">Type of Energy <span class="text-danger">*</span></label>
                <div class="position-relative">
                    <input type="text" class="form-select" id="energyTypeInput" onclick="openEnergyTypesModal()" readonly placeholder="Select approach first" style="cursor:pointer;" disabled>
                    <span id="energyBadge" class="position-absolute top-50 end-0 translate-middle-y me-5" style="display:none;">
                        <span class="badge bg-primary rounded-circle" id="energyCount">0</span>
                    </span>
                </div>
            </div>

            <!-- Unit -->
            <div class="col-md-2">
                <label class="form-label fw-bold">Unit <span class="text-danger">*</span></label>
                <select class="form-select" id="unitSelect" onchange="checkFiltersComplete(); autoLoadData()">
                    <option value="">Choose</option>
                    <option value="energy_gj">Energy (GJ)</option>
                    <option value="load_percentage">Load Percentage</option>
                </select>
            </div>

            <!-- View -->
            <div class="col-md-2">
                <label class="form-label fw-bold">View <span class="text-danger">*</span></label>
                <select class="form-select" id="viewSelect" onchange="checkFiltersComplete(); handleViewChange()">
                    <option value="">Choose</option>
                    <option value="Table">Table</option>
                    <option value="Graph">Graph</option>
                </select>
            </div>

        </div>

        <!-- ========== CONTENT AREA ========== -->
        <div class="mt-4" id="contentArea">
            <div class="text-center py-5">
                <p class="text-muted" id="statusMessage">Please complete all filters above to view data. Data will load automatically.</p>
            </div>
        </div>

        <!-- ========== ACTION BUTTONS ========== -->
        <div id="actionButtons" style="display:none;" class="mt-3 d-flex justify-content-between align-items-center">
            <button class="btn btn-primary" id="graphFilterBtn" onclick="openGraphFilterModal()" style="display:none; border-radius: 25px;">
                <i class="bi bi-funnel-fill me-2"></i>Filter
            </button>
            <button class="btn btn-primary px-4" id="saveBtn" onclick="manualSaveAll()" style="display:none; border-radius: 25px;">
                <i class="bi bi-check-lg me-2"></i>Save
            </button>
        </div>
    </div>
</div>

<!-- ========== ENERGY TYPES MODAL ========== -->
<div class="modal fade" id="energyTypesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius:15px;">
            <div class="modal-header border-0 pb-2">
                <h5 class="modal-title fw-bold text-dark">Choose Energy Types</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="px-4 py-2 border-bottom d-flex justify-content-between align-items-center">
                    <span class="text-muted small" id="energyTypeSelectionCount">0 selected</span>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="toggleAllEnergyTypes()">
                        <i class="bi bi-check2-all me-1"></i><span id="toggleAllLabel">Select All</span>
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead style="background: linear-gradient(135deg, #4472C4 0%, #2E5AA5 100%); color: white;">
                            <tr>
                                <th class="py-3 ps-4">Energy Type</th>
                                <th class="py-3">Source</th>
                                <th class="py-3">Details</th>
                                <th class="py-3 text-center">No. of Equipment</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($energySources))
                            @foreach($energySources as $es)
                            <tr>
                                <td class="py-2 ps-4">
                                    <div class="form-check">
                                        <input class="form-check-input energy-check" type="checkbox" id="edata_{{ $es->id }}" value="edata_{{ $es->id }}" data-name="{{ $es->energy_type }}">
                                        <label class="form-check-label" for="edata_{{ $es->id }}">{{ $es->energy_type }}</label>
                                    </div>
                                </td>
                                <td class="py-2"><span class="badge bg-info">Energy Data</span></td>
                                <td class="py-2">{{ $es->provider ?? '-' }}</td>
                                <td class="py-2 text-center">
                                    <span id="equipCount_edata_{{ $es->id }}" class="text-muted">0</span>
                                </td>
                            </tr>
                            @endforeach
                            @endif
                            @if(isset($resourceSources))
                            @foreach($resourceSources as $rs)
                            <tr>
                                <td class="py-2 ps-4">
                                    <div class="form-check">
                                        <input class="form-check-input energy-check" type="checkbox" id="rdata_{{ $rs->id }}" value="rdata_{{ $rs->id }}" data-name="{{ $rs->resource_type }}">
                                        <label class="form-check-label" for="rdata_{{ $rs->id }}">{{ $rs->resource_type }}</label>
                                    </div>
                                </td>
                                <td class="py-2"><span class="badge bg-success">Energy Resource</span></td>
                                <td class="py-2">{{ $rs->provider ?? '-' }}</td>
                                <td class="py-2 text-center">
                                    <span id="equipCount_rdata_{{ $rs->id }}" class="text-muted">0</span>
                                </td>
                            </tr>
                            @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
                <div class="p-4 text-center">
                    <button type="button" class="btn btn-primary px-5 py-2" onclick="saveEnergyTypes()" style="border-radius:10px;">Save</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========== GRAPH FILTER MODAL ========== -->
<div class="modal fade" id="graphFilterModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 800px;">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-body p-4">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="fw-bold mb-0" style="color: #4472C4;"><i class="bi bi-funnel me-2"></i>Graph Filter</h4>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-secondary btn-sm" onclick="resetGraphFilters()" title="Reset"><i class="bi bi-arrow-counterclockwise"></i></button>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                </div>

                <!-- Tab Navigation -->
                <ul class="nav nav-tabs nav-fill mb-3" role="tablist" style="font-size:0.8rem;">
                    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#la_tab_chart">Chart Type</a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#la_tab_display">Display Options</a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#la_tab_colors">Colors</a></li>
                </ul>

                <div class="tab-content" style="font-size: 0.85rem; min-height: 250px;">

                    <!-- TAB 1: Chart Type -->
                    <div class="tab-pane fade show active" id="la_tab_chart">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="fw-bold text-muted mb-3">Chart Type</h6>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="la_chart_type" value="bar" id="la_ct_bar" checked>
                                    <label class="form-check-label" for="la_ct_bar"><i class="bi bi-bar-chart me-1"></i>Bar Chart</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="la_chart_type" value="horizontalBar" id="la_ct_hbar">
                                    <label class="form-check-label" for="la_ct_hbar"><i class="bi bi-bar-chart me-1" style="transform:rotate(90deg);display:inline-block;"></i>Horizontal Bar</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="la_chart_type" value="pie" id="la_ct_pie">
                                    <label class="form-check-label" for="la_ct_pie"><i class="bi bi-pie-chart me-1"></i>Pie Chart</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="la_chart_type" value="doughnut" id="la_ct_doughnut">
                                    <label class="form-check-label" for="la_ct_doughnut"><i class="bi bi-circle me-1"></i>Doughnut Chart</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="la_chart_type" value="polarArea" id="la_ct_polar">
                                    <label class="form-check-label" for="la_ct_polar"><i class="bi bi-bullseye me-1"></i>Polar Area</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="la_chart_type" value="pareto" id="la_ct_pareto">
                                    <label class="form-check-label" for="la_ct_pareto"><i class="bi bi-graph-up me-1"></i>Pareto Chart</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-bold text-muted mb-3">Layout</h6>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="la_layout" value="separate" id="la_ly_separate" checked>
                                    <label class="form-check-label" for="la_ly_separate">Separate chart per energy type</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="la_layout" value="combined" id="la_ly_combined">
                                    <label class="form-check-label" for="la_ly_combined">Combined (all energy types)</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 2: Display Options -->
                    <div class="tab-pane fade" id="la_tab_display">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="fw-bold text-muted mb-3">Labels & Legend</h6>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="la_show_labels" checked>
                                    <label class="form-check-label" for="la_show_labels">Show data labels</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="la_show_legend" checked>
                                    <label class="form-check-label" for="la_show_legend">Show legend</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="la_show_grid" checked>
                                    <label class="form-check-label" for="la_show_grid">Show grid lines</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-bold text-muted mb-3">Value Display</h6>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="la_label_format" value="value" id="la_lf_value" checked>
                                    <label class="form-check-label" for="la_lf_value">Show values</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="la_label_format" value="percentage" id="la_lf_pct">
                                    <label class="form-check-label" for="la_lf_pct">Show percentages</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="la_label_format" value="both" id="la_lf_both">
                                    <label class="form-check-label" for="la_lf_both">Show both</label>
                                </div>

                                <h6 class="fw-bold text-muted mb-3 mt-3">Decimal Places</h6>
                                <select class="form-select form-select-sm" id="la_decimal_places" style="width:120px;">
                                    <option value="0">0</option>
                                    <option value="1">1</option>
                                    <option value="2" selected>2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                </select>

                                <h6 class="fw-bold text-muted mb-3 mt-3">Export</h6>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-outline-secondary btn-sm" onclick="exportChartsPNG()"><i class="bi bi-image me-1"></i>PNG</button>
                                    <button class="btn btn-outline-secondary btn-sm" onclick="exportChartsCSV()"><i class="bi bi-file-earmark-spreadsheet me-1"></i>CSV</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 3: Colors -->
                    <div class="tab-pane fade" id="la_tab_colors">
                        <h6 class="fw-bold text-muted mb-3">Color Scheme</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="la_color_scheme" value="default" id="la_cs_default" checked>
                                    <label class="form-check-label" for="la_cs_default">Default</label>
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
                                    <input class="form-check-input" type="radio" name="la_color_scheme" value="pastel" id="la_cs_pastel">
                                    <label class="form-check-label" for="la_cs_pastel">Pastel</label>
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
                                    <input class="form-check-input" type="radio" name="la_color_scheme" value="corporate" id="la_cs_corporate">
                                    <label class="form-check-label" for="la_cs_corporate">Corporate</label>
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
                                    <input class="form-check-input" type="radio" name="la_color_scheme" value="vibrant" id="la_cs_vibrant">
                                    <label class="form-check-label" for="la_cs_vibrant">Vibrant</label>
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
                                    <input class="form-check-input" type="radio" name="la_color_scheme" value="ocean" id="la_cs_ocean">
                                    <label class="form-check-label" for="la_cs_ocean">Ocean</label>
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
                                    <input class="form-check-input" type="radio" name="la_color_scheme" value="warm" id="la_cs_warm">
                                    <label class="form-check-label" for="la_cs_warm">Warm</label>
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

                <!-- Footer -->
                <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                    <button type="button" class="btn btn-outline-secondary px-4" onclick="resetGraphFilters()" style="border-radius: 25px;">Clear All</button>
                    <button type="button" class="btn btn-primary px-5 py-2" onclick="applyGraphFilter()" style="border-radius: 25px; min-width: 200px;">Apply & View</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    #graphFilterModal .nav-tabs .nav-link { font-size: 0.8rem; padding: 0.5rem 0.75rem; }
    #graphFilterModal .nav-tabs .nav-link.active { color: #4472C4; border-bottom: 2px solid #4472C4; font-weight: 600; }
</style>

{{-- ========== JAVASCRIPT ========== --}}
<script>
var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// State
var selectedEnergyTypeIds = [];
var selectedEnergyTypeNames = {};
var currentData = {};
var laChartInstances = {};
var pendingAlert = null; // {type, message} to show after data reload
var chartColors = {
    default: ['#2563EB','#DC2626','#059669','#D97706','#7C3AED','#DB2777','#0891B2','#EA580C','#4F46E5','#16A34A','#CA8A04','#9333EA'],
    pastel: ['#93C5FD','#FCA5A5','#6EE7B7','#FCD34D','#C4B5FD','#FBCFE8','#67E8F9','#FDBA74','#A5B4FC','#86EFAC','#FDE047','#D8B4FE'],
    corporate: ['#1E3A5F','#4472C4','#70AD47','#ED7D31','#FFC000','#5B9BD5','#A5A5A5','#264478','#9DC3E6','#548235','#BF8F00','#7030A0'],
    vibrant: ['#FF6B6B','#4ECDC4','#45B7D1','#96CEB4','#FFEAA7','#DDA0DD','#98D8C8','#F7DC6F','#BB8FCE','#82E0AA','#F8C471','#AED6F1'],
    ocean: ['#0077B6','#00B4D8','#48CAE4','#90E0EF','#023E8A','#0096C7','#CAF0F8','#ADE8F4','#03045E','#0077B6','#00B4D8','#48CAE4'],
    warm: ['#E63946','#F4A261','#E9C46A','#2A9D8F','#264653','#D62828','#F77F00','#FCBF49','#EAE2B7','#003049','#C1121F','#669BBC']
};

// ==================== FILTER HANDLERS ====================
function checkFiltersComplete() {
    var year = document.getElementById('yearSelect').value;
    var approach = document.getElementById('approachSelect').value;
    var energyOk = selectedEnergyTypeIds.length > 0;
    var unit = document.getElementById('unitSelect').value;
    var view = document.getElementById('viewSelect').value;
    return year && approach && approach !== '__custom__' && energyOk && unit && view;
}

function autoLoadData() {
    if (checkFiltersComplete()) {
        loadData();
    }
}

function handleViewChange() {
    if (checkFiltersComplete()) {
        if (currentData && Object.keys(currentData).length > 0) {
            renderView();
        } else {
            loadData();
        }
    }
}

function renderView() {
    var viewMode = document.getElementById('viewSelect').value;
    if (viewMode === 'Graph') {
        renderGraphs();
    } else {
        renderTables();
    }
    updateEquipmentCounts();
}

function updateEquipmentCounts() {
    document.querySelectorAll('[id^="equipCount_"]').forEach(function(el) {
        var etId = el.id.replace('equipCount_', '');
        var rows = currentData[etId] || [];
        var count = rows.length;
        if (count > 0) {
            el.className = 'badge bg-primary rounded-pill';
            el.textContent = count;
        } else {
            el.className = 'text-muted';
            el.textContent = '0';
        }
    });
}

function handleApproachChange() {
    var val = document.getElementById('approachSelect').value;
    var energyInput = document.getElementById('energyTypeInput');
    if (val === '__custom__') {
        document.getElementById('customApproachWrapper').style.display = 'block';
        energyInput.disabled = true;
        energyInput.placeholder = 'Select approach first';
    } else if (val) {
        document.getElementById('customApproachWrapper').style.display = 'none';
        energyInput.disabled = false;
        energyInput.placeholder = 'Choose';
        energyInput.style.cursor = 'pointer';
    } else {
        document.getElementById('customApproachWrapper').style.display = 'none';
        energyInput.disabled = true;
        energyInput.placeholder = 'Select approach first';
    }
    checkFiltersComplete();
}

// ==================== ENERGY TYPES MODAL ====================
function openEnergyTypesModal() {
    var approachId = document.getElementById('approachSelect').value;
    if (!approachId || approachId === '__custom__') {
        return;
    }

    document.querySelectorAll('.energy-check').forEach(function(cb) {
        cb.checked = selectedEnergyTypeIds.indexOf(cb.value) !== -1;
    });

    // Fetch equipment counts for current year + approach
    var year = document.getElementById('yearSelect').value;
    if (year && approachId) {
        fetch('/load-apportioning/equipment-counts?year=' + year + '&approach_id=' + approachId, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
        })
        .then(function(r) { return r.json(); })
        .then(function(counts) {
            document.querySelectorAll('[id^="equipCount_"]').forEach(function(el) {
                var etId = el.id.replace('equipCount_', '');
                var count = counts[etId] || 0;
                if (count > 0) {
                    el.className = 'badge bg-primary rounded-pill';
                    el.textContent = count;
                } else {
                    el.className = 'text-muted';
                    el.textContent = '0';
                }
            });
        })
        .catch(function() {});
    }
    updateEnergyTypeSelectionCount();
    new bootstrap.Modal(document.getElementById('energyTypesModal')).show();
}

function toggleAllEnergyTypes() {
    var checkboxes = document.querySelectorAll('.energy-check');
    var allChecked = true;
    checkboxes.forEach(function(cb) { if (!cb.checked) allChecked = false; });

    checkboxes.forEach(function(cb) { cb.checked = !allChecked; });
    updateEnergyTypeSelectionCount();
}

function updateEnergyTypeSelectionCount() {
    var checked = document.querySelectorAll('.energy-check:checked').length;
    var total = document.querySelectorAll('.energy-check').length;
    var countEl = document.getElementById('energyTypeSelectionCount');
    if (countEl) countEl.textContent = checked + ' of ' + total + ' selected';
    var labelEl = document.getElementById('toggleAllLabel');
    if (labelEl) labelEl.textContent = (checked === total) ? 'Deselect All' : 'Select All';
}

function saveEnergyTypes() {
    var checkboxes = document.querySelectorAll('.energy-check:checked');
    selectedEnergyTypeIds = [];
    selectedEnergyTypeNames = {};

    checkboxes.forEach(function(cb) {
        var id = cb.value; // Keep as string to preserve edata_/rdata_ prefixes
        selectedEnergyTypeIds.push(id);
        selectedEnergyTypeNames[id] = cb.dataset.name;
    });

    var input = document.getElementById('energyTypeInput');
    var badge = document.getElementById('energyBadge');
    var count = document.getElementById('energyCount');

    if (selectedEnergyTypeIds.length > 0) {
        input.value = selectedEnergyTypeIds.length + ' selected';
        count.textContent = selectedEnergyTypeIds.length;
        badge.style.display = 'block';
    } else {
        input.value = '';
        badge.style.display = 'none';
    }

    bootstrap.Modal.getInstance(document.getElementById('energyTypesModal')).hide();
    checkFiltersComplete();
    autoLoadData();
}

// ==================== CUSTOM APPROACH ====================
function saveCustomApproach() {
    var name = document.getElementById('customApproachInput').value.trim();
    if (!name) return;

    fetch('/load-apportioning/approaches', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ name: name })
    })
    .then(function(r) { return r.json(); })
    .then(function(result) {
        if (result.success) {
            var select = document.getElementById('approachSelect');
            var option = document.createElement('option');
            option.value = result.approach.id;
            option.textContent = result.approach.name;
            var customOpt = select.querySelector('option[value="__custom__"]');
            select.insertBefore(option, customOpt);
            select.value = result.approach.id;
            cancelCustomApproach();
            checkFiltersComplete();
        } else {
            alert(result.message || 'Failed to create approach.');
        }
    })
    .catch(function(err) { alert('Network error.'); console.error(err); });
}

function cancelCustomApproach() {
    document.getElementById('customApproachWrapper').style.display = 'none';
    document.getElementById('customApproachInput').value = '';
    var select = document.getElementById('approachSelect');
    if (select.value === '__custom__') select.value = '';
    checkFiltersComplete();
}

// ==================== LOAD DATA ====================
function loadData() {
    var year = document.getElementById('yearSelect').value;
    var approachId = document.getElementById('approachSelect').value;
    var unitMode = document.getElementById('unitSelect').value;

    var params = new URLSearchParams();
    params.append('year', year);
    params.append('approach_id', approachId);
    params.append('unit_mode', unitMode);
    selectedEnergyTypeIds.forEach(function(id) {
        params.append('energy_type_ids[]', id);
    });

    document.getElementById('contentArea').innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div><p class="text-muted mt-2">Loading data...</p></div>';
    document.getElementById('actionButtons').style.display = 'none';

    fetch('/load-apportioning/data?' + params.toString(), {
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
    })
    .then(function(r) { return r.json(); })
    .then(function(result) {
        if (result.success) {
            currentData = result.data || {};
            renderView();
            // Show pending alert after re-render
            if (pendingAlert) {
                showAlert(pendingAlert.type, pendingAlert.message);
                pendingAlert = null;
            }
        } else {
            document.getElementById('contentArea').innerHTML = '<div class="text-center py-5"><p class="text-danger">Failed to load data. Please try again.</p></div>';
        }
    })
    .catch(function(err) {
        console.error('Load data error:', err);
        // If we have cached data, show it anyway
        if (currentData && Object.keys(currentData).length > 0) {
            renderView();
        } else {
            document.getElementById('contentArea').innerHTML = '<div class="text-center py-5"><p class="text-danger">Unable to load data. Please refresh and try again.</p></div>';
        }
    });
}

// ==================== RENDER TABLES ====================
function renderTables() {
    var contentArea = document.getElementById('contentArea');
    var unitMode = document.getElementById('unitSelect').value;
    var approachSelect = document.getElementById('approachSelect');
    var approachRaw = approachSelect.options[approachSelect.selectedIndex].text;
    // Map approach to column header label
    var approachHeaderMap = {
        'Equipment Types': 'Equipment Types',
        'Process Plants': 'Buildings/Blocks',
        'Building/Blocks': 'Buildings/Blocks'
    };
    var approachLabel = approachHeaderMap[approachRaw] || approachRaw;
    var html = '';

    selectedEnergyTypeIds.forEach(function(etId) {
        var rows = currentData[etId] || [];
        var etName = selectedEnergyTypeNames[etId] || 'Energy Type ' + etId;

        html += '<div class="card border-0 shadow-sm mb-4" data-energy-type-id="' + etId + '">';
        html += '<div class="card-header bg-white px-4 py-3 d-flex justify-content-between align-items-center">';
        html += '<h6 class="fw-bold mb-0"><i class="bi bi-lightning-charge me-2 text-primary"></i>' + escapeHtml(etName) + '</h6>';
        html += '<span class="badge bg-light text-dark" id="rowCount_' + etId + '">' + rows.length + ' rows</span>';
        html += '</div>';
        html += '<div class="card-body p-0">';
        html += '<div class="table-responsive">';
        html += '<table class="table table-bordered mb-0" id="table_' + etId + '">';

        // Header
        html += '<thead>';
        html += '<tr style="background: linear-gradient(135deg, #4472C4 0%, #2E5AA5 100%); color: white;">';
        html += '<th class="py-3 ps-3" style="min-width:250px;">' + escapeHtml(approachLabel) + '</th>';

        if (unitMode === 'energy_gj') {
            html += '<th class="py-3 text-center" style="min-width:200px;">Current Energy Consumption (GJ)</th>';
            html += '<th class="py-3 text-center" style="min-width:150px;">Load Percentage (%)</th>';
        } else {
            html += '<th class="py-3 text-center" style="min-width:150px;">Load Percentage (%)</th>';
        }

        html += '<th class="py-3 text-center" style="width:60px;"></th>';
        html += '</tr>';
        html += '</thead>';
        html += '<tbody>';

        if (rows.length > 0) {
            rows.forEach(function(row) {
                html += buildRowHtml(etId, row, unitMode);
            });
        }

        html += '</tbody>';

        // Footer
        html += '<tfoot>';
        html += '<tr class="table-light fw-bold">';
        html += '<td class="py-2 ps-3">Total</td>';

        if (unitMode === 'energy_gj') {
            html += '<td class="py-2 text-center" id="total_gj_' + etId + '">0.00</td>';
            html += '<td class="py-2 text-center" id="total_pct_' + etId + '">0.00%</td>';
        } else {
            html += '<td class="py-2 text-center" id="total_pct_' + etId + '">0.00%</td>';
        }

        html += '<td></td>';
        html += '</tr>';
        html += '<tr>';
        html += '<td colspan="' + (unitMode === 'energy_gj' ? '4' : '3') + '" class="py-2 text-center">';
        html += '<button class="btn btn-sm btn-outline-primary" onclick="addRow(\'' + etId + '\')">';
        html += '<i class="bi bi-plus-lg me-1"></i>Add Row';
        html += '</button>';
        html += '</td>';
        html += '</tr>';
        html += '</tfoot>';

        html += '</table></div></div></div>';
    });

    if (!html) {
        html = '<div class="text-center py-5"><p class="text-muted">No energy types selected.</p></div>';
    }

    // SEU Summary Rollup Table (Section 6.2)
    if (selectedEnergyTypeIds.length > 0) {
        html += buildSeuRollupTable();
    }

    // Monthly Resource Breakdown placeholder (Section 6.3/6.4)
    html += '<div id="monthlyResourceSection"></div>';

    contentArea.innerHTML = html;

    // Load monthly resource breakdown
    var year = document.getElementById('yearSelect').value;
    if (year) loadMonthlyResourceBreakdown(year);
    document.getElementById('actionButtons').style.display = 'flex';
    document.getElementById('graphFilterBtn').style.display = 'none';
    document.getElementById('saveBtn').style.display = '';

    // Calculate totals for loaded data
    selectedEnergyTypeIds.forEach(function(etId) { updateTotals(etId); });

    // Set up auto-save listeners on all inputs
    setupAutoSaveListeners();
}

// ==================== BUILD ROW HTML ====================
function buildRowHtml(etId, row, unitMode) {
    var html = '<tr data-energy-type-id="' + etId + '">';

    // Row label
    html += '<td class="py-1 px-2">';
    html += '<input type="text" class="form-control form-control-sm row-label" '
          + 'value="' + escapeHtml(row.row_label || '') + '" '
          + 'placeholder="Enter name...">';
    html += '</td>';

    if (unitMode === 'energy_gj') {
        // GJ editable (shaded)
        html += '<td class="py-1 px-2" style="background-color:#f0f0f0;">';
        html += '<input type="number" class="form-control form-control-sm text-end gj-input" '
              + 'value="' + (row.energy_consumption_gj || '') + '" '
              + 'step="0.0001" min="0" '
              + 'oninput="updateTotals(\'' + etId + '\')">';
        html += '</td>';

        // Load % readonly (white)
        html += '<td class="py-1 px-2 text-center pct-cell">';
        html += (row.load_percentage ? parseFloat(row.load_percentage).toFixed(2) + '%' : '-');
        html += '</td>';
    } else {
        // Load % editable (shaded)
        html += '<td class="py-1 px-2" style="background-color:#f0f0f0;">';
        html += '<input type="number" class="form-control form-control-sm text-end pct-input" '
              + 'value="' + (row.load_percentage || '') + '" '
              + 'step="0.01" min="0" max="100" '
              + 'oninput="updateTotals(\'' + etId + '\')">';
        html += '</td>';
    }

    // Delete button
    html += '<td class="py-1 px-2 text-center">';
    html += '<button class="btn btn-sm btn-outline-danger" onclick="deleteRow(this, \'' + etId + '\')">';
    html += '<i class="bi bi-trash"></i>';
    html += '</button>';
    html += '</td>';

    html += '</tr>';
    return html;
}

// ==================== ADD / DELETE ROW ====================
function addRow(etId) {
    var unitMode = document.getElementById('unitSelect').value;
    var table = document.getElementById('table_' + etId);
    var tbody = table.querySelector('tbody');
    var emptyRow = { row_label: '', energy_consumption_gj: null, load_percentage: null };
    var rowHtml = buildRowHtml(etId, emptyRow, unitMode);
    tbody.insertAdjacentHTML('beforeend', rowHtml);
    updateRowCount(etId);

    // Focus the new row label input
    var inputs = tbody.querySelectorAll('.row-label');
    if (inputs.length > 0) inputs[inputs.length - 1].focus();
}

function deleteRow(btn, etId) {
    btn.closest('tr').remove();
    updateTotals(etId);
    updateRowCount(etId);
}

function updateRowCount(etId) {
    var table = document.getElementById('table_' + etId);
    if (!table) return;
    var count = table.querySelectorAll('tbody tr').length;
    var badge = document.getElementById('rowCount_' + etId);
    if (badge) badge.textContent = count + ' rows';
}

// ==================== CALCULATE / TOTALS ====================
function updateTotals(etId) {
    var unitMode = document.getElementById('unitSelect').value;
    var table = document.getElementById('table_' + etId);
    if (!table) return;

    if (unitMode === 'energy_gj') {
        var totalGj = 0;
        var gjInputs = table.querySelectorAll('tbody .gj-input');
        gjInputs.forEach(function(input) {
            totalGj += parseFloat(input.value) || 0;
        });

        var totalGjCell = document.getElementById('total_gj_' + etId);
        if (totalGjCell) totalGjCell.textContent = totalGj.toFixed(2);

        var pctCells = table.querySelectorAll('tbody .pct-cell');
        var idx = 0;
        gjInputs.forEach(function(input) {
            var val = parseFloat(input.value) || 0;
            var pct = totalGj > 0 ? (val / totalGj) * 100 : 0;
            if (pctCells[idx]) pctCells[idx].textContent = pct.toFixed(2) + '%';
            idx++;
        });

        var totalPctCell = document.getElementById('total_pct_' + etId);
        if (totalPctCell) totalPctCell.textContent = totalGj > 0 ? '100.00%' : '0.00%';

    } else {
        var totalPct = 0;
        var pctInputs = table.querySelectorAll('tbody .pct-input');
        pctInputs.forEach(function(input) {
            totalPct += parseFloat(input.value) || 0;
        });

        var totalPctCell = document.getElementById('total_pct_' + etId);
        if (totalPctCell) {
            totalPctCell.textContent = totalPct.toFixed(2) + '%';
            totalPctCell.classList.remove('text-danger', 'text-success');
            if (pctInputs.length > 0) {
                if (Math.abs(totalPct - 100) < 0.01) {
                    totalPctCell.classList.add('text-success');
                } else {
                    totalPctCell.classList.add('text-danger');
                }
            }
        }
    }
}

// ==================== SAVE ====================
function setupAutoSaveListeners() {
    // Listen to add row button to rebind listeners on new rows
    document.querySelectorAll('#contentArea .btn-outline-primary').forEach(function(btn) {
        if (btn.textContent.includes('Add Row')) {
            btn.addEventListener('click', function() {
                setTimeout(setupAutoSaveListeners, 100);
            });
        }
    });
}

function collectTableData() {
    var unitMode = document.getElementById('unitSelect').value;
    var tables = {};

    selectedEnergyTypeIds.forEach(function(etId) {
        var table = document.getElementById('table_' + etId);
        if (!table) return;

        var rows = [];
        var trs = table.querySelectorAll('tbody tr');
        trs.forEach(function(tr, idx) {
            var label = tr.querySelector('.row-label').value.trim();
            if (!label) return;

            var gjInput = tr.querySelector('.gj-input');
            var pctInput = tr.querySelector('.pct-input');
            var pctCell = tr.querySelector('.pct-cell');

            var gj = gjInput ? (parseFloat(gjInput.value) || null) : null;
            var pct = pctInput ? (parseFloat(pctInput.value) || null) : null;

            // In energy_gj mode, read calculated percentage from cell text
            if (unitMode === 'energy_gj' && pctCell) {
                pct = parseFloat(pctCell.textContent) || null;
            }

            rows.push({
                row_label: label,
                energy_consumption_gj: gj,
                load_percentage: pct,
                sort_order: idx
            });
        });

        tables[etId] = rows;
    });

    return tables;
}

function manualSaveAll() {
    var year = document.getElementById('yearSelect').value;
    var approachId = document.getElementById('approachSelect').value;
    var unitMode = document.getElementById('unitSelect').value;
    var viewMode = document.getElementById('viewSelect').value;

    if (viewMode !== 'Table') return;

    var tables = collectTableData();

    // Client-side validation for load_percentage mode
    if (unitMode === 'load_percentage') {
        for (var etId in tables) {
            if (tables[etId].length > 0) {
                var sum = tables[etId].reduce(function(acc, r) {
                    return acc + (r.load_percentage || 0);
                }, 0);
                if (Math.abs(sum - 100) > 0.01) {
                    showAlert('danger', 'Load percentages for energy type must sum to 100%. Current sum: ' + sum.toFixed(2) + '%');
                    return;
                }
            }
        }
    }

    var saveBtn = document.getElementById('saveBtn');
    saveBtn.disabled = true;
    saveBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Saving...';

    fetch('/load-apportioning/save', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            year: parseInt(year),
            approach_id: parseInt(approachId),
            unit_mode: unitMode,
            tables: tables
        })
    })
    .then(function(r) { return r.json(); })
    .then(function(result) {
        if (result.success || result.status === 'success') {
            // Set pending alert, then re-fetch data so SEU rollup, graphs, and tables refresh
            pendingAlert = { type: 'success', message: 'Data saved successfully.' };
            loadData();
        } else {
            showAlert('danger', result.message || 'Failed to save data.');
        }
    })
    .catch(function(err) {
        console.error('Save error:', err);
        showAlert('danger', 'Network error. Please try again.');
    })
    .finally(function() {
        saveBtn.disabled = false;
        saveBtn.innerHTML = '<i class="bi bi-check-lg me-2"></i>Save';
    });
}


// ==================== HELPERS ====================
function showAlert(type, message) {
    // Remove existing alerts
    var existing = document.querySelectorAll('#contentArea > .alert');
    existing.forEach(function(el) { el.remove(); });

    var icon = type === 'success' ? 'check-circle' : (type === 'danger' ? 'exclamation-triangle' : 'info-circle');
    var alertHtml = '<div class="alert alert-' + type + ' alert-dismissible fade show">'
        + '<i class="bi bi-' + icon + ' me-2"></i>'
        + escapeHtml(message)
        + '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>'
        + '</div>';
    document.getElementById('contentArea').insertAdjacentHTML('afterbegin', alertHtml);

    // Auto-dismiss success after 3 seconds
    if (type === 'success') {
        setTimeout(function() {
            var alert = document.querySelector('#contentArea > .alert-success');
            if (alert) {
                var bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                bsAlert.close();
            }
        }, 3000);
    }
}

// ==================== SEU SUMMARY ROLLUP (Section 6.2) ====================
function buildSeuRollupTable() {
    // Aggregate data across all energy types
    var grandTotalGj = 0;
    var seuRows = [];

    selectedEnergyTypeIds.forEach(function(etId) {
        var rows = currentData[etId] || [];
        var etName = selectedEnergyTypeNames[etId] || 'Energy Type ' + etId;
        var typeTotal = 0;

        rows.forEach(function(r) {
            typeTotal += parseFloat(r.energy_consumption_gj) || 0;
        });

        grandTotalGj += typeTotal;
        seuRows.push({ name: etName, totalGj: typeTotal, rowCount: rows.length });
    });

    var html = '<div class="card border-0 shadow-sm mb-4">';
    html += '<div class="card-header bg-white d-flex align-items-center justify-content-between px-4 py-3" style="cursor:pointer;user-select:none;" onclick="toggleSeuRollup()">';
    html += '<div><h6 class="fw-bold mb-0"><i class="bi bi-diagram-3 me-2 text-primary"></i>SEU Summary Rollup</h6>';
    html += '<small class="text-muted">Aggregated load by energy type (Section 6.2)</small></div>';
    html += '<i class="bi bi-chevron-down" id="seuRollupChevron" style="transition:transform 0.3s;"></i>';
    html += '</div>';

    html += '<div id="seuRollupCollapse">';
    html += '<div class="table-responsive">';
    html += '<table class="table table-hover align-middle mb-0">';

    // Header
    html += '<thead>';
    html += '<tr style="background: linear-gradient(135deg, #4472C4 0%, #2E5AA5 100%); color: white;">';
    html += '<th class="py-3 ps-3">SEUs (Energy Type)</th>';
    html += '<th class="py-3 text-center">Items</th>';
    html += '<th class="py-3 text-center">Load (GJ)</th>';
    html += '<th class="py-3 text-center">Load (%)</th>';
    html += '</tr></thead><tbody>';

    // Data rows
    seuRows.forEach(function(row) {
        var pct = grandTotalGj > 0 ? (row.totalGj / grandTotalGj * 100) : 0;
        html += '<tr>';
        html += '<td class="py-2 ps-3 fw-semibold">' + escapeHtml(row.name) + '</td>';
        html += '<td class="py-2 text-center">' + row.rowCount + '</td>';
        html += '<td class="py-2 text-center">' + row.totalGj.toFixed(2) + '</td>';
        html += '<td class="py-2 text-center">' + pct.toFixed(2) + '%</td>';
        html += '</tr>';
    });

    // Total row
    html += '<tr class="table-light fw-bold">';
    html += '<td class="py-2 ps-3">Grand Total</td>';
    html += '<td class="py-2 text-center">' + seuRows.reduce(function(s, r) { return s + r.rowCount; }, 0) + '</td>';
    html += '<td class="py-2 text-center">' + grandTotalGj.toFixed(2) + '</td>';
    html += '<td class="py-2 text-center">' + (grandTotalGj > 0 ? '100.00%' : '0.00%') + '</td>';
    html += '</tr>';

    html += '</tbody></table></div></div></div>';
    return html;
}

window.toggleSeuRollup = function() {
    var el = document.getElementById('seuRollupCollapse');
    var chevron = document.getElementById('seuRollupChevron');
    if (el.style.display === 'none') {
        el.style.display = '';
        chevron.style.transform = '';
    } else {
        el.style.display = 'none';
        chevron.style.transform = 'rotate(180deg)';
    }
};

// ==================== MONTHLY RESOURCE BREAKDOWN (Section 6.3/6.4) ====================
function loadMonthlyResourceBreakdown(year) {
    var container = document.getElementById('monthlyResourceSection');
    if (!container) return;

    fetch('/load-apportioning/monthly-ng?year=' + year, {
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
    })
    .then(function(r) { return r.json(); })
    .then(function(result) {
        if (!result.success || !result.meters || result.meters.length === 0) {
            container.innerHTML = '';
            return;
        }
        container.innerHTML = buildMonthlyResourceTable(result);
    })
    .catch(function(err) {
        console.error('Monthly resource load error:', err);
        container.innerHTML = '';
    });
}

function buildMonthlyResourceTable(data) {
    var meters = data.meters;
    var monthLabels = data.month_labels;
    var monthlyTotals = data.monthly_totals;
    var grandTotal = data.grand_total;

    var html = '<div class="card border-0 shadow-sm mb-4">';
    html += '<div class="card-header bg-white d-flex align-items-center justify-content-between px-4 py-3" style="cursor:pointer;user-select:none;" onclick="toggleMonthlyResource()">';
    html += '<div><h6 class="fw-bold mb-0"><i class="bi bi-calendar3 me-2 text-primary"></i>Monthly Energy Resource Breakdown</h6>';
    html += '<small class="text-muted">Monthly consumption by resource meter (Section 6.3/6.4)</small></div>';
    html += '<i class="bi bi-chevron-down" id="monthlyResourceChevron" style="transition:transform 0.3s;"></i>';
    html += '</div>';

    html += '<div id="monthlyResourceCollapse">';
    html += '<div class="table-responsive">';
    html += '<table class="table table-hover table-bordered align-middle mb-0" style="font-size:0.85rem;">';

    // Header
    html += '<thead>';
    html += '<tr style="background: linear-gradient(135deg, #4472C4 0%, #2E5AA5 100%); color: white;">';
    html += '<th class="py-3 ps-3" style="min-width:80px;">Month</th>';
    meters.forEach(function(m) {
        html += '<th class="py-3 text-center" style="min-width:100px;">' + escapeHtml(m.name) + ' (GJ)</th>';
    });
    html += '<th class="py-3 text-center" style="min-width:100px;">SUM (GJ)</th>';
    html += '<th class="py-3 text-center" style="min-width:80px;">%</th>';
    html += '</tr></thead><tbody>';

    // Monthly rows
    for (var i = 0; i < 12; i++) {
        html += '<tr>';
        html += '<td class="py-2 ps-3 fw-semibold">' + monthLabels[i] + '</td>';
        meters.forEach(function(m) {
            html += '<td class="py-2 text-end">' + m.monthly[i].toFixed(2) + '</td>';
        });
        html += '<td class="py-2 text-end fw-bold" style="background:rgba(68,114,196,0.06);">' + monthlyTotals[i].toFixed(2) + '</td>';
        var monthPct = grandTotal > 0 ? (monthlyTotals[i] / grandTotal * 100) : 0;
        html += '<td class="py-2 text-end" style="background:rgba(68,114,196,0.06);">' + monthPct.toFixed(2) + '%</td>';
        html += '</tr>';
    }

    // SUM row
    html += '<tr class="table-light fw-bold">';
    html += '<td class="py-2 ps-3">Total</td>';
    meters.forEach(function(m) {
        html += '<td class="py-2 text-end">' + m.total.toFixed(2) + '</td>';
    });
    html += '<td class="py-2 text-end" style="background:rgba(68,114,196,0.12);">' + grandTotal.toFixed(2) + '</td>';
    html += '<td class="py-2 text-end" style="background:rgba(68,114,196,0.12);">100.00%</td>';
    html += '</tr>';

    // % row per meter
    html += '<tr style="background:#e8f4f8;">';
    html += '<td class="py-2 ps-3 fw-bold">Apportioning (%)</td>';
    meters.forEach(function(m) {
        html += '<td class="py-2 text-end fw-bold">' + m.percentage.toFixed(2) + '%</td>';
    });
    html += '<td class="py-2 text-end fw-bold">100.00%</td>';
    html += '<td class="py-2 text-end"></td>';
    html += '</tr>';

    html += '</tbody></table></div></div></div>';
    return html;
}

window.toggleMonthlyResource = function() {
    var el = document.getElementById('monthlyResourceCollapse');
    var chevron = document.getElementById('monthlyResourceChevron');
    if (el.style.display === 'none') {
        el.style.display = '';
        chevron.style.transform = '';
    } else {
        el.style.display = 'none';
        chevron.style.transform = 'rotate(180deg)';
    }
};

function escapeHtml(str) {
    if (!str) return '';
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(str));
    return div.innerHTML;
}

// ==================== GRAPH VIEW ====================
function getGraphFilterValues() {
    var chartTypeRadio = document.querySelector('input[name="la_chart_type"]:checked');
    var layoutRadio = document.querySelector('input[name="la_layout"]:checked');
    var labelFormatRadio = document.querySelector('input[name="la_label_format"]:checked');
    var colorSchemeRadio = document.querySelector('input[name="la_color_scheme"]:checked');

    var decimalEl = document.getElementById('la_decimal_places');
    return {
        chartType: chartTypeRadio ? chartTypeRadio.value : 'bar',
        layout: layoutRadio ? layoutRadio.value : 'separate',
        showLabels: document.getElementById('la_show_labels').checked,
        showLegend: document.getElementById('la_show_legend').checked,
        showGrid: document.getElementById('la_show_grid').checked,
        labelFormat: labelFormatRadio ? labelFormatRadio.value : 'value',
        colorScheme: colorSchemeRadio ? colorSchemeRadio.value : 'default',
        decimalPlaces: decimalEl ? parseInt(decimalEl.value) : 2
    };
}

function destroyAllCharts() {
    for (var key in laChartInstances) {
        if (laChartInstances[key]) laChartInstances[key].destroy();
    }
    laChartInstances = {};
}

function renderGraphs() {
    destroyAllCharts();
    var contentArea = document.getElementById('contentArea');
    var unitMode = document.getElementById('unitSelect').value;
    var approachSelect = document.getElementById('approachSelect');
    var approachLabel = approachSelect.options[approachSelect.selectedIndex].text;
    var gf = getGraphFilterValues();
    var colors = chartColors[gf.colorScheme] || chartColors['default'];

    if (gf.chartType === 'pareto') {
        // Pareto always renders separately
        renderSeparateGraphs(contentArea, unitMode, approachLabel, gf, colors);
    } else if (gf.layout === 'combined') {
        renderCombinedGraph(contentArea, unitMode, approachLabel, gf, colors);
    } else {
        renderSeparateGraphs(contentArea, unitMode, approachLabel, gf, colors);
    }

    document.getElementById('actionButtons').style.display = 'flex';
    document.getElementById('graphFilterBtn').style.display = '';
    document.getElementById('saveBtn').style.display = 'none';
}

function renderSeparateGraphs(contentArea, unitMode, approachLabel, gf, colors) {
    var html = '';

    // Add filter button at top
    html += '<div class="mb-3 d-flex justify-content-end">';
    html += '<button class="btn btn-primary" onclick="openGraphFilterModal()" style="border-radius: 25px;">';
    html += '<i class="bi bi-funnel-fill me-2"></i>Filter';
    html += '</button>';
    html += '</div>';

    selectedEnergyTypeIds.forEach(function(etId) {
        var etName = selectedEnergyTypeNames[etId] || 'Energy Type ' + etId;
        html += '<div class="card border-0 shadow-sm mb-4">';
        html += '<div class="card-header bg-white px-4 py-3">';
        html += '<h6 class="fw-bold mb-0"><i class="bi bi-bar-chart me-2 text-primary"></i>' + escapeHtml(etName) + ' - ' + escapeHtml(approachLabel) + '</h6>';
        html += '</div>';
        html += '<div class="card-body">';
        html += '<div style="position:relative; height:400px;">';
        html += '<canvas id="chart_' + etId + '"></canvas>';
        html += '</div>';
        html += '</div></div>';
    });

    if (!html) {
        html = '<div class="text-center py-5"><p class="text-muted">No energy types selected.</p></div>';
    }

    contentArea.innerHTML = html;

    // Render each chart
    selectedEnergyTypeIds.forEach(function(etId) {
        var rows = currentData[etId] || [];
        if (rows.length === 0) return;
        var canvas = document.getElementById('chart_' + etId);
        if (!canvas) return;

        var labels = rows.map(function(r) { return r.row_label || 'Unnamed'; });
        var data = rows.map(function(r) {
            if (unitMode === 'energy_gj') {
                return parseFloat(r.energy_consumption_gj) || 0;
            } else {
                return parseFloat(r.load_percentage) || 0;
            }
        });

        // Calculate percentages for labels
        var total = data.reduce(function(a, b) { return a + b; }, 0);
        var percentages = data.map(function(v) { return total > 0 ? ((v / total) * 100).toFixed(2) : '0.00'; });

        var bgColors = labels.map(function(_, i) { return colors[i % colors.length]; });

        if (gf.chartType === 'pareto') {
            laChartInstances[etId] = buildParetoChart(canvas, labels, data, colors, gf, unitMode);
        } else {
            laChartInstances[etId] = buildChart(canvas, labels, data, percentages, bgColors, gf, unitMode);
        }
    });
}

// ==================== PARETO CHART ====================
function buildParetoChart(canvas, labels, data, colors, gf, unitMode) {
    // Sort descending
    var paired = labels.map(function(l, i) { return { label: l, value: data[i] }; });
    paired.sort(function(a, b) { return b.value - a.value; });
    var sortedLabels = paired.map(function(p) { return p.label; });
    var sortedData = paired.map(function(p) { return p.value; });
    var dp = gf.decimalPlaces || 2;

    // Cumulative percentage
    var total = sortedData.reduce(function(a, b) { return a + b; }, 0);
    var cumPct = [];
    var running = 0;
    sortedData.forEach(function(v) {
        running += v;
        cumPct.push(total > 0 ? parseFloat(((running / total) * 100).toFixed(2)) : 0);
    });

    var bgColors = sortedLabels.map(function(_, i) { return colors[i % colors.length] + 'CC'; });
    var borderColors = sortedLabels.map(function(_, i) { return colors[i % colors.length]; });

    var plugins = [];
    if (gf.showLabels) plugins.push(ChartDataLabels);

    return new Chart(canvas.getContext('2d'), {
        type: 'bar',
        data: {
            labels: sortedLabels,
            datasets: [
                {
                    label: unitMode === 'energy_gj' ? 'Energy (GJ)' : 'Load (%)',
                    data: sortedData,
                    backgroundColor: bgColors,
                    borderColor: borderColors,
                    borderWidth: 1,
                    yAxisID: 'y',
                    order: 2
                },
                {
                    label: 'Cumulative %',
                    data: cumPct,
                    type: 'line',
                    borderColor: '#DC2626',
                    backgroundColor: 'transparent',
                    borderWidth: 2,
                    pointRadius: 4,
                    pointBackgroundColor: '#DC2626',
                    yAxisID: 'y1',
                    order: 1
                }
            ]
        },
        plugins: plugins,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: gf.showLegend, position: 'top' },
                title: { display: true, text: 'Pareto Analysis' + (unitMode === 'energy_gj' ? ' (GJ)' : ' (%)'), font: { size: 14, weight: 'bold' } },
                datalabels: gf.showLabels ? {
                    anchor: 'end', align: 'top', font: { size: 9 },
                    formatter: function(v, ctx) {
                        if (ctx.datasetIndex === 1) return v.toFixed(2) + '%';
                        return v > 0 ? v.toFixed(2) : '';
                    }
                } : false
            },
            scales: {
                y: { beginAtZero: true, position: 'left', grid: { display: gf.showGrid }, title: { display: true, text: unitMode === 'energy_gj' ? 'GJ' : '%' } },
                y1: { beginAtZero: true, max: 100, position: 'right', grid: { display: false }, title: { display: true, text: 'Cumulative %' }, ticks: { callback: function(v) { return v + '%'; } } }
            }
        }
    });
}

function renderCombinedGraph(contentArea, unitMode, approachLabel, gf, colors) {
    var html = '';

    // Add filter button at top
    html += '<div class="mb-3 d-flex justify-content-end">';
    html += '<button class="btn btn-primary" onclick="openGraphFilterModal()" style="border-radius: 25px;">';
    html += '<i class="bi bi-funnel-fill me-2"></i>Filter';
    html += '</button>';
    html += '</div>';

    html += '<div class="card border-0 shadow-sm mb-4">';
    html += '<div class="card-header bg-white px-4 py-3">';
    html += '<h6 class="fw-bold mb-0"><i class="bi bi-bar-chart me-2 text-primary"></i>Load Apportioning - ' + escapeHtml(approachLabel) + ' (All Energy Types)</h6>';
    html += '</div>';
    html += '<div class="card-body">';
    html += '<div style="position:relative; height:500px;">';
    html += '<canvas id="chart_combined"></canvas>';
    html += '</div>';
    html += '</div></div>';

    contentArea.innerHTML = html;

    var canvas = document.getElementById('chart_combined');
    if (!canvas) return;

    var isPieType = ['pie', 'doughnut', 'polarArea'].indexOf(gf.chartType) !== -1;

    if (isPieType) {
        // For pie/doughnut, merge all rows across energy types
        var allLabels = [], allData = [], allColors = [];
        var colorIdx = 0;
        selectedEnergyTypeIds.forEach(function(etId) {
            var etName = selectedEnergyTypeNames[etId] || 'Energy Type ' + etId;
            var rows = currentData[etId] || [];
            rows.forEach(function(r) {
                allLabels.push((r.row_label || 'Unnamed') + ' (' + etName + ')');
                allData.push(unitMode === 'energy_gj' ? (parseFloat(r.energy_consumption_gj) || 0) : (parseFloat(r.load_percentage) || 0));
                allColors.push(colors[colorIdx % colors.length]);
                colorIdx++;
            });
        });
        var total = allData.reduce(function(a, b) { return a + b; }, 0);
        var pcts = allData.map(function(v) { return total > 0 ? ((v / total) * 100).toFixed(2) : '0.00'; });
        laChartInstances['combined'] = buildChart(canvas, allLabels, allData, pcts, allColors, gf, unitMode);
    } else {
        // Bar chart: grouped by energy type with datasets
        var allLabels = [];
        selectedEnergyTypeIds.forEach(function(etId) {
            var rows = currentData[etId] || [];
            rows.forEach(function(r) {
                var lbl = r.row_label || 'Unnamed';
                if (allLabels.indexOf(lbl) === -1) allLabels.push(lbl);
            });
        });

        var datasets = [];
        selectedEnergyTypeIds.forEach(function(etId, idx) {
            var etName = selectedEnergyTypeNames[etId] || 'Energy Type ' + etId;
            var rows = currentData[etId] || [];
            var rowMap = {};
            rows.forEach(function(r) { rowMap[r.row_label || 'Unnamed'] = r; });
            var data = allLabels.map(function(lbl) {
                var r = rowMap[lbl];
                if (!r) return 0;
                return unitMode === 'energy_gj' ? (parseFloat(r.energy_consumption_gj) || 0) : (parseFloat(r.load_percentage) || 0);
            });
            datasets.push({
                label: etName,
                data: data,
                backgroundColor: colors[idx % colors.length] + 'CC',
                borderColor: colors[idx % colors.length],
                borderWidth: 1
            });
        });

        var isHorizontal = gf.chartType === 'horizontalBar';
        var plugins = [];
        if (gf.showLabels) plugins.push(ChartDataLabels);

        laChartInstances['combined'] = new Chart(canvas.getContext('2d'), {
            type: 'bar',
            data: { labels: allLabels, datasets: datasets },
            plugins: plugins,
            options: {
                indexAxis: isHorizontal ? 'y' : 'x',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: gf.showLegend, position: 'top' },
                    title: { display: true, text: 'Load Apportioning' + (unitMode === 'energy_gj' ? ' (GJ)' : ' (%)'), font: { size: 16, weight: 'bold' } },
                    datalabels: gf.showLabels ? {
                        anchor: 'end', align: isHorizontal ? 'right' : 'top', font: { size: 10 },
                        formatter: function(v) { return v > 0 ? v.toFixed(2) : ''; }
                    } : false
                },
                scales: {
                    x: { display: true, grid: { display: gf.showGrid }, title: { display: true, text: isHorizontal ? (unitMode === 'energy_gj' ? 'Energy Consumption (GJ)' : 'Load Percentage (%)') : '' } },
                    y: { display: true, grid: { display: gf.showGrid }, beginAtZero: true, title: { display: true, text: isHorizontal ? '' : (unitMode === 'energy_gj' ? 'Energy Consumption (GJ)' : 'Load Percentage (%)') } }
                }
            }
        });
    }
}

function buildChart(canvas, labels, data, percentages, bgColors, gf, unitMode) {
    var isPieType = ['pie', 'doughnut', 'polarArea'].indexOf(gf.chartType) !== -1;
    var isHorizontal = gf.chartType === 'horizontalBar';
    var actualType = isHorizontal ? 'bar' : (isPieType ? gf.chartType : gf.chartType);

    var plugins = [];
    if (gf.showLabels) plugins.push(ChartDataLabels);

    var dataLabelFormatter = function(value, ctx) {
        if (value === 0) return '';
        var pct = percentages[ctx.dataIndex];
        if (gf.labelFormat === 'percentage') return pct + '%';
        if (gf.labelFormat === 'both') return value.toFixed(2) + ' (' + pct + '%)';
        return value.toFixed(2);
    };

    var opts = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: gf.showLegend, position: isPieType ? 'right' : 'top' },
            title: {
                display: true,
                text: unitMode === 'energy_gj' ? 'Energy Consumption (GJ)' : 'Load Percentage (%)',
                font: { size: 14, weight: 'bold' }
            },
            datalabels: gf.showLabels ? {
                anchor: isPieType ? 'center' : 'end',
                align: isPieType ? 'center' : (isHorizontal ? 'right' : 'top'),
                font: { size: 10, weight: 'bold' },
                color: isPieType ? '#fff' : '#333',
                formatter: dataLabelFormatter
            } : false,
            tooltip: {
                callbacks: {
                    label: function(ctx) {
                        var v = ctx.parsed !== undefined ? (typeof ctx.parsed === 'object' ? (ctx.parsed.y || ctx.parsed.x || ctx.parsed) : ctx.parsed) : ctx.raw;
                        var pct = percentages[ctx.dataIndex];
                        var suffix = unitMode === 'energy_gj' ? ' GJ' : '%';
                        return ctx.label + ': ' + parseFloat(v).toFixed(2) + suffix + ' (' + pct + '%)';
                    }
                }
            }
        }
    };

    if (!isPieType) {
        opts.indexAxis = isHorizontal ? 'y' : 'x';
        opts.scales = {
            x: { display: true, grid: { display: gf.showGrid }, title: { display: !isHorizontal, text: '' } },
            y: { display: true, grid: { display: gf.showGrid }, beginAtZero: true, title: { display: !isHorizontal, text: unitMode === 'energy_gj' ? 'GJ' : '%' } }
        };
        if (isHorizontal) {
            opts.scales.x.title = { display: true, text: unitMode === 'energy_gj' ? 'GJ' : '%' };
            opts.scales.y.title = { display: false };
        }
    }

    return new Chart(canvas.getContext('2d'), {
        type: actualType,
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: isPieType ? bgColors : bgColors.map(function(c) { return c + 'CC'; }),
                borderColor: bgColors,
                borderWidth: isPieType ? 2 : 1,
                hoverOffset: isPieType ? 10 : 0
            }]
        },
        plugins: plugins,
        options: opts
    });
}

// ==================== GRAPH FILTER FUNCTIONS ====================
function openGraphFilterModal() {
    new bootstrap.Modal(document.getElementById('graphFilterModal')).show();
}

function applyGraphFilter() {
    var modal = bootstrap.Modal.getInstance(document.getElementById('graphFilterModal'));
    if (modal) modal.hide();
    renderGraphs();
}

function resetGraphFilters() {
    document.getElementById('la_ct_bar').checked = true;
    document.getElementById('la_ly_separate').checked = true;
    document.getElementById('la_show_labels').checked = true;
    document.getElementById('la_show_legend').checked = true;
    document.getElementById('la_show_grid').checked = true;
    document.getElementById('la_lf_value').checked = true;
    document.getElementById('la_cs_default').checked = true;
}

function exportChartsPNG() {
    for (var key in laChartInstances) {
        var chart = laChartInstances[key];
        if (!chart) continue;
        var link = document.createElement('a');
        link.download = 'load_apportioning_' + key + '.png';
        link.href = chart.toBase64Image();
        link.click();
    }
}

function exportChartsCSV() {
    var unitMode = document.getElementById('unitSelect').value;
    var approachSelect = document.getElementById('approachSelect');
    var approachLabel = approachSelect.options[approachSelect.selectedIndex].text;
    var year = document.getElementById('yearSelect').value;

    var csv = [];

    // Header
    csv.push([
        'Year: ' + year,
        'Approach: ' + approachLabel,
        'Unit Mode: ' + (unitMode === 'energy_gj' ? 'Energy (GJ)' : 'Load Percentage')
    ].join(','));
    csv.push('');

    // Iterate through each energy type
    selectedEnergyTypeIds.forEach(function(etId) {
        var rows = currentData[etId] || [];
        var etName = selectedEnergyTypeNames[etId] || 'Energy Type ' + etId;

        // Energy type header
        csv.push(etName);

        // Column headers
        var headers = ['Row Label'];
        if (unitMode === 'energy_gj') {
            headers.push('Energy Consumption (GJ)');
            headers.push('Load Percentage (%)');
        } else {
            headers.push('Load Percentage (%)');
        }
        csv.push(headers.map(function(h) { return '"' + h + '"'; }).join(','));

        // Data rows
        rows.forEach(function(row) {
            var values = [
                row.row_label || ''
            ];

            if (unitMode === 'energy_gj') {
                values.push(row.energy_consumption_gj || '');
                values.push(row.load_percentage || '');
            } else {
                values.push(row.load_percentage || '');
            }

            csv.push(values.map(function(v) {
                // Escape quotes and wrap in quotes if contains comma or quotes
                var str = String(v).replace(/"/g, '""');
                return '"' + str + '"';
            }).join(','));
        });

        // Totals row
        var totalValues = ['Total'];
        if (rows.length > 0) {
            if (unitMode === 'energy_gj') {
                var totalGj = rows.reduce(function(sum, r) { return sum + (parseFloat(r.energy_consumption_gj) || 0); }, 0);
                totalValues.push(totalGj.toFixed(2));
                totalValues.push('100.00');
            } else {
                var totalPct = rows.reduce(function(sum, r) { return sum + (parseFloat(r.load_percentage) || 0); }, 0);
                totalValues.push(totalPct.toFixed(2));
            }
        }
        csv.push(totalValues.map(function(v) {
            var str = String(v).replace(/"/g, '""');
            return '"' + str + '"';
        }).join(','));

        csv.push('');
    });

    // Create and download file
    var csvContent = csv.join('\n');
    var blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    var link = document.createElement('a');
    var url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', 'load_apportioning_' + year + '.csv');
    link.click();
}
</script>

@endsection
