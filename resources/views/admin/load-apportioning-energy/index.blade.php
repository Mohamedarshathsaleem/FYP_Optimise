@extends('layouts.dashboard')

@section('title', 'Load Apportioning (Energy & Energy Resource)')

@section('content')

<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <p class="text-secondary small mb-1">Pages / Load Apportioning (Energy & Energy Resource)</p>
        <h3 class="fw-bold">Load Apportioning (Energy & Energy Resource)</h3>
    </div>
    <div class="d-flex align-items-center">
        <div class="input-group search-box me-3">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="text" class="form-control" placeholder="Search">
        </div>
        <img src="{{ asset('images/user.png') }}" class="rounded-circle" style="width:40px;height:40px">
    </div>
</div>

<!-- Instructions -->
<div class="card border-0 shadow-sm mb-4" id="instructionCard">
    <div class="card-body d-flex justify-content-between">
        <div class="d-flex">
            <div class="me-3">
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px">
                    <i class="bi bi-info-lg"></i>
                </div>
            </div>
            <div>
                <h6 class="fw-bold text-primary mb-1">Instructions</h6>
                <p class="mb-0 text-muted">
                    Complete the desired year, select load apportionment approach, energy type, unit and matrix.
                </p>
            </div>
        </div>
        <button class="btn-close" onclick="document.getElementById('instructionCard').remove()"></button>
    </div>
</div>

<!-- Filter -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="row g-3">

            <div class="col-md-2">
                <label class="fw-bold">Year *</label>
                <select class="form-select" id="yearSelect">
                    <option value="">Choose</option>
                    @for($y = 2025; $y >= 2018; $y--)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <div class="col-md-3">
                <label class="fw-bold">Load Apportionment Approach</label>
                <select class="form-select" id="approachSelect">
                    <option value="">Choose</option>
                    <option>Equipment Types</option>
                    <option>Building / Blocks</option>
                    <option>Process Plants</option>
                    <option>Department</option>
                </select>
            </div>

            <div class="col-md-2">
                <label class="fw-bold">Type of energy</label>
                <input type="text" class="form-control" id="energyTypeInput" readonly onclick="openEnergyModal()" placeholder="Choose" style="cursor: pointer;">
            </div>

            <div class="col-md-2">
                <label class="fw-bold">Unit</label>
                <select class="form-select">
                    <option>Energy (GJ)</option>
                    <option>Load percentage</option>
                </select>
            </div>

            <div class="col-md-2">
                <label class="fw-bold">Matrix</label>
                <select class="form-select" id="matrixSelect" onchange="renderMatrix()">
                    <option value="">Choose</option>
                    <option value="table">Table</option>
                    <option value="graph">Graph</option>
                </select>
            </div>

        </div>
    </div>
</div>

<!-- CONTENT -->
<div id="contentArea" class="text-center text-muted py-5">
    Please complete category
</div>

<!-- ENERGY MODAL -->
<div class="modal fade" id="energyModal" tabindex="-1" aria-labelledby="energyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="energyModalLabel">Choose Energy Types</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <table class="table table-bordered">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th style="width: 50px;"></th>
                            <th>Energy</th>
                            <th>Conversion</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center"><input type="checkbox" class="form-check-input" value="Hard Coal"></td>
                            <td>Hard Coal</td>
                            <td>29.3076 GJ/tonne</td>
                        </tr>
                        <tr>
                            <td class="text-center"><input type="checkbox" class="form-check-input" value="Coke / Oven coke"></td>
                            <td>Coke / Oven coke</td>
                            <td>26.3768 GJ/tonne</td>
                        </tr>
                        <tr>
                            <td class="text-center"><input type="checkbox" class="form-check-input" value="Gas coke"></td>
                            <td>Gas coke</td>
                            <td>26.3768 GJ/tonne</td>
                        </tr>
                    </tbody>
                </table>

                <div class="text-center">
                    <button class="btn btn-primary px-5" onclick="saveEnergyTypes()">Save</button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let energyModal;

// Initialize modal when document is ready
document.addEventListener('DOMContentLoaded', function() {
    energyModal = new bootstrap.Modal(document.getElementById('energyModal'));
});

function openEnergyModal(){
    if (!energyModal) {
        energyModal = new bootstrap.Modal(document.getElementById('energyModal'));
    }
    energyModal.show();
}

function saveEnergyTypes() {
    const checkboxes = document.querySelectorAll('#energyModal input[type="checkbox"]:checked');
    const selectedEnergies = Array.from(checkboxes).map(cb => cb.value);
    
    // Update the input field with selected energies
    const energyInput = document.getElementById('energyTypeInput');
    if (selectedEnergies.length > 0) {
        energyInput.value = selectedEnergies.join(', ');
    } else {
        energyInput.value = '';
    }
    
    // Close modal
    energyModal.hide();
}

function renderMatrix(){
    const type = document.getElementById('matrixSelect').value;
    const area = document.getElementById('contentArea');

    if(type === 'table'){
        area.innerHTML = `
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <h5 class="fw-bold">Load Apportioning Table</h5>
                        <button class="btn btn-primary btn-sm">Calculate</button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="bg-primary text-white text-center">
                                <tr>
                                    <th>Equipment Types</th>
                                    <th>Current Energy Consumption (GJ)</th>
                                    <th>Load Percentage (%)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><input class="form-control"></td>
                                    <td><input class="form-control"></td>
                                    <td></td>
                                </tr>
                                <tr class="bg-light fw-bold">
                                    <td>Total</td>
                                    <td>60,000</td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        `;
    }

    if(type === 'graph'){
        area.innerHTML = `
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <h5 class="fw-bold mb-3">Load Apportioning Graph</h5>
                    <i class="bi bi-pie-chart-fill" style="font-size:120px;color:#4f6ef7"></i>
                    <p class="text-muted mt-3">Graph visualization (UI placeholder)</p>
                </div>
            </div>
        `;
    }
}
</script>
@endpush