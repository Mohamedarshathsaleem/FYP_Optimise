@extends('layouts.dashboard')

@section('title', 'SEU Flagging')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <p class="text-secondary small mb-1">Pages / Energy Review / SEU Flagging</p>
            <h3 class="fw-bold">Significant Energy Use (SEU) Flagging</h3>
        </div>
        <div class="d-flex align-items-center">
            <div class="input-group search-box me-3">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control" placeholder="Search">
            </div>
            <img src="{{ asset('images/user.png') }}" class="rounded-circle" alt="User" style="width:40px;height:40px;">
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
                        <p class="mb-1 text-dark">1. Select the <strong>Year</strong> and choose the <strong>Matrix</strong> (Table or Graph) to view SEU data.</p>
                        <p class="mb-1 text-dark">2. Equipment names are automatically pulled from <strong>Load Apportioning</strong>. Configure criteria thresholds to control flagging.</p>
                        <p class="mb-0 text-dark">3. Use the <strong>Filter</strong> button to customize chart types, data sources, export, and display options.</p>
                    </div>
                </div>
                <button class="btn-close" onclick="document.getElementById('instructionsCard').style.display='none'"></button>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="row g-3 align-items-end">
                <!-- Year -->
                <div class="col-md-2">
                    <label class="form-label fw-bold">Category <span class="text-danger">*</span></label>
                    <select id="yearSelect" class="form-select" onchange="handleYearChange()">
                        @foreach($years as $year)
                            <option value="{{ $year }}" {{ $year == $selectedYear ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                        @if($years->isEmpty())
                            <option value="{{ date('Y') }}" selected>{{ date('Y') }}</option>
                        @endif
                    </select>
                </div>

                <!-- Matrix -->
                <div class="col-md-2">
                    <label class="form-label fw-bold">&nbsp;</label>
                    <select id="matrixSelect" class="form-select" onchange="updateSeuContent()">
                        <option value="">Matrix</option>
                        <option value="Table">Table</option>
                        <option value="Graph">Graph</option>
                    </select>
                </div>

                <!-- Spacer to push buttons right -->
                <div class="col-md-4"></div>

                <!-- Filter button + Criteria toggle (far right) -->
                <div class="col-md-4 d-flex justify-content-end align-items-end gap-2">
                    <button class="btn btn-primary" id="seuFilterBtn" onclick="openSeuFilterModal()" style="display:none; border-radius:25px;">
                        <i class="bi bi-funnel-fill me-2"></i>Filter
                    </button>
                    <button class="btn btn-outline-secondary btn-sm" onclick="toggleCriteriaPanel()">
                        <i class="bi bi-sliders me-1"></i>SEU Criteria
                    </button>
                </div>
            </div>

            <!-- Criteria Configuration (collapsible) -->
            <div class="mt-3" id="criteriaPanel" style="display:none;">
                <div class="card border shadow-sm">
                    <div class="card-body p-3">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label fw-bold small">Criteria Type</label>
                                <select class="form-select form-select-sm" id="criteriaType">
                                    <option value="load_percentage" {{ $criteria->criteria_type == 'load_percentage' ? 'selected' : '' }}>Load Percentage</option>
                                    <option value="absolute_gj" {{ $criteria->criteria_type == 'absolute_gj' ? 'selected' : '' }}>Absolute GJ</option>
                                    <option value="custom" {{ $criteria->criteria_type == 'custom' ? 'selected' : '' }}>Custom</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold small">Lower Limit (%)</label>
                                <input type="number" class="form-control form-control-sm" id="lowerLimit" step="0.01" min="0" max="100" value="{{ $criteria->lower_limit * 100 }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold small">Upper Limit (%)</label>
                                <input type="number" class="form-control form-control-sm" id="upperLimit" step="0.01" min="0" max="100" value="{{ $criteria->upper_limit * 100 }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small">Notes</label>
                                <input type="text" class="form-control form-control-sm" id="criteriaNotes" value="{{ $criteria->notes }}" placeholder="Optional...">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button class="btn btn-primary btn-sm w-100" id="btnSaveCriteria" onclick="saveCriteria()">
                                    <i class="bi bi-check-lg me-1"></i>Save
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Area -->
            <div class="mt-4" id="seuContentArea">
                <div class="text-center py-5">
                    <p class="text-muted">Please choose matrix</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toggle Flag Modal -->
<div class="modal fade" id="toggleFlagModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:15px;">
            <div class="modal-header border-0">
                <h5 class="fw-bold">Toggle SEU Flag</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to change the SEU flag status for this item?</p>
                <div class="mb-3">
                    <label class="form-label fw-bold">Reason (optional)</label>
                    <textarea class="form-control" id="toggleReason" rows="3" placeholder="Provide a reason for the manual override..."></textarea>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-primary" id="confirmToggle" onclick="confirmToggleFlag()">Confirm Toggle</button>
            </div>
        </div>
    </div>
</div>

<!-- SEU Filter Modal (SEC-style with radio buttons, 4 tabs) -->
<div class="modal fade" id="seuFilterModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 1200px;">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-body p-4">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="fw-bold mb-0" style="color: #4472C4;"><i class="bi bi-funnel me-2"></i>Filter</h4>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-secondary btn-sm" onclick="resetSeuFilters()" title="Reset all"><i class="bi bi-arrow-counterclockwise"></i></button>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                </div>

                <!-- Tab Navigation -->
                <ul class="nav nav-tabs nav-fill mb-3" role="tablist" style="font-size:0.8rem;">
                    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#seu_tab_chart">Chart & Display</a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#seu_tab_data">Data Sources</a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#seu_tab_stats">Statistics & Trendline</a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#seu_tab_colors">Colors</a></li>
                </ul>

                <div class="tab-content" style="font-size: 0.85rem; min-height: 350px; max-height: 450px; overflow-y: auto;">

                    <!-- TAB 1: Chart & Display -->
                    <div class="tab-pane fade show active" id="seu_tab_chart">
                        <div class="row">
                            <div class="col-md-4">
                                <h6 class="fw-bold text-muted mb-3">Chart Type</h6>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="seu_chart_type" value="bar" id="seu_ct_bar" checked>
                                    <label class="form-check-label" for="seu_ct_bar"><i class="bi bi-bar-chart me-1"></i>Bar Chart</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="seu_chart_type" value="horizontalBar" id="seu_ct_hbar">
                                    <label class="form-check-label" for="seu_ct_hbar"><i class="bi bi-bar-chart me-1" style="transform:rotate(90deg);display:inline-block;"></i>Horizontal Bar</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="seu_chart_type" value="line" id="seu_ct_line">
                                    <label class="form-check-label" for="seu_ct_line"><i class="bi bi-graph-up me-1"></i>Line Chart</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="seu_chart_type" value="pie" id="seu_ct_pie">
                                    <label class="form-check-label" for="seu_ct_pie"><i class="bi bi-pie-chart me-1"></i>Pie Chart</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="seu_chart_type" value="stacked_bar" id="seu_ct_stacked">
                                    <label class="form-check-label" for="seu_ct_stacked"><i class="bi bi-bar-chart-steps me-1"></i>Stacked Bar</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="seu_chart_type" value="combo" id="seu_ct_combo">
                                    <label class="form-check-label" for="seu_ct_combo"><i class="bi bi-bar-chart-line me-1"></i>Combo (Bar+Line)</label>
                                </div>

                                <h6 class="fw-bold text-muted mb-3 mt-3">Metric</h6>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="seu_metric" value="gj" id="seu_m_gj" checked>
                                    <label class="form-check-label" for="seu_m_gj">Energy (GJ)</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="seu_metric" value="percentage" id="seu_m_pct">
                                    <label class="form-check-label" for="seu_m_pct">Percentage (%)</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <h6 class="fw-bold text-muted mb-3">Display Options</h6>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="seu_show_labels">
                                    <label class="form-check-label" for="seu_show_labels">Show data labels</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="seu_show_legend" checked>
                                    <label class="form-check-label" for="seu_show_legend">Show legend</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="seu_show_grid" checked>
                                    <label class="form-check-label" for="seu_show_grid">Show grid lines</label>
                                </div>

                                <h6 class="fw-bold text-muted mb-3 mt-3">Table Options</h6>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="seu_show_totals" checked>
                                    <label class="form-check-label" for="seu_show_totals">Show totals row</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <h6 class="fw-bold text-muted mb-3">Export</h6>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-outline-secondary btn-sm" onclick="exportSeuChartPNG()"><i class="bi bi-image me-1"></i>PNG</button>
                                    <button class="btn btn-outline-secondary btn-sm" onclick="exportSeuCSV()"><i class="bi bi-file-earmark-spreadsheet me-1"></i>CSV</button>
                                    <a href="{{ route('seu-flagging.export', ['year' => $selectedYear]) }}" class="btn btn-outline-success btn-sm"><i class="bi bi-file-earmark-excel me-1"></i>Export Full</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 2: Data Sources -->
                    <div class="tab-pane fade" id="seu_tab_data">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <button class="btn btn-outline-primary btn-sm me-2" onclick="document.querySelectorAll('.seu-energy-check,.seu-type-check').forEach(function(c){c.checked=true;})">Select All</button>
                                <button class="btn btn-outline-secondary btn-sm" onclick="document.querySelectorAll('.seu-energy-check,.seu-type-check').forEach(function(c){c.checked=false;})">Deselect All</button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <h6 class="fw-bold text-muted mb-3"><i class="bi bi-lightning-charge me-1"></i>Energy Category</h6>
                                <div class="form-check mb-2">
                                    <input class="form-check-input seu-energy-check" type="checkbox" id="seu_show_energy" checked>
                                    <span class="d-inline-block rounded-circle me-1" style="width:10px;height:10px;background:#4472C4;"></span>
                                    <label class="form-check-label" for="seu_show_energy">Energy</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input seu-energy-check" type="checkbox" id="seu_show_resource" checked>
                                    <span class="d-inline-block rounded-circle me-1" style="width:10px;height:10px;background:#548235;"></span>
                                    <label class="form-check-label" for="seu_show_resource">Energy Resource</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <h6 class="fw-bold text-muted mb-3"><i class="bi bi-lightning-charge me-1" style="color:#4472C4;"></i>Energy</h6>
                                <div id="seuEnergyTypeChecks">
                                    <!-- Populated dynamically from energyTypeNames -->
                                </div>
                            </div>
                            <div class="col-md-3">
                                <h6 class="fw-bold text-muted mb-3"><i class="bi bi-fuel-pump me-1" style="color:#548235;"></i>Energy Resource</h6>
                                <div id="seuResourceTypeChecks">
                                    <!-- Populated dynamically from resourceTypeNames -->
                                </div>
                            </div>
                            <div class="col-md-3">
                                <h6 class="fw-bold text-muted mb-3"><i class="bi bi-flag me-1"></i>Flag Filter</h6>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="seu_flag_filter" value="all" id="seu_ff_all" checked>
                                    <label class="form-check-label" for="seu_ff_all">All Items</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="seu_flag_filter" value="flagged" id="seu_ff_flagged">
                                    <label class="form-check-label" for="seu_ff_flagged">Flagged Only (SEU)</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="seu_flag_filter" value="not_flagged" id="seu_ff_notflagged">
                                    <label class="form-check-label" for="seu_ff_notflagged">Not Flagged Only</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 3: Statistics & Trendline -->
                    <div class="tab-pane fade" id="seu_tab_stats">
                        <div class="row">
                            <div class="col-md-4">
                                <h6 class="fw-bold text-muted mb-3">Statistics</h6>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="seu_show_min">
                                    <label class="form-check-label" for="seu_show_min">Show Minimum</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="seu_show_max">
                                    <label class="form-check-label" for="seu_show_max">Show Maximum</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="seu_show_avg" checked>
                                    <label class="form-check-label" for="seu_show_avg">Show Average</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <h6 class="fw-bold text-muted mb-3">Threshold Lines</h6>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="seu_show_threshold">
                                    <label class="form-check-label" for="seu_show_threshold">Show Lower/Upper Limit Lines</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 4: Colors -->
                    <div class="tab-pane fade" id="seu_tab_colors">
                        <h6 class="fw-bold text-muted mb-3">Color Scheme</h6>
                        <div class="row g-3">
                            <div class="col-md-4 mb-3">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="seu_color_scheme" value="default" id="seu_cs_default" checked>
                                    <label class="form-check-label fw-semibold" for="seu_cs_default">Default</label>
                                </div>
                                <div class="d-flex gap-1 ms-4">
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#4472C4;"></span>
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#548235;"></span>
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#ED7D31;"></span>
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#FFC000;"></span>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="seu_color_scheme" value="pastel" id="seu_cs_pastel">
                                    <label class="form-check-label fw-semibold" for="seu_cs_pastel">Pastel</label>
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
                                    <input class="form-check-input" type="radio" name="seu_color_scheme" value="corporate" id="seu_cs_corporate">
                                    <label class="form-check-label fw-semibold" for="seu_cs_corporate">Corporate</label>
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
                                    <input class="form-check-input" type="radio" name="seu_color_scheme" value="vibrant" id="seu_cs_vibrant">
                                    <label class="form-check-label fw-semibold" for="seu_cs_vibrant">Vibrant</label>
                                </div>
                                <div class="d-flex gap-1 ms-4">
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#FF6B6B;"></span>
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#4ECDC4;"></span>
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#45B7D1;"></span>
                                    <span class="d-inline-block rounded" style="width:20px;height:20px;background:#96CEB4;"></span>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="seu_color_scheme" value="ocean" id="seu_cs_ocean">
                                    <label class="form-check-label fw-semibold" for="seu_cs_ocean">Ocean</label>
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
                                    <input class="form-check-input" type="radio" name="seu_color_scheme" value="warm" id="seu_cs_warm">
                                    <label class="form-check-label fw-semibold" for="seu_cs_warm">Warm</label>
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
                    <button type="button" class="btn btn-outline-secondary px-4" onclick="resetSeuFilters()" style="border-radius: 25px;">Clear All</button>
                    <button type="button" class="btn btn-primary px-5 py-2" onclick="applySeuFilter()" style="border-radius: 25px; min-width: 200px;">Apply & View</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    #seuFilterModal .nav-tabs .nav-link { font-size: 0.8rem; padding: 0.5rem 0.75rem; }
    #seuFilterModal .nav-tabs .nav-link.active { color: #4472C4; border-bottom: 2px solid #4472C4; font-weight: 600; }
    @media (max-width: 768px) {
        #seuFilterModal .modal-dialog { max-width: 100%; margin: 0.5rem; }
        #seuFilterModal .tab-content { max-height: 60vh; }
    }
</style>

<script>
var csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
var selectedYear = '{{ $selectedYear }}';

// Server-passed data for initial render
var energySeus = @json($energySeus);
var resourceSeus = @json($resourceSeus);
var criteriaData = @json($criteria);

var seuChart = null;
var toggleTargetId = null;
var toggleModal = null;

// Build unique energy type names per category for filter
var energyTypeNames = (function() {
    var names = {};
    energySeus.forEach(function(s) { if (s.energy_type_name) names[s.energy_type_name] = true; });
    return Object.keys(names).sort();
})();
var resourceTypeNames = (function() {
    var names = {};
    resourceSeus.forEach(function(s) { if (s.energy_type_name) names[s.energy_type_name] = true; });
    return Object.keys(names).sort();
})();
var allEnergyTypeNames = energyTypeNames.concat(resourceTypeNames.filter(function(n) { return energyTypeNames.indexOf(n) === -1; }));

// Color schemes
var seuColorSchemes = {
    default: { energy: ['#4472C4', '#5B9BD5', '#7FB3E0', '#A2C4E8', '#C5D9F1', '#2E5AA5', '#1A4080'], resource: ['#548235', '#70AD47', '#92C96B', '#A9D18E', '#C6E0B4', '#3B6B1F', '#285210'] },
    pastel: { energy: ['#93C5FD', '#BFDBFE', '#DBEAFE', '#A5B4FC', '#C7D2FE', '#818CF8', '#6366F1'], resource: ['#6EE7B7', '#A7F3D0', '#D1FAE5', '#86EFAC', '#BBF7D0', '#4ADE80', '#22C55E'] },
    corporate: { energy: ['#1E3A5F', '#2E5AA5', '#4472C4', '#5B9BD5', '#7FB3E0', '#A2C4E8', '#C5D9F1'], resource: ['#2D6A4F', '#40916C', '#52B788', '#74C69D', '#95D5B2', '#B7E4C7', '#D8F3DC'] },
    vibrant: { energy: ['#FF6B6B', '#EE5A24', '#FF6348', '#FC427B', '#D980FA', '#9B59B6', '#E74C3C'], resource: ['#4ECDC4', '#45B7D1', '#96CEB4', '#2ECC71', '#1ABC9C', '#16A085', '#27AE60'] },
    ocean: { energy: ['#0077B6', '#00B4D8', '#48CAE4', '#90E0EF', '#023E8A', '#0096C7', '#CAF0F8'], resource: ['#0D9488', '#14B8A6', '#2DD4BF', '#5EEAD4', '#99F6E4', '#0F766E', '#115E59'] },
    warm: { energy: ['#E63946', '#F4A261', '#E9C46A', '#D62828', '#F77F00', '#FCBF49', '#C1121F'], resource: ['#2A9D8F', '#264653', '#3A7D7B', '#48BFE3', '#457B9D', '#1D3557', '#006D77'] }
};

// ===== GET FILTER VALUES =====
function getSeuFilterValues() {
    var f = {};
    var chartRadio = document.querySelector('input[name="seu_chart_type"]:checked');
    f.chartType = chartRadio ? chartRadio.value : 'bar';

    var metricRadio = document.querySelector('input[name="seu_metric"]:checked');
    f.metric = metricRadio ? metricRadio.value : 'gj';

    var colorRadio = document.querySelector('input[name="seu_color_scheme"]:checked');
    f.colorScheme = colorRadio ? colorRadio.value : 'default';

    var flagRadio = document.querySelector('input[name="seu_flag_filter"]:checked');
    f.flagFilter = flagRadio ? flagRadio.value : 'all';

    f.showLabels = document.getElementById('seu_show_labels').checked;
    f.showLegend = document.getElementById('seu_show_legend').checked;
    f.showGrid = document.getElementById('seu_show_grid').checked;
    f.showTotals = document.getElementById('seu_show_totals').checked;
    f.showEnergy = document.getElementById('seu_show_energy').checked;
    f.showResource = document.getElementById('seu_show_resource').checked;
    f.showMin = document.getElementById('seu_show_min').checked;
    f.showMax = document.getElementById('seu_show_max').checked;
    f.showAvg = document.getElementById('seu_show_avg').checked;
    f.showThreshold = document.getElementById('seu_show_threshold').checked;

    // Collect selected Type of Energy names
    f.selectedTypeOfEnergy = [];
    document.querySelectorAll('.seu-type-check:checked').forEach(function(cb) {
        f.selectedTypeOfEnergy.push(cb.value);
    });

    return f;
}

// ===== YEAR CHANGE =====
function handleYearChange() {
    var year = document.getElementById('yearSelect').value;
    window.location.href = '{{ route("seu-flagging.index") }}?year=' + year;
}

// ===== MATRIX TOGGLE =====
function updateSeuContent() {
    var matrix = document.getElementById('matrixSelect').value;

    if (matrix) sessionStorage.setItem('seuMatrixSelect', matrix);

    if (!matrix) {
        document.getElementById('seuContentArea').innerHTML = '<div class="text-center py-5"><p class="text-muted">Please choose matrix</p></div>';
        document.getElementById('seuFilterBtn').style.display = 'none';
        return;
    }

    if (energySeus.length === 0 && resourceSeus.length === 0) {
        document.getElementById('seuContentArea').innerHTML = '<div class="alert alert-info text-center py-4"><i class="bi bi-info-circle me-2"></i>No equipment data found for <strong>' + selectedYear + '</strong>. Please enter equipment in Load Apportioning first.</div>';
        document.getElementById('seuFilterBtn').style.display = 'none';
        return;
    }

    // Show filter button in both Table and Graph mode
    document.getElementById('seuFilterBtn').style.display = '';

    if (matrix === 'Table') {
        displaySeuTable();
    } else if (matrix === 'Graph') {
        displaySeuGraph();
    }
}

// ===== FILTER BY TYPE OF ENERGY =====
function filterByTypeOfEnergy(seus, selectedTypes) {
    if (!selectedTypes || selectedTypes.length === 0 || selectedTypes.length === allEnergyTypeNames.length) {
        return seus; // All selected or none specified — no filtering
    }
    return seus.filter(function(s) {
        return selectedTypes.indexOf(s.energy_type_name || '') !== -1;
    });
}

// ===== FILTER BY FLAG =====
function filterByFlag(seus, flagFilter) {
    if (flagFilter === 'all') return seus;
    return seus.filter(function(s) {
        return flagFilter === 'flagged' ? s.is_flagged : !s.is_flagged;
    });
}

// ===== TABLE VIEW =====
function displaySeuTable() {
    var f = getSeuFilterValues();
    var filteredEnergy = f.showEnergy ? filterByFlag(energySeus, f.flagFilter) : [];
    var filteredResource = f.showResource ? filterByFlag(resourceSeus, f.flagFilter) : [];

    // Apply Type of Energy filter
    filteredEnergy = filterByTypeOfEnergy(filteredEnergy, f.selectedTypeOfEnergy);
    filteredResource = filterByTypeOfEnergy(filteredResource, f.selectedTypeOfEnergy);

    var html = '';

    if (f.showEnergy) {
        html += buildSeuTableCard('Energy SEUs', filteredEnergy, '#4472C4', 'primary', f);
    }
    if (f.showResource) {
        html += buildSeuTableCard('Energy Resource SEUs', filteredResource, '#548235', 'success', f);
    }

    if (!html) {
        html = '<div class="text-center py-5"><p class="text-muted">No data sources selected. Open Filter to select data sources.</p></div>';
    }

    document.getElementById('seuContentArea').innerHTML = html;

    // Attach toggle listeners
    document.querySelectorAll('.btn-toggle-flag').forEach(function(btn) {
        btn.addEventListener('click', function() {
            toggleTargetId = this.dataset.id;
            document.getElementById('toggleReason').value = '';
            if (!toggleModal) toggleModal = new bootstrap.Modal(document.getElementById('toggleFlagModal'));
            toggleModal.show();
        });
    });
}

function buildSeuTableCard(title, seus, color, badgeClass, f) {
    var totalGj = 0;
    seus.forEach(function(s) { totalGj += parseFloat(s.current_gj) || 0; });

    var html = '<div class="card border-0 shadow-sm mb-4">';
    html += '<div class="card-header bg-white border-0 py-3">';
    html += '<div class="d-flex justify-content-between align-items-center">';
    html += '<h5 class="fw-bold mb-0"><span class="badge bg-' + badgeClass + ' me-2">' + seus.length + '</span>' + title + '</h5>';
    html += '<span class="text-muted">Total: ' + formatNum(totalGj) + ' GJ</span>';
    html += '</div></div>';
    html += '<div class="card-body p-0"><div class="table-responsive">';
    html += '<table class="table table-hover mb-0">';
    html += '<thead style="background:' + color + '; color:white;">';
    html += '<tr><th class="py-3 ps-4">#</th><th class="py-3">Name of SEU</th><th class="py-3">Type of Energy</th><th class="py-3 text-end">Current GJ</th>';
    html += '<th class="py-3 text-end">% of Overall Usage</th><th class="py-3 text-center">Flagged</th>';
    html += '<th class="py-3 text-center">Override</th><th class="py-3 text-center">Actions</th></tr></thead><tbody>';

    if (seus.length === 0) {
        html += '<tr><td colspan="8" class="text-center py-5 text-muted"><i class="bi bi-inbox" style="font-size:2rem;"></i>';
        html += '<p class="mt-2 mb-0">No data. Enter equipment in Load Apportioning.</p></td></tr>';
    } else {
        seus.forEach(function(seu, idx) {
            html += '<tr data-id="' + seu.id + '">';
            html += '<td class="ps-4">' + (idx + 1) + '</td>';
            html += '<td class="fw-bold">' + escapeHtml(seu.seu_name) + '</td>';
            html += '<td>' + escapeHtml(seu.energy_type_name || '-') + '</td>';
            html += '<td class="text-end">' + formatNum(seu.current_gj) + '</td>';
            html += '<td class="text-end">' + (parseFloat(seu.overall_usage_pct) * 100).toFixed(2) + '%</td>';
            html += '<td class="text-center">';
            if (seu.is_flagged) {
                html += '<span class="badge bg-danger"><i class="bi bi-flag-fill"></i> SEU</span>';
            } else {
                html += '<span class="badge bg-secondary">Not SEU</span>';
            }
            html += '</td>';
            html += '<td class="text-center">';
            if (seu.is_manually_overridden) {
                html += '<span class="badge bg-warning text-dark" title="' + escapeHtml(seu.override_reason || '') + '"><i class="bi bi-pencil-fill"></i> Manual</span>';
            } else {
                html += '<span class="text-muted">Auto</span>';
            }
            html += '</td>';
            html += '<td class="text-center"><button class="btn btn-sm btn-outline-' + badgeClass + ' btn-toggle-flag" data-id="' + seu.id + '" title="Toggle flag">';
            html += '<i class="bi ' + (seu.is_flagged ? 'bi-flag' : 'bi-flag-fill') + '"></i></button></td>';
            html += '</tr>';
        });

        // Totals footer
        if (f.showTotals) {
            html += '<tr style="background:#f0f0f0; font-weight:bold;"><td colspan="3" class="ps-4">Total</td>';
            html += '<td class="text-end">' + formatNum(totalGj) + '</td>';
            var totalPct = 0;
            seus.forEach(function(s) { totalPct += parseFloat(s.overall_usage_pct) || 0; });
            html += '<td class="text-end">' + (totalPct * 100).toFixed(2) + '%</td>';
            html += '<td colspan="3"></td></tr>';
        }
    }

    html += '</tbody></table></div></div></div>';
    return html;
}

// ===== GRAPH VIEW =====
function displaySeuGraph() {
    var html = '<div class="card border-0 shadow-sm">';
    html += '<div class="card-header bg-white d-flex align-items-center justify-content-between px-4 py-3">';
    html += '<div><h6 class="fw-bold mb-0" id="seuChartTitle">SEU Energy Distribution</h6>';
    html += '<small class="text-muted">Year: ' + selectedYear + '</small></div>';
    html += '</div>';
    html += '<div class="card-body p-4">';
    html += '<div style="position:relative;height:450px;"><canvas id="seuChartCanvas"></canvas></div>';
    html += '</div></div>';

    document.getElementById('seuContentArea').innerHTML = html;
    setTimeout(function() { renderSeuChart(); }, 50);
}

function renderSeuChart() {
    var canvas = document.getElementById('seuChartCanvas');
    if (!canvas) return;
    if (seuChart) seuChart.destroy();

    var f = getSeuFilterValues();
    var colors = seuColorSchemes[f.colorScheme] || seuColorSchemes['default'];

    var filteredEnergy = f.showEnergy ? filterByFlag(energySeus, f.flagFilter) : [];
    var filteredResource = f.showResource ? filterByFlag(resourceSeus, f.flagFilter) : [];

    // Apply Type of Energy filter
    filteredEnergy = filterByTypeOfEnergy(filteredEnergy, f.selectedTypeOfEnergy);
    filteredResource = filterByTypeOfEnergy(filteredResource, f.selectedTypeOfEnergy);

    var isDoughnut = (f.chartType === 'pie');
    var isHorizontal = (f.chartType === 'horizontalBar');
    var isStacked = (f.chartType === 'stacked_bar');
    var isLine = (f.chartType === 'line');
    var isCombo = (f.chartType === 'combo');

    if (isDoughnut) {
        renderSeuDoughnut(canvas, filteredEnergy, filteredResource, f, colors);
    } else {
        renderSeuBarLine(canvas, filteredEnergy, filteredResource, f, colors, isHorizontal, isStacked, isLine, isCombo);
    }

    // Update title
    var titleEl = document.getElementById('seuChartTitle');
    if (titleEl) {
        var typeLabel = f.metric === 'gj' ? 'Energy (GJ)' : 'Percentage (%)';
        titleEl.textContent = 'SEU Distribution - ' + typeLabel;
    }
}

function renderSeuDoughnut(canvas, energy, resource, f, colors) {
    var labels = [];
    var values = [];
    var bgColors = [];
    var ci = 0;

    energy.forEach(function(s) {
        labels.push(s.seu_name + ' (E)');
        values.push(f.metric === 'gj' ? parseFloat(s.current_gj) : parseFloat(s.overall_usage_pct) * 100);
        bgColors.push(colors.energy[ci % colors.energy.length]);
        ci++;
    });
    ci = 0;
    resource.forEach(function(s) {
        labels.push(s.seu_name + ' (R)');
        values.push(f.metric === 'gj' ? parseFloat(s.current_gj) : parseFloat(s.overall_usage_pct) * 100);
        bgColors.push(colors.resource[ci % colors.resource.length]);
        ci++;
    });

    seuChart = new Chart(canvas.getContext('2d'), {
        type: 'doughnut',
        data: { labels: labels, datasets: [{ data: values, backgroundColor: bgColors, borderWidth: 2, borderColor: '#fff' }] },
        plugins: f.showLabels ? [ChartDataLabels] : [],
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: f.showLegend, position: 'right', labels: { padding: 10, font: { size: 11 } } },
                datalabels: f.showLabels ? {
                    color: '#fff', font: { weight: 'bold', size: 11 },
                    formatter: function(val, ctx) {
                        var total = ctx.dataset.data.reduce(function(a, b) { return a + b; }, 0);
                        return total > 0 ? (val / total * 100).toFixed(1) + '%' : '';
                    }
                } : { display: false }
            }
        }
    });
}

function renderSeuBarLine(canvas, energy, resource, f, colors, isHorizontal, isStacked, isLine, isCombo) {
    var allNames = [];
    var energyMap = {};
    var resourceMap = {};

    energy.forEach(function(s) {
        if (allNames.indexOf(s.seu_name) === -1) allNames.push(s.seu_name);
        energyMap[s.seu_name] = f.metric === 'gj' ? parseFloat(s.current_gj) : parseFloat(s.overall_usage_pct) * 100;
    });
    resource.forEach(function(s) {
        if (allNames.indexOf(s.seu_name) === -1) allNames.push(s.seu_name);
        resourceMap[s.seu_name] = f.metric === 'gj' ? parseFloat(s.current_gj) : parseFloat(s.overall_usage_pct) * 100;
    });

    var datasets = [];
    var actualType = isStacked ? 'bar' : (isHorizontal ? 'bar' : (isLine ? 'line' : 'bar'));

    if (energy.length > 0) {
        datasets.push({
            label: 'Energy (Electricity)',
            data: allNames.map(function(n) { return energyMap[n] || 0; }),
            backgroundColor: colors.energy[0] + 'E6',
            borderColor: colors.energy[0],
            borderWidth: (isLine || isCombo) ? 2 : 1,
            borderRadius: (!isLine) ? 4 : 0,
            tension: 0.3,
            type: isCombo ? 'bar' : undefined
        });
    }
    if (resource.length > 0) {
        datasets.push({
            label: 'Energy Resource (NG)',
            data: allNames.map(function(n) { return resourceMap[n] || 0; }),
            backgroundColor: colors.resource[0] + 'E6',
            borderColor: colors.resource[0],
            borderWidth: (isLine || isCombo) ? 2 : 1,
            borderRadius: (!isLine) ? 4 : 0,
            tension: 0.3,
            type: isCombo ? 'line' : undefined
        });
    }

    // Stat overlays
    var allValues = [];
    datasets.forEach(function(ds) { ds.data.forEach(function(v) { if (v > 0) allValues.push(v); }); });

    if (allValues.length > 0) {
        var minVal = Math.min.apply(null, allValues);
        var maxVal = Math.max.apply(null, allValues);
        var avgVal = allValues.reduce(function(a, b) { return a + b; }, 0) / allValues.length;

        if (f.showAvg) {
            datasets.push({ label: 'Avg (' + formatNum(avgVal) + ')', data: Array(allNames.length).fill(avgVal), borderColor: '#28A745', borderWidth: 2, borderDash: [6, 3], pointRadius: 0, type: 'line', fill: false });
        }
        if (f.showMin) {
            datasets.push({ label: 'Min (' + formatNum(minVal) + ')', data: Array(allNames.length).fill(minVal), borderColor: '#17A2B8', borderWidth: 1, borderDash: [4, 4], pointRadius: 0, type: 'line', fill: false });
        }
        if (f.showMax) {
            datasets.push({ label: 'Max (' + formatNum(maxVal) + ')', data: Array(allNames.length).fill(maxVal), borderColor: '#DC3545', borderWidth: 1, borderDash: [4, 4], pointRadius: 0, type: 'line', fill: false });
        }
    }

    if (f.showThreshold && criteriaData && f.metric === 'percentage') {
        var lowerPct = parseFloat(criteriaData.lower_limit) * 100;
        var upperPct = parseFloat(criteriaData.upper_limit) * 100;
        datasets.push({ label: 'Lower Limit (' + lowerPct.toFixed(1) + '%)', data: Array(allNames.length).fill(lowerPct), borderColor: '#E74C3C', borderWidth: 2, borderDash: [10, 5], pointRadius: 0, type: 'line', fill: false });
        datasets.push({ label: 'Upper Limit (' + upperPct.toFixed(1) + '%)', data: Array(allNames.length).fill(upperPct), borderColor: '#E74C3C80', borderWidth: 2, borderDash: [10, 5], pointRadius: 0, type: 'line', fill: false });
    }

    var scales = {
        y: { beginAtZero: true, title: { display: true, text: f.metric === 'gj' ? 'GJ' : '%' }, grid: { display: f.showGrid }, stacked: isStacked },
        x: { title: { display: true, text: 'Equipment' }, grid: { display: false }, stacked: isStacked }
    };

    if (isHorizontal) {
        scales = {
            x: { beginAtZero: true, title: { display: true, text: f.metric === 'gj' ? 'GJ' : '%' }, grid: { display: f.showGrid }, stacked: isStacked },
            y: { title: { display: true, text: 'Equipment' }, grid: { display: false }, stacked: isStacked }
        };
    }

    seuChart = new Chart(canvas.getContext('2d'), {
        type: actualType,
        data: { labels: allNames, datasets: datasets },
        plugins: f.showLabels ? [ChartDataLabels] : [],
        options: {
            indexAxis: isHorizontal ? 'y' : 'x',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: f.showLegend, position: 'top', labels: { padding: 15, font: { size: 12 } } },
                title: { display: true, text: 'SEU Distribution (' + selectedYear + ')', font: { size: 16, weight: 'bold' } },
                datalabels: f.showLabels ? {
                    anchor: isHorizontal ? 'end' : 'end',
                    align: isHorizontal ? 'right' : 'top',
                    font: { size: 9 },
                    formatter: function(v) { return v > 0 ? formatNum(v) : ''; }
                } : { display: false }
            },
            scales: scales
        }
    });
}

// ===== CRITERIA =====
function toggleCriteriaPanel() {
    var panel = document.getElementById('criteriaPanel');
    panel.style.display = panel.style.display === 'none' ? '' : 'none';
}

function saveCriteria() {
    var btn = document.getElementById('btnSaveCriteria');
    btn.disabled = true;

    fetch('{{ route("seu-flagging.criteria.update") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        body: JSON.stringify({
            year: parseInt(selectedYear),
            criteria_type: document.getElementById('criteriaType').value,
            upper_limit: parseFloat(document.getElementById('upperLimit').value) / 100,
            lower_limit: parseFloat(document.getElementById('lowerLimit').value) / 100,
            notes: document.getElementById('criteriaNotes').value || null,
        })
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.status === 'success') {
            window.location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to save criteria'));
        }
    })
    .catch(function(err) { console.error(err); alert('Error saving criteria.'); })
    .finally(function() { btn.disabled = false; });
}

// ===== TOGGLE FLAG =====
function confirmToggleFlag() {
    if (!toggleTargetId) return;
    var btn = document.getElementById('confirmToggle');
    btn.disabled = true;

    fetch('/seu-flagging/' + toggleTargetId + '/toggle-flag', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        body: JSON.stringify({ reason: document.getElementById('toggleReason').value || null })
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.status === 'success') {
            if (toggleModal) toggleModal.hide();
            window.location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to toggle flag'));
        }
    })
    .catch(function(err) { console.error(err); alert('Error toggling flag.'); })
    .finally(function() { btn.disabled = false; });
}

// ===== FILTER MODAL =====
function openSeuFilterModal() {
    new bootstrap.Modal(document.getElementById('seuFilterModal')).show();
}

function applySeuFilter() {
    var modal = bootstrap.Modal.getInstance(document.getElementById('seuFilterModal'));
    if (modal) modal.hide();

    var matrix = document.getElementById('matrixSelect').value;
    if (matrix === 'Table') {
        displaySeuTable();
    } else if (matrix === 'Graph') {
        displaySeuGraph();
    }
}

function resetSeuFilters() {
    document.getElementById('seu_ct_bar').checked = true;
    document.getElementById('seu_m_gj').checked = true;
    document.getElementById('seu_cs_default').checked = true;
    document.getElementById('seu_ff_all').checked = true;
    document.getElementById('seu_show_labels').checked = false;
    document.getElementById('seu_show_legend').checked = true;
    document.getElementById('seu_show_grid').checked = true;
    document.getElementById('seu_show_totals').checked = true;
    document.getElementById('seu_show_energy').checked = true;
    document.getElementById('seu_show_resource').checked = true;
    document.getElementById('seu_show_min').checked = false;
    document.getElementById('seu_show_max').checked = false;
    document.getElementById('seu_show_avg').checked = true;
    document.getElementById('seu_show_threshold').checked = false;

    // Reset Type of Energy checkboxes (check all)
    document.querySelectorAll('.seu-type-check').forEach(function(cb) { cb.checked = true; });
}

// ===== EXPORT =====
function exportSeuChartPNG() {
    var canvas = document.getElementById('seuChartCanvas');
    if (!canvas) return;
    var a = document.createElement('a');
    a.href = canvas.toDataURL('image/png');
    a.download = 'seu-flagging-' + selectedYear + '.png';
    a.click();
}

function exportSeuCSV() {
    var f = getSeuFilterValues();
    var rows = [['Type', 'Name', 'Type of Energy', 'GJ', '% of Usage', 'Flagged', 'Override']];

    if (f.showEnergy) {
        filterByTypeOfEnergy(filterByFlag(energySeus, f.flagFilter), f.selectedTypeOfEnergy).forEach(function(s) {
            rows.push(['Energy', s.seu_name, s.energy_type_name || '-', s.current_gj, (parseFloat(s.overall_usage_pct) * 100).toFixed(2) + '%', s.is_flagged ? 'SEU' : 'Not SEU', s.is_manually_overridden ? 'Manual' : 'Auto']);
        });
    }
    if (f.showResource) {
        filterByTypeOfEnergy(filterByFlag(resourceSeus, f.flagFilter), f.selectedTypeOfEnergy).forEach(function(s) {
            rows.push(['Resource', s.seu_name, s.energy_type_name || '-', s.current_gj, (parseFloat(s.overall_usage_pct) * 100).toFixed(2) + '%', s.is_flagged ? 'SEU' : 'Not SEU', s.is_manually_overridden ? 'Manual' : 'Auto']);
        });
    }

    var csv = rows.map(function(r) { return r.join(','); }).join('\n');
    var blob = new Blob([csv], { type: 'text/csv' });
    var a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = 'seu-flagging-' + selectedYear + '.csv';
    a.click();
}

// ===== HELPERS =====
function formatNum(num) {
    if (!num || num === 0) return '0.00';
    return parseFloat(num).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

// ===== INIT =====
document.addEventListener('DOMContentLoaded', function() {
    // Populate Energy type checkboxes
    var energyContainer = document.getElementById('seuEnergyTypeChecks');
    if (energyContainer && energyTypeNames.length > 0) {
        var html = '';
        energyTypeNames.forEach(function(name, idx) {
            var id = 'seu_etype_' + idx;
            html += '<div class="form-check mb-2">';
            html += '<input class="form-check-input seu-type-check" type="checkbox" id="' + id + '" value="' + escapeHtml(name) + '" checked>';
            html += '<label class="form-check-label" for="' + id + '">' + escapeHtml(name) + '</label>';
            html += '</div>';
        });
        energyContainer.innerHTML = html;
    } else if (energyContainer) {
        energyContainer.innerHTML = '<p class="text-muted small">No energy types available</p>';
    }

    // Populate Energy Resource type checkboxes
    var resourceContainer = document.getElementById('seuResourceTypeChecks');
    if (resourceContainer && resourceTypeNames.length > 0) {
        var html = '';
        resourceTypeNames.forEach(function(name, idx) {
            var id = 'seu_rtype_' + idx;
            html += '<div class="form-check mb-2">';
            html += '<input class="form-check-input seu-type-check" type="checkbox" id="' + id + '" value="' + escapeHtml(name) + '" checked>';
            html += '<label class="form-check-label" for="' + id + '">' + escapeHtml(name) + '</label>';
            html += '</div>';
        });
        resourceContainer.innerHTML = html;
    } else if (resourceContainer) {
        resourceContainer.innerHTML = '<p class="text-muted small">No resource types available</p>';
    }

    var savedMatrix = sessionStorage.getItem('seuMatrixSelect');
    if (savedMatrix) {
        document.getElementById('matrixSelect').value = savedMatrix;
    }
    var matrix = document.getElementById('matrixSelect').value;
    if (matrix) {
        updateSeuContent();
    }
});
</script>
@endsection
