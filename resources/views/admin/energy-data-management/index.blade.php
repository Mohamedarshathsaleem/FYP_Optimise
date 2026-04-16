@extends('layouts.dashboard')

@section('title', 'Energy Data Management')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- ================= FLASH MESSAGES ================= --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Success!</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Error!</strong> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('warning'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <strong>Warning!</strong> {{ session('warning') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- ================= HEADER ================= --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <p class="text-muted small mb-1">Pages / Energy Management</p>
        <h4 class="fw-bold mb-0">
            Energy Data Management
            <i class="bi bi-info-circle text-primary" onclick="toggleEnergyInstructions()" style="cursor:pointer;" title="Show instructions"></i>
        </h4>
    </div>

    <div class="d-flex align-items-center gap-3">
        <div class="input-group">
            <span class="input-group-text bg-white">
                <i class="bi bi-search text-muted"></i>
            </span>
            <input type="text" class="form-control" placeholder="Search...">
        </div>

        <img src="{{ asset('images/user.png') }}"
            class="rounded-circle border"
            width="40" height="40">
    </div>
</div>

{{-- ================= INSTRUCTIONS CARD ================= --}}
<div class="card border-0 shadow-sm mb-4" id="energyInstructionsCard" style="display:none; background:#e3f2fd; border-left:4px solid #2196f3 !important;">
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
                        Use the <strong>Add</strong> button to register an energy source (e.g. Electricity, Gas, Renewable).
                        Click the <strong>calculator</strong> icon <i class="bi bi-calculator"></i> to input monthly energy usage and cost data for each source.
                        Use the <strong>sliders</strong> icon <i class="bi bi-sliders"></i> to configure custom conversion factors per source.
                        Select a date range and click <strong>Summarise</strong> to view aggregated energy totals across all sources.
                    </p>
                </div>
            </div>
            <button class="btn-close" onclick="toggleEnergyInstructions()"></button>
        </div>
    </div>
</div>

{{-- ================= FILTER ================= --}}
<form method="GET" action="{{ route('admin.energy-data-management.summarize') }}" id="filterForm">
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Category</label>
                    <select class="form-select" name="category" id="categorySelect">
                        <option value="All" {{ $category == 'All' ? 'selected' : '' }}>All</option>
                        <option value="Industrial" {{ $category == 'Industrial' ? 'selected' : '' }}>Industrial</option>
                        <option value="Commercial" {{ $category == 'Commercial' ? 'selected' : '' }}>Commercial</option>
                        <option value="Residential" {{ $category == 'Residential' ? 'selected' : '' }}>Residential</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Start Month</label>
                    <input type="month" class="form-control" name="start_month" id="startMonth" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">End Month</label>
                    <input type="month" class="form-control" name="end_month" id="endMonth" required>
                </div>

                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-bar-chart-line me-1"></i> Summarise
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

{{-- ========================================================= --}}
{{-- ======================= ENERGY DATA ===================== --}}
{{-- ========================================================= --}}
<div class="card border-0 shadow-sm mb-5">
    <div class="card-header bg-white d-flex justify-content-between align-items-center px-4 py-3">
        <div>
            <h6 class="fw-bold mb-0">Energy Data</h6>
            <small class="text-muted">Electricity, Gas, Renewable</small>
        </div>
        @if(auth()->user()->hasPermission('energy-data-management.add'))
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#energyDataModal">
            <i class="bi bi-plus-lg"></i> Add
        </button>
        @endif
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th width="50">#</th>
                    <th>Energy Type</th>
                    <th>Provider</th>
                    <th>Account No</th>
                    <th>Contract Type</th>
                    <th width="120" class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($energyData as $index => $data)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $data->energy_type }}</td>
                    <td>{{ $data->provider }}</td>
                    <td>{{ $data->account_no }}</td>
                    <td>{{ $data->contract_type ?? '-' }}</td>
                    <td class="text-center">
                        <div class="btn-group btn-group-sm gap-2">
                            <button class="btn btn-outline-primary" title="Calculator" onclick="openEnergyCalculator({{ $data->id }})">
                                <i class="bi bi-calculator"></i>
                            </button>
                            <button class="btn btn-outline-secondary" title="Conversion Factors" onclick="openEnergyConversionFactors({{ $data->id }}, '{{ addslashes($data->energy_type) }}')">
                                <i class="bi bi-sliders"></i>
                            </button>
                            <button class="btn btn-outline-warning" onclick="editEnergyData({{ $data->id }})">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-outline-danger" onclick="confirmDeleteEnergyData({{ $data->id }})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        No energy data found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ========================================================= --}}
{{-- ======================= RESOURCE DATA =================== --}}
{{-- ========================================================= --}}
<div class="card border-0 shadow-sm mb-5">
    <div class="card-header bg-white d-flex justify-content-between align-items-center px-4 py-3">
        <h6 class="fw-bold mb-0">Energy Resource Data</h6>
        @if(auth()->user()->hasPermission('energy-data-management.add'))
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#energyResourceModal">
            <i class="bi bi-plus-lg"></i> Add
        </button>
        @endif
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th width="50">#</th>
                    <th>Resource Type</th>
                    <th>Provider</th>
                    <th>Account No</th>
                    <th>Contract Type</th>
                    <th width="120" class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($energyResourceData as $index => $data)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $data->resource_type }}</td>
                    <td>{{ $data->provider }}</td>
                    <td>{{ $data->account_no }}</td>
                    <td>{{ $data->contract_type ?? '-' }}</td>
                    <td class="text-center">
                        <div class="btn-group btn-group-sm gap-2">
                            <button class="btn btn-outline-primary" title="Calculator" onclick="openResourceCalculator({{ $data->id }})">
                                <i class="bi bi-calculator"></i>
                            </button>
                            <button class="btn btn-outline-secondary" title="Conversion Factors" onclick="openResourceConversionFactors({{ $data->id }}, '{{ addslashes($data->resource_type) }}')">
                                <i class="bi bi-sliders"></i>
                            </button>
                            <button class="btn btn-outline-warning" onclick="editResourceData({{ $data->id }})">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-outline-danger" onclick="confirmDeleteResourceData({{ $data->id }})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        No resource data available.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ========================================================= --}}
{{-- ==================== MONTHLY PRODUCTION ================ --}}
{{-- ========================================================= --}}
<div class="card border-0 shadow-sm mb-5">
    <div class="card-header bg-white d-flex justify-content-between align-items-center px-4 py-3">
        <h6 class="fw-bold mb-0">Monthly Production</h6>
        @if(auth()->user()->hasPermission('energy-data-management.add'))
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#monthlyProductionModal">
            <i class="bi bi-plus-lg"></i> Add
        </button>
        @endif
    </div>

    <table class="table table-hover mb-0">
        <thead class="table-light">
            <tr>
                <th width="50">#</th>
                <th>Production Type</th>
                <th width="120" class="text-center">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($monthlyProductions as $index => $data)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $data->production_type }}</td>
                <td class="text-center">
                    <div class="btn-group btn-group-sm gap-2">
                        <button class="btn btn-outline-primary" title="Edit Product" onclick="openMonthlyProductionEditor({{ $data->id }})">
                            <i class="bi bi-calculator"></i>
                        </button>
                        <button class="btn btn-outline-warning" onclick="editMonthlyProduction({{ $data->id }})">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-outline-danger" onclick="confirmDeleteMonthlyProduction({{ $data->id }})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="text-center text-muted py-4">
                    No monthly production data.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- ========================================================= --}}
{{-- ==================== MONTHLY VARIABLE ================= --}}
{{-- ========================================================= --}}
<div class="card border-0 shadow-sm mb-5">
    <div class="card-header bg-white d-flex justify-content-between align-items-center px-4 py-3">
        <h6 class="fw-bold mb-0">Monthly Variable</h6>
        @if(auth()->user()->hasPermission('energy-data-management.add'))
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#monthlyVariableModal">
            <i class="bi bi-plus-lg"></i> Add
        </button>
        @endif
    </div>

    <table class="table table-hover mb-0">
        <thead class="table-light">
            <tr>
                <th width="50">#</th>
                <th>Variable Name</th>
                <th width="120" class="text-center">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($monthlyVariables as $index => $data)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $data->variable_name }}</td>
                <td class="text-center">
                    <div class="btn-group btn-group-sm gap-2">
                        <button class="btn btn-outline-primary" title="Edit Variable" onclick="openMonthlyVariableEditor({{ $data->id }})">
                            <i class="bi bi-calculator"></i>
                        </button>
                        <button class="btn btn-outline-warning" onclick="editMonthlyVariable({{ $data->id }})">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-outline-danger" onclick="confirmDeleteMonthlyVariable({{ $data->id }})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="text-center text-muted py-4">
                    No monthly variable data.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- ==================== ADD ENERGY DATA MODAL ==================== --}}
<div class="modal fade" id="energyDataModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark">Add Energy Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4">
                <form method="POST" action="{{ route('admin.energy-data-management.store-data') }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Category <span class="text-danger">*</span></label>
                            <select class="form-select" name="category" required>
                                <option value="">Choose...</option>
                                <option value="Industrial" {{ $category == 'Industrial' ? 'selected' : '' }}>Industrial</option>
                                <option value="Commercial" {{ $category == 'Commercial' ? 'selected' : '' }}>Commercial</option>
                                <option value="Residential" {{ $category == 'Residential' ? 'selected' : '' }}>Residential</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Energy Type <span class="text-danger">*</span></label>
                            <select class="form-select" name="energytype" id="energyTypeSelect" onchange="toggleCustomEnergyType()" required>
                                <option value="">Choose...</option>
                                <option>Electricity</option>
                                <option>Gas</option>
                                <option>Solar</option>
                                <option>Wind</option>
                                <option value="Others">Others</option>
                            </select>
                            <input type="text" class="form-control mt-2 d-none" id="customEnergyType" name="custom_energytype" placeholder="Enter custom energy type">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Provider <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="provider" placeholder="Enter provider name" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Account No <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="accountno" placeholder="Enter account number" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted">Contract Type</label>
                        <input type="text" class="form-control" name="contracttype" placeholder="Optional">
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary px-5 py-2" style="border-radius:8px;min-width:120px;">
                            Add Now
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ==================== EDIT ENERGY DATA MODAL ==================== --}}
<div class="modal fade" id="editEnergyDataModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark">Edit Energy Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4">
                <form method="POST" id="editEnergyDataForm">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Category <span class="text-danger">*</span></label>
                            <select class="form-select" name="category" id="edit_category" required>
                                <option value="">Choose...</option>
                                <option value="Industrial">Industrial</option>
                                <option value="Commercial">Commercial</option>
                                <option value="Residential">Residential</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Energy Type <span class="text-danger">*</span></label>
                            <select class="form-select" name="energytype" id="edit_energytype" onchange="toggleEditCustomEnergyType()" required>
                                <option value="">Choose...</option>
                                <option>Electricity</option>
                                <option>Gas</option>
                                <option>Solar</option>
                                <option>Wind</option>
                                <option value="Others">Others</option>
                            </select>
                            <input type="text" class="form-control mt-2 d-none" id="edit_customEnergyType" name="custom_energytype" placeholder="Enter custom energy type">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Provider <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="provider" id="edit_provider" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Account No <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="accountno" id="edit_accountno" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted">Contract Type</label>
                        <input type="text" class="form-control" name="contracttype" id="edit_contracttype">
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary px-5 py-2" style="border-radius:8px;min-width:120px;">
                            Update Now
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
 
{{-- ==================== ENERGY DATA CALCULATOR MODAL ==================== --}}

<div class="modal fade" id="energyCalculatorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content" style="border-radius:15px;">
            <div class="modal-header border-0 pb-0">
                <div class="w-100">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="modal-title fw-bold text-dark mb-0">Input Energy Data <small id="calc_title" class="text-muted"></small></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold mb-1">Select Year</label>
                            <select class="form-select form-select-sm" id="energyYearSelect" onchange="loadEnergyDataForYear(this.value)">
                                <option value="">Loading years...</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold mb-1">&nbsp;</label>
                            <button type="button" class="btn btn-outline-secondary btn-sm w-100" data-bs-toggle="collapse" data-bs-target="#energyUploadSection">
                                <i class="bi bi-upload"></i> Upload Excel
                            </button>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold mb-1">&nbsp;</label>
                            <button type="button" class="btn btn-outline-success btn-sm w-100" onclick="downloadEnergyDataExcel()">
                                <i class="bi bi-download"></i> Download Excel
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-body p-3">
                <form method="POST" id="energyCalculatorForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="energyFormYear" name="year" value="">
                    <div class="table-responsive" style="max-height: 420px;">
                        <table class="table table-borderless mb-0 align-middle" id="energy-data-table">
                           <thead class="bg-light sticky-top" style="z-index:1;">
                                <tr>
                                    <th style="width:12%;">Month</th>
                                    <th style="width:25%;">Monthly Energy Usage</th>
                                    <th style="width:13%;">
                                        <div class="d-flex align-items-center justify-content-center gap-1">
                                            <small class="fw-semibold">Units</small>
                                            <select id="energyGlobalUnitSelect"
                                                    class="form-select form-select-sm"
                                                    style="width: auto; font-size: 0.75rem; padding: 2px 24px 2px 8px;"
                                                    onchange="updateAllEnergyUnits(this.value)">
                                                <option value="L">L</option>
                                                <option value="kg">kg</option>
                                                <option value="ton">ton</option>
                                                <option value="Gallon">Gallon</option>
                                                <option value="m3">m³</option>
                                                <option value="kWh">kWh</option>
                                                <option value="MWh">MWh</option>
                                            </select>
                                        </div>
                                    </th>
                                    <th style="width:20%;">Monthly Cost (RM)</th>
                                    <th style="width:30%;">Monthly Energy Usage (GJ)</th>
                                </tr>
                            </thead>
                            <tbody style="display:block; max-height:320px; overflow-y:auto;" id="energyTableBody">
                                {{-- Rows will be generated by JavaScript based on selected year --}}
                            </tbody>
                        </table>
                    </div>

                    <div class="collapse my-4" id="energyUploadSection">
                        <div class="border rounded p-4 text-center">
                            <div style="min-height:120px;">
                                <p class="text-muted mb-3">Upload an Excel file (xlsx) to populate data</p>

                                {{-- Download Template Button --}}
                                <div class="mb-3">
                                    <a href="{{ route('admin.energy-data-management.download-template') }}"
                                       class="btn btn-outline-info btn-sm">
                                        <i class="bi bi-download"></i> Download Template
                                    </a>
                                </div>

                                <input type="file" id="energyFileInput" name="upload_file" accept=".xlsx" class="d-none" onchange="handleEnergyFileSelect(this)">
                                <div class="d-flex justify-content-center align-items-center gap-3">
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="document.getElementById('energyFileInput').click()">
                                        <i class="bi bi-upload"></i> Select file
                                    </button>
                                    <span id="energyFileName" class="text-muted"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-3">
                        <button type="submit" class="btn btn-primary px-5 py-2" style="border-radius:8px;min-width:140px;">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
#energy-data-table thead, #energy-data-table tbody tr {
    display: table;
    width: 100%;
    table-layout: fixed;
}
#energy-data-table tbody {
    display: block;
    max-height: 320px;
    overflow-y: auto;
}
#energy-data-table th, #energy-data-table td {
    vertical-align: middle;
    text-align: center;
}
</style>

            {{-- ==================== ADD ENERGY RESOURCE MODAL ==================== --}}
<div class="modal fade" id="energyResourceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius:16px;">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Add Energy Resource Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body px-4 pb-4">
                <form method="POST" action="{{ route('admin.energy-data-management.store-resource') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                            <select name="category" class="form-select" required>
                                <option value="">Choose...</option>
                                <option value="Industrial" {{ $category == 'Industrial' || $category == 'All' ? 'selected' : '' }}>Industrial</option>
                                <option value="Commercial" {{ $category == 'Commercial' ? 'selected' : '' }}>Commercial</option>
                                <option value="Residential" {{ $category == 'Residential' ? 'selected' : '' }}>Residential</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Energy Type <span class="text-danger">*</span></label>
                            <select name="resourcetype" class="form-select" id="resourceTypeSelect" onchange="toggleCustomResourceType()" required>
                                <option value="">Choose...</option>
                                <option>Biodiesel</option>
                                <option>Natural Gas</option>
                                <option>Coal</option>
                                <option>Biomass</option>
                                <option value="Others">Others</option>
                            </select>
                            <input type="text" class="form-control mt-2 d-none" id="customResourceType" name="custom_resourcetype" placeholder="Enter custom resource type">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Provider <span class="text-danger">*</span></label>
                            <input type="text" name="provider" class="form-control" placeholder="e.g. IOI Plantations" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Account No. <span class="text-danger">*</span></label>
                            <input type="text" name="accountno" class="form-control" placeholder="e.g. TL3023" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tariff / Contract Type</label>
                            <input type="text" name="contracttype" class="form-control" placeholder="Optional">
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary px-5 py-2" style="border-radius:10px;min-width:160px;">
                            Add Now
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ==================== EDIT ENERGY RESOURCE MODAL ==================== --}}
<div class="modal fade" id="editEnergyResourceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius:16px;">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Edit Energy Resource Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body px-4 pb-4">
                <form method="POST" id="editEnergyResourceForm">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                            <select name="category" class="form-select" id="edit_resource_category" required>
                                <option value="">Choose...</option>
                                <option value="Industrial">Industrial</option>
                                <option value="Commercial">Commercial</option>
                                <option value="Residential">Residential</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Energy Type <span class="text-danger">*</span></label>
                             <select name="resourcetype" class="form-select" id="edit_resourcetype" onchange="toggleEditCustomResourceType()" required>
                                <option value="">Choose...</option>
                                <option>Biodiesel</option>
                                <option>Natural Gas</option>
                                <option>Coal</option>
                                <option>Biomass</option>
                                <option value="Others">Others</option>
                            </select>
                            <input type="text" class="form-control mt-2 d-none" id="edit_customResourceType" name="custom_resourcetype" placeholder="Enter custom resource type">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Provider <span class="text-danger">*</span></label>
                            <input type="text" name="provider" class="form-control" id="edit_resource_provider" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Account No. <span class="text-danger">*</span></label>
                            <input type="text" name="accountno" class="form-control" id="edit_resource_accountno" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tariff / Contract Type</label>
                            <input type="text" name="contracttype" class="form-control" id="edit_resource_contracttype">
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary px-5 py-2" style="border-radius:10px;min-width:160px;">
                            Update Now
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ==================== ENERGY RESOURCE CALCULATOR MODAL ==================== --}}
<div class="modal fade" id="energyResourceCalculatorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content" style="border-radius:15px;">
            <div class="modal-header border-0 pb-0">
                <div class="w-100">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="modal-title fw-bold text-dark mb-0">Input Resource Data <small id="resource_calc_title" class="text-muted"></small></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold mb-1">Select Year</label>
                            <select class="form-select form-select-sm" id="resourceYearSelect" onchange="loadResourceDataForYear(this.value)">
                                <option value="">Loading years...</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold mb-1">&nbsp;</label>
                            <button type="button" class="btn btn-outline-secondary btn-sm w-100" data-bs-toggle="collapse" data-bs-target="#resourceUploadSection">
                                <i class="bi bi-upload"></i> Upload Excel
                            </button>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold mb-1">&nbsp;</label>
                            <button type="button" class="btn btn-outline-success btn-sm w-100" onclick="downloadResourceDataExcel()">
                                <i class="bi bi-download"></i> Download Excel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-body p-3">
                <form method="POST" id="energyResourceCalculatorForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="resourceFormYear" name="year" value="">
                    <div class="table-responsive" style="max-height: 420px;">
                        <table class="table table-borderless mb-0 align-middle" id="energy-resource-data-table">
                            <thead class="bg-light sticky-top" style="z-index:1;">
                            <tr>
                                <th style="width:15%;">Month</th>
                                <th style="width:25%;">Monthly Resource Usage</th>
                                <th style="width:15%;">
                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                        <small class="fw-semibold">Units</small>
                                        <select id="resourceGlobalUnitSelect"
                                                class="form-select form-select-sm"
                                                style="width: auto; font-size: 0.75rem; padding: 2px 24px 2px 8px;"
                                                onchange="updateAllResourceUnits(this.value)">
                                            <option value="L">L</option>
                                            <option value="kg">kg</option>
                                            <option value="ton">ton</option>
                                            <option value="Gallon">Gallon</option>
                                            <option value="m3">m³</option>
                                            <option value="kWh">kWh</option>
                                            <option value="MWh">MWh</option>
                                        </select>
                                    </div>
                                </th>
                                <th style="width:20%;">Monthly Cost (RM)</th>
                                <th style="width:25%;">Monthly Resource Usage (GJ)</th>
                            </tr>
                        </thead>
                            <tbody style="display:block; max-height:320px; overflow-y:auto;" id="resourceTableBody">
                                {{-- Rows will be generated by JavaScript based on selected year --}}
                            </tbody>
                        </table>
                    </div>

                    <div class="collapse my-4" id="resourceUploadSection">
                        <div class="border rounded p-4 text-center">
                            <div style="min-height:120px;">
                                <p class="text-muted mb-2">Or upload an Excel file (xlsx)</p>

                                {{-- Download Template Button --}}
                                <div class="mb-3">
                                    <a href="{{ route('admin.energy-resource-data.download-template') }}"
                                       class="btn btn-outline-info btn-sm">
                                        <i class="bi bi-download"></i> Download Template
                                    </a>
                                </div>

                                <input type="file" id="resourceFileInput" name="upload_file" accept=".xlsx" class="d-none" onchange="handleResourceFileSelect(this)">
                                <div class="d-flex justify-content-center align-items-center gap-3">
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="document.getElementById('resourceFileInput').click()">
                                        <i class="bi bi-upload"></i> Select file
                                    </button>
                                    <span id="resourceFileName" class="text-muted"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-3">
                        <button type="submit" class="btn btn-primary px-5 py-2" style="border-radius:8px;min-width:140px;">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
#energy-resource-data-table thead, #energy-resource-data-table tbody tr {
    display: table;
    width: 100%;
    table-layout: fixed;
}
#energy-resource-data-table tbody {
    display: block;
    max-height: 320px;
    overflow-y: auto;
}
#energy-resource-data-table th, #energy-resource-data-table td {
    vertical-align: middle;
    text-align: center;
}
</style>

{{-- ==================== ADD MONTHLY PRODUCTION MODAL ==================== --}}
<div class="modal fade" id="monthlyProductionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:15px;">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Add Monthly Production</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4">
                <form method="POST" action="{{ route('admin.energy-data-management.store-production') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                        <select name="category" class="form-select" required>
                            <option value="">Choose...</option>
                            <option value="Industrial" {{ $category == 'Industrial' || $category == 'All' ? 'selected' : '' }}>Industrial</option>
                            <option value="Commercial" {{ $category == 'Commercial' ? 'selected' : '' }}>Commercial</option>
                            <option value="Residential" {{ $category == 'Residential' ? 'selected' : '' }}>Residential</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Production Type <span class="text-danger">*</span></label>
                        <input type="text" name="production_type" class="form-control" placeholder="e.g. Manufacturing Output" required>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary px-5 py-2" style="border-radius:8px;">
                            Add Now
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ==================== EDIT MONTHLY PRODUCTION MODAL ==================== --}}
<div class="modal fade" id="editMonthlyProductionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:15px;">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Edit Monthly Production</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4">
                <form method="POST" id="editMonthlyProductionForm">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                        <select name="category" class="form-select" id="edit_production_category" required>
                            <option value="">Choose...</option>
                            <option value="Industrial">Industrial</option>
                            <option value="Commercial">Commercial</option>
                            <option value="Residential">Residential</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Production Type <span class="text-danger">*</span></label>
                        <input type="text" name="production_type" class="form-control" id="edit_production_type" required>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary px-5 py-2" style="border-radius:8px;">
                            Update Now
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ==================== MONTHLY PRODUCTION CALCULATOR MODAL ==================== --}}
<div class="modal fade" id="monthlyProductionEditorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius:15px;">
            <div class="modal-header border-0 pb-2">
                <div class="w-100">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="modal-title fw-bold text-dark mb-0" style="color: #1e3a8a;">Input Product <small id="production_calc_title" class="text-muted"></small></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold mb-1">Select Year</label>
                            <select class="form-select form-select-sm" id="productionYearSelect" onchange="loadProductionDataForYear(this.value)">
                                <option value="">Loading years...</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold mb-1">&nbsp;</label>
                            <button type="button" class="btn btn-outline-secondary btn-sm w-100" data-bs-toggle="collapse" data-bs-target="#productionUploadSection">
                                <i class="bi bi-upload"></i> Upload Excel
                            </button>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold mb-1">&nbsp;</label>
                            <button type="button" class="btn btn-outline-success btn-sm w-100" onclick="downloadProductionDataExcel()">
                                <i class="bi bi-download"></i> Download Excel
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-body px-4 pb-4">
                <form method="POST" id="monthlyProductionEditorForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="productionFormYear" name="year" value="">

                    {{-- Column Headers Row --}}
                    <div class="row mb-2 px-2">
                        <div class="col-3">
                            <small class="text-muted fw-semibold">Month / Year</small>
                        </div>
                        <div class="col-6">
                            <small class="text-muted fw-semibold">Production Amount</small>
                        </div>
                        <div class="col-3">
                            <div class="d-flex align-items-center gap-1">
                                <small class="text-muted fw-semibold">Units</small>
                                <select id="monthlyProductionUnitSelect"
                                        name="production_unit"
                                        class="form-select form-select-sm"
                                        style="width: auto; font-size: 0.75rem; padding: 2px 24px 2px 8px;"
                                        onchange="updateAllProductionUnits(this.value)">
                                    <option value="Gallon">Gallon</option>
                                    <option value="Ton">Ton</option>
                                    <option value="Kg">Kg</option>
                                    <option value="L">L</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Scrollable Data Rows --}}
                    <div id="productionRowsContainer" style="max-height: 280px; overflow-y: auto; border: 1px solid #e5e7eb; border-radius: 8px; padding: 8px;">
                        {{-- Rows will be generated by JavaScript based on selected year --}}
                    </div>

                    {{-- File Upload Section --}}
                    <div class="collapse mt-4" id="productionUploadSection">
                        <div class="border rounded p-4 text-center" style="background: #fafafa; border: 1px dashed #d1d5db !important; border-radius: 10px;">
                            <div class="py-2">
                                <p class="text-muted mb-2" style="font-size: 0.9rem;">Or upload an Excel file (xlsx)</p>

                                {{-- Download Template Button --}}
                                <div class="mb-3">
                                    <a href="{{ route('admin.monthly-production.download-template') }}"
                                    class="btn btn-outline-info btn-sm">
                                        <i class="bi bi-download"></i> Download Template
                                    </a>
                                </div>

                                <input type="file"
                                    id="monthlyProductionEditorFile"
                                    name="upload_file"
                                    accept=".xlsx,.xls"
                                    class="d-none"
                                    onchange="handleMonthlyProductionFileSelect(this)">

                                <button type="button"
                                        class="btn btn-primary btn-sm px-4 py-2"
                                        style="border-radius: 8px; font-size: 0.9rem;"
                                        onclick="document.getElementById('monthlyProductionEditorFile').click()">
                                    Select file
                                </button>

                                <div class="mt-2">
                                    <small id="monthlyProductionFileName" class="text-success fw-semibold"></small>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Save Button --}}
                    <div class="text-center mt-4">
                        <button type="submit"
                                class="btn btn-primary px-5 py-2 w-100"
                                style="border-radius: 10px; font-size: 1rem; max-width: 400px;">
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
/* Modal specific styles */
#monthlyProductionEditorModal .modal-content {
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
}

/* Scrollbar styling */
#monthlyProductionEditorModal div[style*="overflow-y"]::-webkit-scrollbar {
    width: 6px;
}

#monthlyProductionEditorModal div[style*="overflow-y"]::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

#monthlyProductionEditorModal div[style*="overflow-y"]::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 10px;
}

#monthlyProductionEditorModal div[style*="overflow-y"]::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Input field styling */
#monthlyProductionEditorModal input.form-control:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Select dropdown styling */
#monthlyProductionEditorModal select.form-select:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Button hover effect */
#monthlyProductionEditorModal .btn-primary:hover {
    background-color: #2563eb;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
}

/* Row hover effect for better UX */
#monthlyProductionEditorModal .row.align-items-center:hover {
    background-color: #f8fafc;
    border-radius: 6px;
    transition: background-color 0.2s ease;
}
</style>

{{-- ==================== ADD MONTHLY VARIABLE MODAL ==================== --}}
<div class="modal fade" id="monthlyVariableModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:15px;">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Add Monthly Variable</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4">
                <form method="POST" action="{{ route('admin.energy-data-management.store-variable') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                        <select name="category" class="form-select" required>
                            <option value="">Choose...</option>
                            <option value="Industrial" {{ $category == 'Industrial' || $category == 'All' ? 'selected' : '' }}>Industrial</option>
                            <option value="Commercial" {{ $category == 'Commercial' ? 'selected' : '' }}>Commercial</option>
                            <option value="Residential" {{ $category == 'Residential' ? 'selected' : '' }}>Residential</option>
                        </select>
                    </div>

<div class="mb-3">
    <label class="form-label fw-semibold">Variable Name <span class="text-danger">*</span></label>
    <input type="text" name="variable_name" class="form-control" placeholder="e.g. Temperature" required>
</div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary px-5 py-2" style="border-radius:8px;">
                            Add Now
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ==================== EDIT MONTHLY VARIABLE MODAL ==================== --}}
<div class="modal fade" id="editMonthlyVariableModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:15px;">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Edit Monthly Variable</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4">
                <form method="POST" id="editMonthlyVariableForm">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                        <select name="category" class="form-select" id="edit_variable_category" required>
                            <option value="">Choose...</option>
                            <option value="Industrial">Industrial</option>
                            <option value="Commercial">Commercial</option>
                            <option value="Residential">Residential</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Variable Name <span class="text-danger">*</span></label>
                        <input type="text" name="variable_name" class="form-control" id="edit_variable_name" required>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary px-5 py-2" style="border-radius:8px;">
                            Update Now
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ==================== MONTHLY VARIABLE CALCULATOR MODAL ==================== --}}
<div class="modal fade" id="monthlyVariableCalculatorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius:15px;">
            <div class="modal-header border-0 pb-2">
                <div class="w-100">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="modal-title fw-bold text-dark mb-0">Input Variable <small id="variable_calc_title" class="text-muted"></small></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold mb-1">Select Year</label>
                            <select class="form-select form-select-sm" id="variableYearSelect" onchange="loadVariableDataForYear(this.value)">
                                <option value="">Loading years...</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold mb-1">&nbsp;</label>
                            <button type="button" class="btn btn-outline-secondary btn-sm w-100" data-bs-toggle="collapse" data-bs-target="#variableUploadSection">
                                <i class="bi bi-upload"></i> Upload Excel
                            </button>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold mb-1">&nbsp;</label>
                            <button type="button" class="btn btn-outline-success btn-sm w-100" onclick="downloadVariableDataExcel()">
                                <i class="bi bi-download"></i> Download Excel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-body px-4 pb-4">
                <form method="POST" id="monthlyVariableCalculatorForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="variableFormYear" name="year" value="">

                    {{-- Column Headers Row --}}
                    <div class="row mb-2 px-2">
                        <div class="col-3">
                            <small class="text-muted fw-semibold">Month</small>
                        </div>
                        <div class="col-6">
                            <small class="text-muted fw-semibold">Variable Value</small>
                        </div>
                        <div class="col-3">
                            <div class="d-flex flex-column gap-1">
                                <div class="d-flex align-items-center gap-1">
                                    <small class="text-muted fw-semibold">Units</small>
                                    <select name="variable_unit"
                                            id="monthlyVariableUnitSelect"
                                            class="form-select form-select-sm"
                                            style="width: auto; font-size: 0.75rem; padding: 2px 24px 2px 8px;"
                                            onchange="updateAllVariableUnits(this.value)">
                                        <option value="°C">°C</option>
                                        <option value="°F">°F</option>
                                        <option value="K">K</option>
                                        <option value="%">%</option>
                                        <option value="Pa">Pa</option>
                                        <option value="Others">Others</option>
                                    </select>
                                </div>
                                <input type="text" 
                                    class="form-control form-control-sm d-none" 
                                    id="customVariableUnit"
                                    name="custom_variable_unit"
                                    placeholder="Enter custom unit"
                                    style="font-size: 0.75rem; width: 100%;">
                            </div>
                        </div>
                    </div>

                    {{-- Scrollable Data Rows --}}
                    <div id="variableRowsContainer" style="max-height: 280px; overflow-y: auto; border: 1px solid #e5e7eb; border-radius: 8px; padding: 8px;">
                        {{-- Rows will be generated by JavaScript based on selected year --}}
                    </div>

                    {{-- File Upload Section --}}
                    <div class="collapse mt-4" id="variableUploadSection">
                        <div class="border rounded p-4 text-center" style="background: #fafafa; border: 1px dashed #d1d5db !important; border-radius: 10px;">
                            <div class="py-2">
                                <p class="text-muted mb-2">Or upload an Excel file (xlsx)</p>

                                {{-- Download Template Button --}}
                                <div class="mb-3">
                                    <a href="{{ route('admin.monthly-variable.download-template') }}"
                                       class="btn btn-outline-info btn-sm">
                                        <i class="bi bi-download"></i> Download Template
                                    </a>
                                </div>

                                <input type="file"
                                       id="monthlyVariableCalculatorFile"
                                       name="upload_file"
                                       accept=".xlsx"
                                       class="d-none"
                                       onchange="handleMonthlyVariableFileSelect(this)">

                                <button type="button"
                                        class="btn btn-outline-primary btn-sm px-4 py-2"
                                        style="border-radius: 8px;"
                                        onclick="document.getElementById('monthlyVariableCalculatorFile').click()">
                                    <i class="bi bi-upload"></i> Select file
                                </button>

                                <div class="mt-2">
                                    <small id="monthlyVariableFileName" class="text-success fw-semibold"></small>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Save Button --}}
                    <div class="text-center mt-4">
                        <button type="submit"
                                class="btn btn-primary px-5 py-2 w-100"
                                style="border-radius: 10px; font-size: 1rem; max-width: 400px;">
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ==================== DELETE MODALS ==================== --}}
<div class="modal fade" id="deleteEnergyDataModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Delete Energy Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="deleteEnergyDataForm">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <p class="text-muted mb-0">Are you sure you want to delete this energy data?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteResourceDataModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Delete Resource Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="deleteResourceDataForm">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <p class="text-muted mb-0">Are you sure you want to delete this resource data?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteMonthlyProductionModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Delete Monthly Production</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="deleteMonthlyProductionForm">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <p class="text-muted mb-0">Are you sure you want to delete this monthly production data?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteMonthlyVariableModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Delete Monthly Variable</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="deleteMonthlyVariableForm">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <p class="text-muted mb-0">Are you sure you want to delete this monthly variable data?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ==================== ENERGY DATA CONVERSION FACTORS MODAL ==================== --}}
<div class="modal fade" id="energyConversionFactorsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius:15px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark">
                    <i class="bi bi-sliders me-2"></i>Conversion Factors
                    <small class="text-muted ms-2" id="cf_energy_title"></small>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="table-responsive mb-4">
                    <table class="table table-hover align-middle mb-0" id="energyCFTable">
                        <thead class="table-light">
                            <tr>
                                <th>From Unit</th>
                                <th>To Unit</th>
                                <th>Factor</th>
                                <th>Notes</th>
                                <th class="text-center" width="80">Action</th>
                            </tr>
                        </thead>
                        <tbody id="energyCFTableBody">
                            <tr><td colspan="5" class="text-center text-muted py-3">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="border rounded p-3" style="background:#f8f9fa;">
                    <h6 class="fw-semibold mb-3"><i class="bi bi-plus-circle me-1"></i>Add / Update Conversion Factor</h6>
                    <div class="row g-2 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label small mb-1">From Unit <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm" id="cf_energy_from_unit">
                                @isset($energyUnits)
                                @foreach($energyUnits as $u)
                                <option value="{{ $u->code }}">{{ $u->code }} ({{ $u->name }})</option>
                                @endforeach
                                @endisset
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small mb-1">To Unit</label>
                            <select class="form-select form-select-sm" id="cf_energy_to_unit" disabled>
                                <option value="GJ" selected>GJ</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small mb-1">Factor <span class="text-danger">*</span></label>
                            <input type="number" step="any" class="form-control form-control-sm" id="cf_energy_factor" placeholder="e.g. 0.0036">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small mb-1">Notes</label>
                            <input type="text" class="form-control form-control-sm" id="cf_energy_notes" placeholder="Optional">
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-primary btn-sm w-100" onclick="saveEnergyConversionFactor()">
                                <i class="bi bi-save me-1"></i>Save
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ==================== RESOURCE CONVERSION FACTORS MODAL ==================== --}}
<div class="modal fade" id="resourceConversionFactorsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius:15px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark">
                    <i class="bi bi-sliders me-2"></i>Conversion Factors
                    <small class="text-muted ms-2" id="cf_resource_title"></small>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="table-responsive mb-4">
                    <table class="table table-hover align-middle mb-0" id="resourceCFTable">
                        <thead class="table-light">
                            <tr>
                                <th>From Unit</th>
                                <th>To Unit</th>
                                <th>Factor</th>
                                <th>Notes</th>
                                <th class="text-center" width="80">Action</th>
                            </tr>
                        </thead>
                        <tbody id="resourceCFTableBody">
                            <tr><td colspan="5" class="text-center text-muted py-3">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="border rounded p-3" style="background:#f8f9fa;">
                    <h6 class="fw-semibold mb-3"><i class="bi bi-plus-circle me-1"></i>Add / Update Conversion Factor</h6>
                    <div class="row g-2 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label small mb-1">From Unit <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm" id="cf_resource_from_unit">
                                @isset($energyUnits)
                                @foreach($energyUnits as $u)
                                <option value="{{ $u->code }}">{{ $u->code }} ({{ $u->name }})</option>
                                @endforeach
                                @endisset
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small mb-1">To Unit</label>
                            <select class="form-select form-select-sm" id="cf_resource_to_unit" disabled>
                                <option value="GJ" selected>GJ</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small mb-1">Factor <span class="text-danger">*</span></label>
                            <input type="number" step="any" class="form-control form-control-sm" id="cf_resource_factor" placeholder="e.g. 0.0347">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small mb-1">Notes</label>
                            <input type="text" class="form-control form-control-sm" id="cf_resource_notes" placeholder="Optional">
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-primary btn-sm w-100" onclick="saveResourceConversionFactor()">
                                <i class="bi bi-save me-1"></i>Save
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const csrf = document.querySelector('meta[name="csrf-token"]').content;

// ==================== INSTRUCTIONS TOGGLE ====================
function toggleEnergyInstructions() {
    const card = document.getElementById('energyInstructionsCard');
    card.style.display = card.style.display === 'none' ? 'block' : 'none';
}

// ==================== UTILITY FUNCTIONS ====================

// Dynamic conversion factors (populated when calculator modals open)
var currentEnergyFactors = { 'kWh': 0.0036, 'MWh': 3.6, 'L': 0.0347, 'kg': 0.0464, 'ton': 46.4, 'Gallon': 0.131, 'm3': 0.0378 };
var currentResourceFactors = { 'kWh': 0.0036, 'MWh': 3.6, 'L': 0.0347, 'kg': 0.0464, 'ton': 46.4, 'Gallon': 0.131, 'm3': 0.0378 };

function populateUnitSelect(selectId, units) {
    const select = document.getElementById(selectId);
    if (!select) return;
    select.innerHTML = units.map(u => {
        const label = u === 'm3' ? 'm³' : u;
        return `<option value="${u}">${label}</option>`;
    }).join('');
}

function calculateGJValue(rawUsage, unit) {
    if (!rawUsage) return '';
    const cleaned = String(rawUsage).replace(/,/g, '').trim();
    if (cleaned === '') return '';
    const val = parseFloat(cleaned);
    if (isNaN(val)) return '';
    const multiplier = currentEnergyFactors[unit] || ((unit === 'MWh') ? 3.6 : 0.0036);
    return (val * multiplier).toFixed(3);
}

function calculateResourceGJ(rawUsage, unit) {
    if (!rawUsage) return '';
    const cleaned = String(rawUsage).replace(/,/g, '').trim();
    if (cleaned === '') return '';
    const val = parseFloat(cleaned);
    if (isNaN(val)) return '';
    const multiplier = currentResourceFactors[unit] || 1;
    return (val * multiplier).toFixed(3);
}

// ==================== YEAR SELECTOR HELPER ====================
function populateYearSelector(selectElement, dataYears) {
    selectElement.innerHTML = '';
    const currentYear = new Date().getFullYear();
    const allYears = new Set();

    // Add every year from 1 to the current year
    for (let y = 1; y <= currentYear; y++) {
        allYears.add(y);
    }
    // Include any data years that might exceed current year
    dataYears.forEach(y => allYears.add(y));

    // Sort in DESCENDING order (most recent first)
    const sortedYears = Array.from(allYears).sort((a, b) => b - a);
    
    sortedYears.forEach(year => {
        const option = document.createElement('option');
        option.value = year;
        option.textContent = year;
        selectElement.appendChild(option);
    });

    return sortedYears;
}

// ==================== ENERGY DATA FUNCTIONS ====================
function clearEnergyCalculatorForm() {
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    
    months.forEach(month => {
        const usageInput = document.querySelector(`#energyCalculatorModal .monthly-usage[data-month="${month}"]`);
        const unitSelect = document.querySelector(`#energyCalculatorModal .monthly-unit[data-month="${month}"]`);
        const costInput = document.querySelector(`#energyCalculatorModal .monthly-cost[data-month="${month}"]`);
        const gjInput = document.querySelector(`#energyCalculatorModal .monthly-gj[data-month="${month}"]`);
        
        if (usageInput) usageInput.value = '';
        if (unitSelect) unitSelect.value = 'kWh';
        if (costInput) costInput.value = '';
        if (gjInput) gjInput.value = '';
    });
    
    const fileInput = document.getElementById('energyFileInput');
    const fileNameDisplay = document.getElementById('energyFileName');
    if (fileInput) fileInput.value = '';
    if (fileNameDisplay) fileNameDisplay.textContent = '';
    
    const titleEl = document.getElementById('calc_title');
    if (titleEl) titleEl.textContent = '';
}

let currentEnergyDataId = null;
let energyDataByYear = {};

function openEnergyCalculator(id) {
    currentEnergyDataId = id;
    energyDataByYear = {};
    clearEnergyCalculatorForm();
    document.getElementById('energyCalculatorForm').action = `/admin/energy-data/${id}/calculate`;

    // Fetch conversion factors for this energy source
    fetch(`/admin/energy-data/${id}/conversion-factors`)
        .then(r => r.json())
        .then(cfData => {
            if (cfData.success && cfData.factors && cfData.factors.length > 0) {
                currentEnergyFactors = {};
                cfData.factors.forEach(f => { currentEnergyFactors[f.from_unit] = parseFloat(f.factor); });
                populateUnitSelect('energyGlobalUnitSelect', cfData.factors.map(f => f.from_unit));
            } else {
                currentEnergyFactors = { 'kWh': 0.0036, 'MWh': 3.6, 'L': 0.0347, 'kg': 0.0464, 'ton': 46.4, 'Gallon': 0.131, 'm3': 0.0378 };
                populateUnitSelect('energyGlobalUnitSelect', Object.keys(currentEnergyFactors));
            }
        })
        .catch(() => {
            currentEnergyFactors = { 'kWh': 0.0036, 'MWh': 3.6 };
            populateUnitSelect('energyGlobalUnitSelect', Object.keys(currentEnergyFactors));
        });

    // Fetch energy data details
    fetch(`/admin/energy-data/${id}/edit`)
        .then(res => res.json())
        .then(data => {
            const titleEl = document.getElementById('calc_title');
            if (titleEl) {
                titleEl.textContent = ` ${data.provider || ''} ${data.account_no ? '(' + data.account_no + ')' : ''}`;
            }
        });

    // Fetch all usage data
    fetch(`/admin/energy-data/${id}/usage`)
        .then(res => res.json())
        .then(usageResponse => {
            if (usageResponse.success && usageResponse.data) {
                const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                const years = new Set();

                // Extract available years from the month keys
                Object.keys(usageResponse.data).forEach(monthKey => {
                    const year = parseInt(monthKey.substring(0, 4));
                    years.add(year);

                    if (!energyDataByYear[year]) {
                        energyDataByYear[year] = {};
                    }
                    energyDataByYear[year][monthKey] = usageResponse.data[monthKey];
                });

                // Populate year selector with full range
                const yearSelect = document.getElementById('energyYearSelect');
                const sortedYears = populateYearSelector(yearSelect, years);

                // Load data for first year
                if (sortedYears.length > 0) {
                    yearSelect.value = sortedYears[0];
                    loadEnergyDataForYear(sortedYears[0]);
                }
            } else {
                // No data yet - still show year selector with empty form
                const yearSelect = document.getElementById('energyYearSelect');
                const sortedYears = populateYearSelector(yearSelect, new Set());
                if (sortedYears.length > 0) {
                    yearSelect.value = sortedYears[0];
                    loadEnergyDataForYear(sortedYears[0]);
                }
            }
        })
        .catch(error => console.error('Error:', error))
        .finally(() => {
            new bootstrap.Modal(document.getElementById('energyCalculatorModal')).show();
        });
}

function loadEnergyDataForYear(year) {
    document.getElementById('energyFormYear').value = year;
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    const tbody = document.getElementById('energyTableBody');
    tbody.innerHTML = '';

    // Determine dominant unit from saved data, then sync global select first
    const firstUsageUnit = energyDataByYear[year]
        ? Object.values(energyDataByYear[year])[0]?.usage_unit || 'kWh'
        : 'kWh';
    const globalUnitSelect = document.getElementById('energyGlobalUnitSelect');
    if (globalUnitSelect) globalUnitSelect.value = firstUsageUnit;
    const defaultUnit = firstUsageUnit;

    months.forEach((month, index) => {
        const monthNumber = String(index + 1).padStart(2, '0');
        const monthKey = `${year}-${monthNumber}`;
        const usage = energyDataByYear[year] ? energyDataByYear[year][monthKey] : null;

        const tr = document.createElement('tr');
        tr.style.display = 'table';
        tr.style.width = '100%';
        tr.style.tableLayout = 'fixed';

        const rowUnit = (usage && usage.usage_unit) ? usage.usage_unit : defaultUnit;
        const rowLabel = rowUnit === 'm3' ? 'm³' : rowUnit;

        tr.innerHTML = `
            <td style="width:12%; vertical-align:middle; text-align:center;">${month}</td>
            <td style="width:25%;"><input type="text" name="monthly[${month}][usage]" data-month="${month}" class="form-control form-control-sm monthly-usage" value="${usage ? usage.usage_value : ''}" onchange="updateEnergyGJ(this)"></td>
            <td style="width:13%; text-align:center;">
                <input type="hidden" name="monthly[${month}][unit]" data-month="${month}" class="energy-unit-hidden" value="${rowUnit}">
                <span class="energy-unit-label fw-semibold">${rowLabel}</span>
            </td>
            <td style="width:20%;"><input type="text" name="monthly[${month}][cost]" data-month="${month}" class="form-control form-control-sm monthly-cost" value="${usage && usage.cost ? usage.cost : ''}"></td>
            <td style="width:30%;"><input type="text" name="monthly[${month}][gj]" data-month="${month}" class="form-control form-control-sm monthly-gj" value="${usage ? usage.usage_gj : ''}" readonly></td>
        `;

        tbody.appendChild(tr);
    });
}

function updateEnergyGJ(element) {
    const row = element.closest('tr');
    const usage = row.querySelector('.monthly-usage').value;
    const unit = row.querySelector('.energy-unit-hidden').value;
    const gjInput = row.querySelector('.monthly-gj');

    if (usage && unit) {
        let multiplier = currentEnergyFactors[unit] || ((unit === 'MWh') ? 3.6 : 0.0036);
        let gj = (parseFloat(usage) * multiplier).toFixed(3);
        gjInput.value = gj;
    }
}

function editEnergyData(id) {
    fetch(`/admin/energy-data/${id}/edit`)
        .then(res => res.json())
        .then(data => {
            document.getElementById('edit_category').value = data.category;
            
            // Check if it's a custom type
            const standardTypes = ['Electricity', 'Gas', 'Solar', 'Wind'];
            if (standardTypes.includes(data.energy_type)) {
                document.getElementById('edit_energytype').value = data.energy_type;
                document.getElementById('edit_customEnergyType').classList.add('d-none');
                document.getElementById('edit_customEnergyType').value = '';
            } else {
                document.getElementById('edit_energytype').value = 'Others';
                document.getElementById('edit_customEnergyType').classList.remove('d-none');
                document.getElementById('edit_customEnergyType').value = data.energy_type;
                document.getElementById('edit_customEnergyType').required = true;
            }
            
            document.getElementById('edit_provider').value = data.provider;
            document.getElementById('edit_accountno').value = data.account_no;
            document.getElementById('edit_contracttype').value = data.contract_type || '';
            document.getElementById('editEnergyDataForm').action = `/admin/energy-data/${id}`;
            new bootstrap.Modal(document.getElementById('editEnergyDataModal')).show();
        });
}

function confirmDeleteEnergyData(id) {
    document.getElementById('deleteEnergyDataForm').action = `/admin/energy-data/${id}`;
    new bootstrap.Modal(document.getElementById('deleteEnergyDataModal')).show();
}

function handleEnergyFileSelect(input) {
    const fileNameEl = document.getElementById('energyFileName');
    if (fileNameEl && input.files && input.files.length) {
        fileNameEl.textContent = input.files[0].name;
    }
}

function toggleCustomEnergyType() {
    const select = document.getElementById('energyTypeSelect');
    const customInput = document.getElementById('customEnergyType');
    
    if (select.value === 'Others') {
        customInput.classList.remove('d-none');
        customInput.required = true;
    } else {
        customInput.classList.add('d-none');
        customInput.required = false;
        customInput.value = '';
    }
}

function toggleEditCustomEnergyType() {
    const select = document.getElementById('edit_energytype');
    const customInput = document.getElementById('edit_customEnergyType');
    
    if (select.value === 'Others') {
        customInput.classList.remove('d-none');
        customInput.required = true;
    } else {
        customInput.classList.add('d-none');
        customInput.required = false;
        customInput.value = '';
    }
}

function updateAllEnergyUnits(unit) {
    // Update all hidden inputs
    document.querySelectorAll('.energy-unit-hidden').forEach(input => {
        input.value = unit;
    });

    // Update all visible labels
    const displayUnit = unit === 'm3' ? 'm³' : unit;
    document.querySelectorAll('.energy-unit-label').forEach(label => {
        label.textContent = displayUnit;
    });

    // Recalculate all GJ values with new unit
    document.querySelectorAll('.monthly-usage').forEach(usageInput => {
        if (usageInput.value) {
            updateEnergyGJ(usageInput);
        }
    });
}

// ==================== ENERGY RESOURCE FUNCTIONS ====================
let currentResourceDataId = null;
let resourceDataByYear = {};

function clearResourceCalculatorForm() {
    const tbody = document.getElementById('resourceTableBody');
    if (tbody) tbody.innerHTML = '';

    const fileInput = document.getElementById('resourceFileInput');
    const fileNameDisplay = document.getElementById('resourceFileName');
    if (fileInput) fileInput.value = '';
    if (fileNameDisplay) fileNameDisplay.textContent = '';

    const titleEl = document.getElementById('resource_calc_title');
    if (titleEl) titleEl.textContent = '';
}

function openResourceCalculator(id) {
    currentResourceDataId = id;
    resourceDataByYear = {};
    clearResourceCalculatorForm();
    document.getElementById('energyResourceCalculatorForm').action = `/admin/energy-resource-data/${id}/calculate`;

    // Fetch conversion factors for this resource source
    fetch(`/admin/energy-resource-data/${id}/conversion-factors`)
        .then(r => r.json())
        .then(cfData => {
            if (cfData.success && cfData.factors && cfData.factors.length > 0) {
                currentResourceFactors = {};
                cfData.factors.forEach(f => { currentResourceFactors[f.from_unit] = parseFloat(f.factor); });
                populateUnitSelect('resourceGlobalUnitSelect', cfData.factors.map(f => f.from_unit));
            } else {
                currentResourceFactors = { 'kWh': 0.0036, 'MWh': 3.6, 'L': 0.0347, 'kg': 0.0464, 'ton': 46.4, 'Gallon': 0.131, 'm3': 0.0378 };
                populateUnitSelect('resourceGlobalUnitSelect', Object.keys(currentResourceFactors));
            }
        })
        .catch(() => {
            currentResourceFactors = { 'kWh': 0.0036, 'MWh': 3.6, 'L': 0.0347, 'kg': 0.0464, 'ton': 46.4, 'Gallon': 0.131, 'm3': 0.0378 };
            populateUnitSelect('resourceGlobalUnitSelect', Object.keys(currentResourceFactors));
        });

    // Fetch resource data details
    fetch(`/admin/energy-resource-data/${id}/edit`)
        .then(res => res.json())
        .then(data => {
            const titleEl = document.getElementById('resource_calc_title');
            if (titleEl) {
                titleEl.textContent = ` ${data.provider || ''} ${data.account_no ? '(' + data.account_no + ')' : ''}`;
            }
        });

    // Fetch all usage data
    fetch(`/admin/energy-resource-data/${id}/usage`)
        .then(res => res.json())
        .then(usageResponse => {
            if (usageResponse.success && usageResponse.data) {
                const years = new Set();

                Object.keys(usageResponse.data).forEach(monthKey => {
                    const year = parseInt(monthKey.substring(0, 4));
                    years.add(year);

                    if (!resourceDataByYear[year]) {
                        resourceDataByYear[year] = {};
                    }
                    resourceDataByYear[year][monthKey] = usageResponse.data[monthKey];
                });

                // Populate year selector with full range
                const yearSelect = document.getElementById('resourceYearSelect');
                const sortedYears = populateYearSelector(yearSelect, years);

                if (sortedYears.length > 0) {
                    yearSelect.value = sortedYears[0];
                    loadResourceDataForYear(sortedYears[0]);
                }
            } else {
                const yearSelect = document.getElementById('resourceYearSelect');
                const sortedYears = populateYearSelector(yearSelect, new Set());
                if (sortedYears.length > 0) {
                    yearSelect.value = sortedYears[0];
                    loadResourceDataForYear(sortedYears[0]);
                }
            }
        })
        .catch(error => console.error('Error:', error))
        .finally(() => {
            new bootstrap.Modal(document.getElementById('energyResourceCalculatorModal')).show();
        });
}

function loadResourceDataForYear(year) {
    document.getElementById('resourceFormYear').value = year;
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    const tbody = document.getElementById('resourceTableBody');
    tbody.innerHTML = '';

    // Sync global select first so empty rows default to the correct unit
    const firstUsageUnit = resourceDataByYear[year]
        ? Object.values(resourceDataByYear[year])[0]?.usage_unit || 'L'
        : 'L';
    const globalUnitSelect = document.getElementById('resourceGlobalUnitSelect');
    if (globalUnitSelect) globalUnitSelect.value = firstUsageUnit;
    const defaultUnit = firstUsageUnit;

    months.forEach((month, index) => {
        const monthNumber = String(index + 1).padStart(2, '0');
        const monthKey = `${year}-${monthNumber}`;
        const usage = resourceDataByYear[year] ? resourceDataByYear[year][monthKey] : null;

        const tr = document.createElement('tr');
        tr.style.display = 'table';
        tr.style.width = '100%';
        tr.style.tableLayout = 'fixed';

        const rowUnit = (usage && usage.usage_unit) ? usage.usage_unit : defaultUnit;
        const rowLabel = rowUnit === 'm3' ? 'm³' : rowUnit;

        tr.innerHTML = `
            <td style="width:15%;">${month}</td>
            <td style="width:25%;"><input type="text" name="monthly[${month}][usage]" data-month="${month}" class="form-control form-control-sm resource-monthly-usage" value="${usage ? usage.usage_value : ''}" onchange="updateResourceGJ(this)"></td>
            <td style="width:15%; text-align:center;">
                <input type="hidden" name="monthly[${month}][unit]" data-month="${month}" class="resource-unit-hidden" value="${rowUnit}">
                <span class="resource-unit-label fw-semibold">${rowLabel}</span>
            </td>
            <td style="width:20%;"><input type="text" name="monthly[${month}][cost]" data-month="${month}" class="form-control form-control-sm resource-monthly-cost" value="${usage && usage.cost ? usage.cost : ''}"></td>
            <td style="width:25%;"><input type="text" name="monthly[${month}][gj]" data-month="${month}" class="form-control form-control-sm resource-monthly-gj" value="${usage ? usage.usage_gj : ''}" readonly></td>
        `;

        tbody.appendChild(tr);
    });
}

function updateResourceGJ(element) {
    const row = element.closest('tr');
    const usage = row.querySelector('.resource-monthly-usage').value;
    const unit = row.querySelector('.resource-unit-hidden').value;  
    const gjInput = row.querySelector('.resource-monthly-gj');

    if (usage && unit) {
        gjInput.value = calculateResourceGJ(usage, unit);
    }
}

function editResourceData(id) {
    fetch(`/admin/energy-resource-data/${id}/edit`)
        .then(res => res.json())
        .then(data => {
            document.getElementById('edit_resource_category').value = data.category;  
            
            // Check if it's a custom type
            const standardTypes = ['Biodiesel', 'Natural Gas', 'Coal', 'Biomass'];
            if (standardTypes.includes(data.resource_type)) {
                document.getElementById('edit_resourcetype').value = data.resource_type;
                document.getElementById('edit_customResourceType').classList.add('d-none');
                document.getElementById('edit_customResourceType').value = '';
            } else {
                document.getElementById('edit_resourcetype').value = 'Others';
                document.getElementById('edit_customResourceType').classList.remove('d-none');
                document.getElementById('edit_customResourceType').value = data.resource_type;
                document.getElementById('edit_customResourceType').required = true;
            }
            
            document.getElementById('edit_resource_provider').value = data.provider;
            document.getElementById('edit_resource_accountno').value = data.account_no;
            document.getElementById('edit_resource_contracttype').value = data.contract_type || '';
            document.getElementById('editEnergyResourceForm').action = `/admin/energy-resource-data/${id}`;
            new bootstrap.Modal(document.getElementById('editEnergyResourceModal')).show();
        });
}

function confirmDeleteResourceData(id) {
    document.getElementById('deleteResourceDataForm').action = `/admin/energy-resource-data/${id}`;
    new bootstrap.Modal(document.getElementById('deleteResourceDataModal')).show();
}

function handleResourceFileSelect(input) {
    const fileNameEl = document.getElementById('resourceFileName');
    if (fileNameEl && input.files && input.files.length) {
        fileNameEl.textContent = input.files[0].name;
    }
}

function toggleCustomResourceType() {
    const select = document.getElementById('resourceTypeSelect');
    const customInput = document.getElementById('customResourceType');
    
    if (select.value === 'Others') {
        customInput.classList.remove('d-none');
        customInput.required = true;
    } else {
        customInput.classList.add('d-none');
        customInput.required = false;
        customInput.value = '';
    }
}

function toggleEditCustomResourceType() {
    const select = document.getElementById('edit_resourcetype');
    const customInput = document.getElementById('edit_customResourceType');
    
    if (select.value === 'Others') {
        customInput.classList.remove('d-none');
        customInput.required = true;
    } else {
        customInput.classList.add('d-none');
        customInput.required = false;
        customInput.value = '';
    }
}

function updateAllResourceUnits(unit) {
    // Update all hidden inputs
    document.querySelectorAll('.resource-unit-hidden').forEach(input => {
        input.value = unit;
    });
    
    // Update all visible labels (handle m3 special case)
    const displayUnit = unit === 'm3' ? 'm³' : unit;
    document.querySelectorAll('.resource-unit-label').forEach(label => {
        label.textContent = displayUnit;
    });
    
    // Recalculate all GJ values with new unit
    document.querySelectorAll('.resource-monthly-usage').forEach(usageInput => {
        if (usageInput.value) {
            updateResourceGJ(usageInput);
        }
    });
}

// ==================== MONTHLY PRODUCTION FUNCTIONS ====================
function editMonthlyProduction(id) {
    fetch(`/admin/monthly-production/${id}/edit`)
        .then(res => res.json())
        .then(data => {
            document.getElementById('edit_production_category').value = data.category;  // ADD THIS
            document.getElementById('edit_production_type').value = data.production_type;
            document.getElementById('editMonthlyProductionForm').action = `/admin/monthly-production/${id}`;
            new bootstrap.Modal(document.getElementById('editMonthlyProductionModal')).show();
        });
}

// ==================== MONTHLY PRODUCTION FUNCTIONS (UPDATED) ====================
let currentProductionId = null;
let productionDataByYear = {};

function clearProductionEditorForm() {
    const container = document.getElementById('productionRowsContainer');
    if (container) container.innerHTML = '';

    const unitSelect = document.getElementById('monthlyProductionUnitSelect');
    if (unitSelect) unitSelect.value = 'Gallon';
    updateAllProductionUnits('Gallon');

    const fileInput = document.getElementById('monthlyProductionEditorFile');
    const fileNameDisplay = document.getElementById('monthlyProductionFileName');
    if (fileInput) fileInput.value = '';
    if (fileNameDisplay) fileNameDisplay.textContent = '';

    const titleEl = document.getElementById('production_calc_title');
    if (titleEl) titleEl.textContent = '';
}

function openMonthlyProductionEditor(id) {
    currentProductionId = id;
    productionDataByYear = {};
    clearProductionEditorForm();
    document.getElementById('monthlyProductionEditorForm').action = `/admin/monthly-production/${id}/calculate`;

    // Fetch production data details
    fetch(`/admin/monthly-production/${id}/edit`)
        .then(res => res.json())
        .then(data => {
            const titleEl = document.getElementById('production_calc_title');
            if (titleEl) {
                titleEl.textContent = ` - ${data.production_type || ''}`;
            }
        });

    // Fetch all usage data
    fetch(`/admin/monthly-production/${id}/usage`)
        .then(res => res.json())
        .then(usageResponse => {
            if (usageResponse.success && usageResponse.data) {
                const years = new Set();

                Object.keys(usageResponse.data).forEach(monthKey => {
                    const year = parseInt(monthKey.substring(0, 4));
                    years.add(year);

                    if (!productionDataByYear[year]) {
                        productionDataByYear[year] = {};
                    }
                    productionDataByYear[year][monthKey] = usageResponse.data[monthKey];
                });

                // Populate year selector with full range
                const yearSelect = document.getElementById('productionYearSelect');
                const sortedYears = populateYearSelector(yearSelect, years);

                if (sortedYears.length > 0) {
                    yearSelect.value = sortedYears[0];
                    loadProductionDataForYear(sortedYears[0]);
                }
            } else {
                const yearSelect = document.getElementById('productionYearSelect');
                const sortedYears = populateYearSelector(yearSelect, new Set());
                if (sortedYears.length > 0) {
                    yearSelect.value = sortedYears[0];
                    loadProductionDataForYear(sortedYears[0]);
                }
            }
        })
        .catch(error => console.error('Error:', error))
        .finally(() => {
            new bootstrap.Modal(document.getElementById('monthlyProductionEditorModal')).show();
        });
}

function loadProductionDataForYear(year) {
    document.getElementById('productionFormYear').value = year;
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    const container = document.getElementById('productionRowsContainer');
    container.innerHTML = '';

    let commonUnit = 'Gallon';

    months.forEach((month, index) => {
        const monthNumber = String(index + 1).padStart(2, '0');
        const monthKey = `${year}-${monthNumber}`;
        const usage = productionDataByYear[year] ? productionDataByYear[year][monthKey] : null;

        if (usage && usage.production_unit) {
            commonUnit = usage.production_unit;
        }

        const row = document.createElement('div');
        row.className = 'row align-items-center mb-2 px-2';
        row.innerHTML = `
            <div class="col-3">
                <span class="text-dark fw-semibold" style="font-size: 0.9rem;">${month}</span>
            </div>
            <div class="col-6">
                <input type="text"
                       name="production[${month}][amount]"
                       data-month="${month}"
                       class="form-control form-control-sm production-amount-input"
                       placeholder="20"
                       value="${usage ? usage.production_amount : ''}"
                       style="border: 1px solid #e5e7eb; border-radius: 6px;">
            </div>
            <div class="col-3">
                <span class="monthly-production-unit-label text-dark fw-semibold" style="font-size: 0.9rem;">Gallon</span>
            </div>
        `;
        container.appendChild(row);
    });

    const unitSelect = document.getElementById('monthlyProductionUnitSelect');
    if (unitSelect) unitSelect.value = commonUnit;
    updateAllProductionUnits(commonUnit);
}

function confirmDeleteMonthlyProduction(id) {
    document.getElementById('deleteMonthlyProductionForm').action = `/admin/monthly-production/${id}`;
    new bootstrap.Modal(document.getElementById('deleteMonthlyProductionModal')).show();
}

function updateAllProductionUnits(unit) {
    document.querySelectorAll('.monthly-production-unit-label').forEach(label => {
        label.textContent = unit;
    });
}

function handleMonthlyProductionFileSelect(input) {
    const fileNameEl = document.getElementById('monthlyProductionFileName');
    if (fileNameEl && input.files && input.files.length) {
        fileNameEl.textContent = input.files[0].name;
    }
}

// ==================== MONTHLY VARIABLE FUNCTIONS ====================
function editMonthlyVariable(id) {
    fetch(`/admin/monthly-variable/${id}/edit`)
        .then(res => res.json())
        .then(data => {
            document.getElementById('edit_variable_category').value = data.category;  
            document.getElementById('edit_variable_name').value = data.variable_name;
            document.getElementById('editMonthlyVariableForm').action = `/admin/monthly-variable/${id}`;
            new bootstrap.Modal(document.getElementById('editMonthlyVariableModal')).show();
        });
}
let currentVariableId = null;
let variableDataByYear = {};

function clearVariableCalculatorForm() {
    const container = document.getElementById('variableRowsContainer');
    if (container) container.innerHTML = '';

    const unitSelect = document.getElementById('monthlyVariableUnitSelect');
    if (unitSelect) unitSelect.value = '°C';
    updateAllVariableUnits('°C');

    const fileInput = document.getElementById('monthlyVariableCalculatorFile');
    const fileNameDisplay = document.getElementById('monthlyVariableFileName');
    if (fileInput) fileInput.value = '';
    if (fileNameDisplay) fileNameDisplay.textContent = '';

    const titleEl = document.getElementById('variable_calc_title');
    if (titleEl) titleEl.textContent = '';
}

function openMonthlyVariableEditor(id) {
    currentVariableId = id;
    variableDataByYear = {};
    clearVariableCalculatorForm();
    document.getElementById('monthlyVariableCalculatorForm').action = `/admin/monthly-variable/${id}/calculate`;

    // Fetch variable data details
    fetch(`/admin/monthly-variable/${id}/edit`)
        .then(res => res.json())
        .then(data => {
            const titleEl = document.getElementById('variable_calc_title');
            if (titleEl) {
                titleEl.textContent = ` - ${data.variable_name || ''}`;
            }
        });

    // Fetch all usage data
    fetch(`/admin/monthly-variable/${id}/usage`)
        .then(res => res.json())
        .then(usageResponse => {
            if (usageResponse.success && usageResponse.data) {
                const years = new Set();

                Object.keys(usageResponse.data).forEach(monthKey => {
                    const year = parseInt(monthKey.substring(0, 4));
                    years.add(year);

                    if (!variableDataByYear[year]) {
                        variableDataByYear[year] = {};
                    }
                    variableDataByYear[year][monthKey] = usageResponse.data[monthKey];
                });

                // Populate year selector with full range
                const yearSelect = document.getElementById('variableYearSelect');
                const sortedYears = populateYearSelector(yearSelect, years);

                if (sortedYears.length > 0) {
                    yearSelect.value = sortedYears[0];
                    loadVariableDataForYear(sortedYears[0]);
                }
            } else {
                const yearSelect = document.getElementById('variableYearSelect');
                const sortedYears = populateYearSelector(yearSelect, new Set());
                if (sortedYears.length > 0) {
                    yearSelect.value = sortedYears[0];
                    loadVariableDataForYear(sortedYears[0]);
                }
            }
        })
        .catch(error => console.error('Error:', error))
        .finally(() => {
            new bootstrap.Modal(document.getElementById('monthlyVariableCalculatorModal')).show();
        });
}

function loadVariableDataForYear(year) {
    document.getElementById('variableFormYear').value = year;
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    const container = document.getElementById('variableRowsContainer');
    container.innerHTML = '';

    let commonUnit = '°C';
    const standardUnits = ['°C', '°F', 'K', '%', 'Pa'];

    months.forEach((month, index) => {
        const monthNumber = String(index + 1).padStart(2, '0');
        const monthKey = `${year}-${monthNumber}`;
        const usage = variableDataByYear[year] ? variableDataByYear[year][monthKey] : null;

        if (usage && usage.variable_unit) {
            commonUnit = usage.variable_unit;
        }

        const row = document.createElement('div');
        row.className = 'row align-items-center mb-2 px-2';
        row.innerHTML = `
            <div class="col-3">
                <span class="text-dark fw-semibold" style="font-size: 0.9rem;">${month}</span>
            </div>
            <div class="col-6">
                <input type="text"
                       name="variable[${month}][value]"
                       data-month="${month}"
                       class="form-control form-control-sm variable-value-input"
                       placeholder="25"
                       value="${usage ? usage.variable_value : ''}"
                       style="border: 1px solid #e5e7eb; border-radius: 6px;">
            </div>
            <div class="col-3">
                <span class="monthly-variable-unit-label text-dark fw-semibold" style="font-size: 0.9rem;">°C</span>
            </div>
        `;
        container.appendChild(row);
    });

    const unitSelect = document.getElementById('monthlyVariableUnitSelect');
    const customUnitInput = document.getElementById('customVariableUnit');
    
    // Check if it's a standard unit or custom
    if (standardUnits.includes(commonUnit)) {
        unitSelect.value = commonUnit;
        customUnitInput.classList.add('d-none');
        customUnitInput.value = '';
    } else {
        // It's a custom unit
        unitSelect.value = 'Others';
        customUnitInput.classList.remove('d-none');
        customUnitInput.value = commonUnit;
    }
    
    updateAllVariableUnits(unitSelect.value);
}

function updateAllVariableUnits(unit) {
    const customUnitInput = document.getElementById('customVariableUnit');
    
    if (unit === 'Others') {
        // Show custom input field
        customUnitInput.classList.remove('d-none');
        customUnitInput.required = true;
        customUnitInput.focus(); 
    } else {
        // Hide custom input field
        customUnitInput.classList.add('d-none');
        customUnitInput.required = false;
        customUnitInput.value = '';
        
        // Update all unit labels with selected standard unit
        document.querySelectorAll('.monthly-variable-unit-label').forEach(label => {
            label.textContent = unit;
        });
    }
}

function handleMonthlyVariableFileSelect(input) {
    const fileNameEl = document.getElementById('monthlyVariableFileName');
    if (fileNameEl && input.files && input.files.length) {
        fileNameEl.textContent = input.files[0].name;
    }
}

function confirmDeleteMonthlyVariable(id) {
    document.getElementById('deleteMonthlyVariableForm').action = `/admin/monthly-variable/${id}`;
    new bootstrap.Modal(document.getElementById('deleteMonthlyVariableModal')).show();
}

// ==================== EVENT LISTENERS ====================
function setupEnergyCalculatorListeners() {
    // Use event delegation since rows are dynamically generated
    const energyTable = document.getElementById('energyTableBody');
    if (energyTable) {
        energyTable.addEventListener('input', (e) => {
            if (e.target.classList.contains('monthly-usage')) {
                updateEnergyGJ(e.target);
            }
        });
    }
}

function setupResourceCalculatorListeners() {
    // Use event delegation since rows are dynamically generated
    const resourceTable = document.getElementById('resourceTableBody');
    if (resourceTable) {
        resourceTable.addEventListener('input', (e) => {
            if (e.target.classList.contains('resource-monthly-usage')) {
                updateResourceGJ(e.target);
            }
        });
        resourceTable.addEventListener('change', (e) => {
            if (e.target.classList.contains('resource-monthly-unit')) {
                updateResourceGJ(e.target);
            }
        });
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    setupEnergyCalculatorListeners();
    setupResourceCalculatorListeners();
});

document.addEventListener('DOMContentLoaded', () => {
    // Category filter - reload page when category changes
    const categorySelect = document.getElementById('categorySelect');
    if (categorySelect) {
        categorySelect.addEventListener('change', function() {
            window.location.href = '{{ route("admin.energy-data-management.index") }}?category=' + this.value;
        });
    }
});

// Listen for custom unit input changes
document.addEventListener('DOMContentLoaded', function() {
    const customVariableUnitInput = document.getElementById('customVariableUnit');
    if (customVariableUnitInput) {
        customVariableUnitInput.addEventListener('input', function() {
            const customUnit = this.value.trim();
            if (customUnit) {
                // Update all unit labels with custom unit
                document.querySelectorAll('.monthly-variable-unit-label').forEach(label => {
                    label.textContent = customUnit;
                });
            }
        });
    }
});

// ==================== FORM SUBMIT HANDLERS ====================

// Energy Data Form Submit Handler
document.getElementById('energyCalculatorForm').addEventListener('submit', function(e) {
    const yearSelect = document.getElementById('energyYearSelect');
    const yearInput = document.getElementById('energyFormYear');
    
    console.log('Energy form submitting...');
    console.log('Year from select:', yearSelect.value);
    console.log('Year from hidden input:', yearInput.value);
    
    // Make sure year is set
    if (!yearInput.value && yearSelect.value) {
        yearInput.value = yearSelect.value;
        console.log('Set year to:', yearInput.value);
    }
    
    // Check if file is uploaded
    const fileInput = document.getElementById('energyFileInput');
    if (fileInput.files.length > 0) {
        console.log('File uploaded:', fileInput.files[0].name);
    }
    
    // Verify year is set
    if (!yearInput.value) {
        e.preventDefault();
        alert('Please select a year before saving.');
        return false;
    }
});

// Energy Resource Form Submit Handler
document.getElementById('energyResourceCalculatorForm').addEventListener('submit', function(e) {
    const yearSelect = document.getElementById('resourceYearSelect');
    const yearInput = document.getElementById('resourceFormYear');
    
    console.log('Resource form submitting...');
    console.log('Year from select:', yearSelect.value);
    console.log('Year from hidden input:', yearInput.value);
    
    if (!yearInput.value && yearSelect.value) {
        yearInput.value = yearSelect.value;
        console.log('Set year to:', yearInput.value);
    }
    
    const fileInput = document.getElementById('resourceFileInput');
    if (fileInput.files.length > 0) {
        console.log('File uploaded:', fileInput.files[0].name);
    }
    
    if (!yearInput.value) {
        e.preventDefault();
        alert('Please select a year before saving.');
        return false;
    }
});

// Monthly Production Form Submit Handler
document.getElementById('monthlyProductionEditorForm').addEventListener('submit', function(e) {
    const yearSelect = document.getElementById('productionYearSelect');
    const yearInput = document.getElementById('productionFormYear');
    
    console.log('Production form submitting...');
    console.log('Year from select:', yearSelect.value);
    console.log('Year from hidden input:', yearInput.value);
    
    if (!yearInput.value && yearSelect.value) {
        yearInput.value = yearSelect.value;
        console.log('Set year to:', yearInput.value);
    }
    
    const fileInput = document.getElementById('monthlyProductionEditorFile');
    if (fileInput.files.length > 0) {
        console.log('File uploaded:', fileInput.files[0].name);
    }
    
    if (!yearInput.value) {
        e.preventDefault();
        alert('Please select a year before saving.');
        return false;
    }
});

// Monthly Variable Form Submit Handler
document.getElementById('monthlyVariableCalculatorForm').addEventListener('submit', function(e) {
    const yearSelect = document.getElementById('variableYearSelect');
    const yearInput = document.getElementById('variableFormYear');
    
    console.log('Variable form submitting...');
    console.log('Year from select:', yearSelect.value);
    console.log('Year from hidden input:', yearInput.value);
    
    if (!yearInput.value && yearSelect.value) {
        yearInput.value = yearSelect.value;
        console.log('Set year to:', yearInput.value);
    }
    
    const fileInput = document.getElementById('monthlyVariableCalculatorFile');
    if (fileInput.files.length > 0) {
        console.log('File uploaded:', fileInput.files[0].name);
    }
    
    if (!yearInput.value) {
        e.preventDefault();
        alert('Please select a year before saving.');
        return false;
    }
});

// ==================== ENERGY DATA CONVERSION FACTOR FUNCTIONS ====================
var currentCFEnergyDataId = null;

function openEnergyConversionFactors(id, title) {
    currentCFEnergyDataId = id;
    document.getElementById('cf_energy_title').textContent = title ? '— ' + title : '';
    document.getElementById('energyCFTableBody').innerHTML = '<tr><td colspan="5" class="text-center text-muted py-3">Loading...</td></tr>';
    document.getElementById('cf_energy_factor').value = '';
    document.getElementById('cf_energy_notes').value = '';

    fetch(`/admin/energy-data/${id}/conversion-factors`)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                renderCFTable('energyCFTableBody', data.factors, 'energy');
            }
        })
        .catch(() => {
            document.getElementById('energyCFTableBody').innerHTML = '<tr><td colspan="5" class="text-center text-danger">Failed to load.</td></tr>';
        });

    bootstrap.Modal.getOrCreateInstance(document.getElementById('energyConversionFactorsModal')).show();
}

function refreshEnergyCFTable() {
    fetch(`/admin/energy-data/${currentCFEnergyDataId}/conversion-factors`)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                renderCFTable('energyCFTableBody', data.factors, 'energy');
            }
        })
        .catch(() => {
            document.getElementById('energyCFTableBody').innerHTML = '<tr><td colspan="5" class="text-center text-danger">Failed to load.</td></tr>';
        });
}

function saveEnergyConversionFactor() {
    const fromUnit = document.getElementById('cf_energy_from_unit').value;
    const factor = document.getElementById('cf_energy_factor').value;
    const notes = document.getElementById('cf_energy_notes').value;

    if (!fromUnit || !factor) {
        alert('From Unit and Factor are required.');
        return;
    }

    fetch(`/admin/energy-data/${currentCFEnergyDataId}/conversion-factors`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
        body: JSON.stringify({ from_unit: fromUnit, to_unit: 'GJ', factor: parseFloat(factor), notes: notes })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            document.getElementById('cf_energy_factor').value = '';
            document.getElementById('cf_energy_notes').value = '';
            refreshEnergyCFTable();
        } else {
            alert(data.message || 'Failed to save.');
        }
    })
    .catch(() => alert('Network error.'));
}

function deleteEnergyCF(factorId) {
    if (!confirm('Delete this conversion factor?')) return;
    fetch(`/admin/energy-data/${currentCFEnergyDataId}/conversion-factors/${factorId}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            refreshEnergyCFTable();
        }
    })
    .catch(() => alert('Network error.'));
}

// ==================== RESOURCE CONVERSION FACTOR FUNCTIONS ====================
var currentCFResourceDataId = null;

function openResourceConversionFactors(id, title) {
    currentCFResourceDataId = id;
    document.getElementById('cf_resource_title').textContent = title ? '— ' + title : '';
    document.getElementById('resourceCFTableBody').innerHTML = '<tr><td colspan="5" class="text-center text-muted py-3">Loading...</td></tr>';
    document.getElementById('cf_resource_factor').value = '';
    document.getElementById('cf_resource_notes').value = '';

    fetch(`/admin/energy-resource-data/${id}/conversion-factors`)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                renderCFTable('resourceCFTableBody', data.factors, 'resource');
            }
        })
        .catch(() => {
            document.getElementById('resourceCFTableBody').innerHTML = '<tr><td colspan="5" class="text-center text-danger">Failed to load.</td></tr>';
        });

    bootstrap.Modal.getOrCreateInstance(document.getElementById('resourceConversionFactorsModal')).show();
}

function refreshResourceCFTable() {
    fetch(`/admin/energy-resource-data/${currentCFResourceDataId}/conversion-factors`)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                renderCFTable('resourceCFTableBody', data.factors, 'resource');
            }
        })
        .catch(() => {
            document.getElementById('resourceCFTableBody').innerHTML = '<tr><td colspan="5" class="text-center text-danger">Failed to load.</td></tr>';
        });
}

function saveResourceConversionFactor() {
    const fromUnit = document.getElementById('cf_resource_from_unit').value;
    const factor = document.getElementById('cf_resource_factor').value;
    const notes = document.getElementById('cf_resource_notes').value;

    if (!fromUnit || !factor) {
        alert('From Unit and Factor are required.');
        return;
    }

    fetch(`/admin/energy-resource-data/${currentCFResourceDataId}/conversion-factors`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
        body: JSON.stringify({ from_unit: fromUnit, to_unit: 'GJ', factor: parseFloat(factor), notes: notes })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            document.getElementById('cf_resource_factor').value = '';
            document.getElementById('cf_resource_notes').value = '';
            refreshResourceCFTable();
        } else {
            alert(data.message || 'Failed to save.');
        }
    })
    .catch(() => alert('Network error.'));
}

function deleteResourceCF(factorId) {
    if (!confirm('Delete this conversion factor?')) return;
    fetch(`/admin/energy-resource-data/${currentCFResourceDataId}/conversion-factors/${factorId}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            refreshResourceCFTable();
        }
    })
    .catch(() => alert('Network error.'));
}

// ==================== SHARED CF TABLE RENDERER ====================
function renderCFTable(tbodyId, factors, type) {
    const tbody = document.getElementById(tbodyId);
    if (!factors || factors.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted small py-3">No conversion factors defined. Default values will be used.</td></tr>';
        return;
    }
    tbody.innerHTML = factors.map(f => `
        <tr>
            <td><strong>${f.from_unit}</strong></td>
            <td>${f.to_unit}</td>
            <td>${parseFloat(f.factor)}</td>
            <td class="text-muted small">${f.notes || '-'}</td>
            <td class="text-center">
                <button class="btn btn-outline-danger btn-sm" onclick="delete${type === 'energy' ? 'Energy' : 'Resource'}CF(${f.id})" title="Delete">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        </tr>
    `).join('');
}

// ==================== DOWNLOAD EXCEL FUNCTIONS ====================

function downloadEnergyDataExcel() {
    const year = document.getElementById('energyFormYear').value;
    if (!year || !currentEnergyDataId) {
        alert('Please select a year first');
        return;
    }
    
    window.location.href = `/admin/energy-data/${currentEnergyDataId}/download-excel?year=${year}`;
}

function downloadResourceDataExcel() {
    const year = document.getElementById('resourceFormYear').value;
    if (!year || !currentResourceDataId) {
        alert('Please select a year first');
        return;
    }
    
    window.location.href = `/admin/energy-resource-data/${currentResourceDataId}/download-excel?year=${year}`;
}

function downloadProductionDataExcel() {
    const year = document.getElementById('productionFormYear').value;
    if (!year || !currentProductionId) {
        alert('Please select a year first');
        return;
    }
    
    window.location.href = `/admin/monthly-production/${currentProductionId}/download-excel?year=${year}`;
}

function downloadVariableDataExcel() {
    const year = document.getElementById('variableFormYear').value;
    if (!year || !currentVariableId) {
        alert('Please select a year first');
        return;
    }
    
    window.location.href = `/admin/monthly-variable/${currentVariableId}/download-excel?year=${year}`;
}


</script>
@endsection