@extends('layouts.dashboard')

@section('title', 'EnPI & Baseline Management')

@section('content')
<style>
/* ── Model card interactive styles ───────────────────────────────────────── */
.model-entry { transition: box-shadow .15s ease, background .15s ease; }
.model-entry:hover { box-shadow: inset 4px 0 0 #3965FF; background: #fafbff; }

/* ── Action buttons on blue header strip ─────────────────────────────────── */
.btn-group .btn-outline-light {
    border-width: 2px;
    font-size: 15px;
    line-height: 1;
    transition: background .15s ease, color .15s ease, border-color .15s ease;
}
.btn-group .btn-outline-light i { font-size: 15px; }

.btn-calculate:hover  { background: #0d6efd !important; border-color: #0d6efd !important; color: #fff !important; }
.btn-edit:hover       { background: #ffc107 !important; border-color: #ffc107 !important; color: #000 !important; }
.btn-delete:hover     { background: #dc3545 !important; border-color: #dc3545 !important; color: #fff !important; }
.btn-approve:hover    { background: #198754 !important; border-color: #198754 !important; color: #fff !important; }
.btn-disapprove:hover { background: #dc3545 !important; border-color: #dc3545 !important; color: #fff !important; }

.btn-calculate.loading { pointer-events: none; opacity: .7; }

.stat-chip { border-radius: 10px; padding: 8px 16px; text-align: center; background: #f8f9fb; border: 1px solid #e9ecef; }
.stat-chip .chip-label { font-size: 10px; font-weight: 700; letter-spacing: .6px; text-transform: uppercase; color: #9ba3af; margin-bottom: 2px; }
.stat-chip .chip-value { font-size: 1.05rem; font-weight: 700; line-height: 1.2; }
</style>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <p class="text-secondary small mb-1">Pages / EnPI & Baseline Management</p>
        <h3 class="fw-bold">EnPI & Baseline Management
            <i class="bi bi-info-circle text-primary" onclick="toggleEnpiInstructions()" style="cursor:pointer;" title="Show instructions"></i>
        </h3>
    </div>
    <div class="d-flex align-items-center">
        <div class="input-group search-box me-3">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="text" class="form-control" placeholder="Search">
        </div>
        <img src="{{ asset('images/user.png') }}" class="rounded-circle" alt="User" style="width:40px;height:40px;">
    </div>
</div>

{{-- ================= INSTRUCTIONS CARD ================= --}}
<div class="card border-0 shadow-sm mb-4" id="enpiInstructionsCard" style="display:none; background:#e3f2fd; border-left:4px solid #2196f3 !important;">
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
                        Create an <strong>EnPI model</strong> by clicking <strong>Add EnPI Model</strong> and selecting the energy source, relevant variables, and baseline period.
                        Use the <strong>Calculate</strong> button to run the regression and generate the EnPI baseline.
                        Review the <strong>R²</strong> and <strong>p-value</strong> to assess model quality. Edit or delete models as needed.
                        Calculated baselines feed into energy performance tracking across the platform.
                    </p>
                </div>
            </div>
            <button class="btn-close" onclick="toggleEnpiInstructions()"></button>
        </div>
    </div>
</div>

<form method="GET" action="{{ route('enpi-baseline-management.index') }}" class="mb-4" id="yearFilterForm">
    <div class="row">
        <div class="col-md-3">
            <label class="form-label fw-bold">Category <span class="text-danger">*</span></label>
            @php $currentYear = (int) date('Y'); @endphp
            <select class="form-select" name="category" id="yearSelect" onchange="this.form.submit()">
                @for($y = $currentYear; $y >= $currentYear - 4; $y--)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>
    </div>
</form>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center p-4">
        <h5 class="fw-bold mb-0">Baseline Analysis Model</h5>
        @if(auth()->user()->hasPermission('enpi-baseline-management.add'))
        <button class="btn btn-primary" id="btnAddModel" data-bs-toggle="modal" data-bs-target="#modelModal">
            Add Model
        </button>
        @endif
    </div>
    <div class="card-body p-0">
        @forelse($models as $model)
        <div class="model-entry border-bottom">

            {{-- ── Header strip ────────────────────────────────────────────── --}}
            <div class="d-flex justify-content-between align-items-center px-4 py-3"
                 style="background:linear-gradient(90deg,#1a3aad 0%,#2d52e0 100%);">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-graph-up text-white" style="opacity:.8;font-size:15px;"></i>
                    <span class="text-white fw-bold" style="font-size:1rem;">{{ $model->model_name }}</span>
                    <span class="badge rounded-pill ms-1"
                          style="background:rgba(255,255,255,.2);color:#fff;font-size:11px;">
                        {{ $model->year }}
                    </span>
                    <span class="badge rounded-pill ms-1 bg-{{ $model->approvalBadgeColor }}" style="font-size:11px;">
                        {{ ucfirst($model->approval_status) }}
                    </span>
                </div>
                <div class="btn-group btn-group-sm gap-2">
                    <button type="button"
                            class="btn btn-outline-light btn-calculate"
                            data-id="{{ $model->id }}" title="Calculate">
                        <i class="bi bi-calculator"></i>
                    </button>
                    @if(auth()->user()->hasPermission('enpi-baseline-management.approval'))
                    <form method="POST" action="{{ route('enpi-baseline-management.approve', $model->id) }}" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                                class="btn btn-outline-light btn-approve"
                                title="Approve"
                                @if($model->approval_status === 'approved') disabled @endif>
                            <i class="bi bi-check-circle"></i>
                        </button>
                    </form>
                    <form method="POST" action="{{ route('enpi-baseline-management.disapprove', $model->id) }}" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                                class="btn btn-outline-light btn-disapprove"
                                title="Disapprove"
                                @if($model->approval_status === 'disapproved') disabled @endif>
                            <i class="bi bi-x-circle"></i>
                        </button>
                    </form>
                    @endif
                    @if(auth()->user()->hasPermission('enpi-baseline-management.edit'))
                    <button type="button"
                            class="btn btn-outline-light btn-edit"
                            data-model="{{ htmlspecialchars(json_encode($model->toArray()), ENT_QUOTES, 'UTF-8') }}"
                            data-bs-toggle="modal" data-bs-target="#modelModal" title="Edit">
                        <i class="bi bi-pencil"></i>
                    </button>
                    @endif
                    @if(auth()->user()->hasPermission('enpi-baseline-management.delete'))
                    <button type="button"
                            class="btn btn-outline-light btn-delete"
                            data-id="{{ $model->id }}" data-name="{{ $model->model_name }}" title="Delete">
                        <i class="bi bi-trash"></i>
                    </button>
                    @endif
                </div>
            </div>

            {{-- ── Body ────────────────────────────────────────────────────── --}}
            <div class="px-4 pt-3 pb-4">

                {{-- Equation --}}
                <div class="d-flex align-items-center gap-2 rounded-3 px-3 py-2 mb-3"
                     style="background:#eef2ff;border:1px solid #c7d5ff;">
                    <i class="bi bi-function text-primary flex-shrink-0"></i>
                    <span style="font-family:monospace;font-size:.88rem;word-break:break-all;color:#212529;">
                        {{ $model->equation ?: '—' }}
                    </span>
                </div>

                {{-- Stats + variable flow in one row --}}
                <div class="d-flex flex-wrap align-items-center gap-3">

                    {{-- R² chip --}}
                    <div class="stat-chip" style="min-width:82px;">
                        <div class="chip-label">R²</div>
                        <div class="chip-value text-primary">{{ number_format($model->r_squared, 3) }}</div>
                        <div style="font-size:10px;color:#adb5bd;">{{ round($model->r_squared * 100, 1) }}%</div>
                    </div>

                    {{-- Correlation chip --}}
                    <div class="stat-chip">
                        <div class="chip-label">Correlation</div>
                        <div class="chip-value mt-1">
                            <span class="badge bg-{{ $model->correlationBadgeColor }}" style="font-size:.8rem;">
                                {{ $model->correlation_strength }}
                            </span>
                        </div>
                    </div>

                    {{-- Variables chip --}}
                    <div class="stat-chip" style="min-width:72px;">
                        <div class="chip-label">Variables</div>
                        <div class="chip-value text-secondary">{{ $model->number_of_independent_variables }}</div>
                    </div>

                    {{-- Divider --}}
                    <div style="width:1px;height:44px;background:#dee2e6;flex-shrink:0;"></div>

                    {{-- Variable flow: Y → X1 → X2… --}}
                    <div class="d-flex align-items-center flex-wrap gap-1" style="font-size:.83rem;">
                        <span class="rounded-pill px-3 py-1 fw-semibold text-white"
                              style="background:#1a3aad;">
                            Y: {{ $model->dependent_label }}
                        </span>
                        @for($i = 1; $i <= $model->number_of_independent_variables; $i++)
                            @php $xVar = 'independent_variable_x' . $i; @endphp
                            <i class="bi bi-arrow-right text-muted" style="font-size:11px;"></i>
                            <span class="rounded-pill px-3 py-1 fw-semibold"
                                  style="background:#eef2ff;border:1px solid #c7d5ff;color:#3965FF;">
                                X{{ $i }}: {{ $model->$xVar ?: '—' }}
                            </span>
                        @endfor
                    </div>

                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-5 text-muted">
            <i class="bi bi-bar-chart-line" style="font-size:2.5rem;opacity:.35;"></i>
            <p class="mt-2 mb-0">No models found for {{ $year }}.</p>
        </div>
        @endforelse
    </div>
</div>

{{-- ─────────────────────────── Add / Edit Modal ─────────────────────────── --}}
<div class="modal fade" id="modelModal" tabindex="-1" aria-labelledby="modelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content" style="border-radius:20px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark" id="modelModalLabel">Add Model</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="modelForm" method="POST">
                    @csrf
                    <input type="hidden" id="modelFormMethod" name="_method" value="POST">
                    <input type="hidden" id="modelId" name="model_id">

                    {{-- ── Row 1: Model Name & Year ─────────────────────── --}}
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="modelName" class="form-label text-primary fw-semibold">
                                Model Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="model_name" id="modelName" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="modalYear" class="form-label text-primary fw-semibold">
                                Year <span class="text-danger">*</span>
                            </label>
                            <select name="year" id="modalYear" class="form-select" required>
                                @for($y = $currentYear; $y >= $currentYear - 4; $y--)
                                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <hr class="my-3">

                    {{-- ── Dependent Variable (Y) ───────────────────────── --}}
                    <h6 class="fw-bold text-secondary mb-2">Dependent Variable (Y) — Energy Consumption</h6>
                    <p class="text-muted small mb-3">
                        Shows energy sources that have usage data (GJ) recorded for the selected year.
                    </p>

                    {{-- Hidden fields written by JS --}}
                    <input type="hidden" name="dependent_variable_type" id="hiddenDepType" value="">
                    <input type="hidden" name="energy_data_id"          id="hiddenEnergyDataId" value="">
                    <input type="hidden" name="energy_resource_id"      id="hiddenEnergyResourceId" value="">
                    <input type="hidden" name="dependent_variable"      id="hiddenDepVar" value="">

                    <div class="mb-4">
                        <label for="dependentSelect" class="form-label text-primary fw-semibold">
                            Select Energy Source <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="dependentSelect" required>
                            <option value="">Loading…</option>
                        </select>
                        <div class="form-text" id="dependentHint"></div>
                    </div>

                    <hr class="my-3">

                    {{-- ── Independent Variables ────────────────────────── --}}
                    <div class="d-flex align-items-center mb-3">
                        <label for="numIV" class="fw-bold text-secondary mb-0 me-3">Independent Variables (X)</label>
                        <div style="min-width:160px;">
                            <select class="form-select form-select-sm" name="number_of_independent_variables" id="numIV" required>
                                <option value="1">1 variable</option>
                                <option value="2">2 variables</option>
                                <option value="3">3 variables</option>
                                <option value="4">4 variables</option>
                            </select>
                        </div>
                    </div>
                    <p class="text-muted small mb-3">
                        Shows production / variable data recorded for the selected year.
                    </p>

                    @foreach([1,2,3,4] as $xi)
                    {{-- Hidden fields for each X written by JS --}}
                    <input type="hidden" name="independent_variable_x{{ $xi }}"      id="hiddenXName{{ $xi }}" value="">
                    <input type="hidden" name="independent_variable_type_x{{ $xi }}" id="hiddenXType{{ $xi }}" value="">
                    <input type="hidden" name="monthly_production_id_x{{ $xi }}"     id="hiddenXProdId{{ $xi }}" value="">
                    <input type="hidden" name="monthly_variable_id_x{{ $xi }}"       id="hiddenXVarId{{ $xi }}" value="">

                    <div class="mb-3 x-var-block" id="xBlock{{ $xi }}" style="{{ $xi > 1 ? 'display:none;' : '' }}">
                        <label for="xSelect{{ $xi }}" class="form-label text-primary fw-semibold">
                            X{{ $xi }} — Independent Variable
                            @if($xi === 1)<span class="text-danger">*</span>@endif
                        </label>
                        <select class="form-select x-select" id="xSelect{{ $xi }}"
                                data-xi="{{ $xi }}"
                                {{ $xi === 1 ? 'required' : '' }}>
                            <option value="">Loading…</option>
                        </select>
                    </div>
                    @endforeach

                    <div class="text-center mt-4 mb-2">
                        <button type="submit" class="btn btn-primary px-5 py-2"
                                style="border-radius:10px;min-width:150px;font-weight:600;" id="submitBtn">
                            Add Now
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ─────────────────────────── Delete Modal ─────────────────────────────── --}}
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:15px;">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Delete Model</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <p id="deleteConfirmText"></p>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary mx-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger mx-2">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleEnpiInstructions() {
    const card = document.getElementById('enpiInstructionsCard');
    card.style.display = card.style.display === 'none' ? 'block' : 'none';
}

document.addEventListener('DOMContentLoaded', function () {

    // ── Route URLs ────────────────────────────────────────────────────────────
    const urlDepOptions  = "{{ route('enpi-baseline-management.dependent-options') }}";
    const urlIndOptions  = "{{ route('enpi-baseline-management.independent-options') }}";
    const actionAdd      = "{{ route('enpi-baseline-management.store') }}";
    const actionEdit     = "{{ route('enpi-baseline-management.update', ':id') }}";
    const actionDelete   = "{{ route('enpi-baseline-management.destroy', ':id') }}";
    const actionCalc     = "{{ route('enpi-baseline-management.calculate', ':id') }}";

    // Caches so we don't re-fetch for the same year
    let depOptionsCache = {};
    let indOptionsCache = {};

    // ── Fetch helpers ─────────────────────────────────────────────────────────
    function fetchDepOptions(year) {
        if (depOptionsCache[year]) return Promise.resolve(depOptionsCache[year]);
        return fetch(urlDepOptions + '?year=' + year)
            .then(r => r.json())
            .then(data => { depOptionsCache[year] = data.options; return data.options; });
    }

    function fetchIndOptions(year) {
        if (indOptionsCache[year]) return Promise.resolve(indOptionsCache[year]);
        return fetch(urlIndOptions + '?year=' + year)
            .then(r => r.json())
            .then(data => { indOptionsCache[year] = data.options; return data.options; });
    }

    // ── Populate grouped <select> ─────────────────────────────────────────────
    function buildGroupedOptions(selectEl, options, currentValue, placeholder) {
        selectEl.innerHTML = '';
        const blank = document.createElement('option');
        blank.value = '';
        blank.textContent = placeholder;
        selectEl.appendChild(blank);

        if (!options || options.length === 0) {
            const none = document.createElement('option');
            none.disabled = true;
            none.textContent = '— No data available for this year —';
            selectEl.appendChild(none);
            return;
        }

        // Group by 'group' key
        const groups = {};
        options.forEach(opt => {
            if (!groups[opt.group]) groups[opt.group] = [];
            groups[opt.group].push(opt);
        });

        Object.entries(groups).forEach(([groupName, items]) => {
            const og = document.createElement('optgroup');
            og.label = groupName;
            items.forEach(item => {
                const o = document.createElement('option');
                o.value = item.value;
                o.textContent = item.label;
                // Store extra data as attributes for later parsing
                o.dataset.type = item.type;
                o.dataset.id   = item.id;
                o.dataset.name = item.name;
                if (item.value === currentValue) o.selected = true;
                og.appendChild(o);
            });
            selectEl.appendChild(og);
        });
    }

    // ── Load all dropdowns for a given year ───────────────────────────────────
    function loadAllDropdowns(year, depCurrentVal, indCurrentVals) {
        const depSel = document.getElementById('dependentSelect');
        depSel.innerHTML = '<option value="">Loading…</option>';
        [1,2,3,4].forEach(i => {
            const el = document.getElementById('xSelect' + i);
            if (el) el.innerHTML = '<option value="">Loading…</option>';
        });

        Promise.all([fetchDepOptions(year), fetchIndOptions(year)]).then(([depOpts, indOpts]) => {
            buildGroupedOptions(depSel, depOpts, depCurrentVal || '', 'Choose energy source…');
            syncDepHiddens(depSel);

            [1,2,3,4].forEach(i => {
                const el = document.getElementById('xSelect' + i);
                if (!el) return;
                buildGroupedOptions(el, indOpts, indCurrentVals ? (indCurrentVals[i] || '') : '', 'Choose variable / production…');
                syncIndHiddens(el, i);
            });
        }).catch(() => {
            depSel.innerHTML = '<option value="">Error loading options</option>';
        });
    }

    // ── Sync hidden inputs from dependent select ──────────────────────────────
    function syncDepHiddens(sel) {
        const opt = sel.selectedOptions[0];
        const type = opt ? (opt.dataset.type || '') : '';
        const id   = opt ? (opt.dataset.id   || '') : '';
        const name = opt ? (opt.dataset.name || '') : '';

        document.getElementById('hiddenDepType').value           = type;
        document.getElementById('hiddenEnergyDataId').value      = type === 'energy_data'     ? id : '';
        document.getElementById('hiddenEnergyResourceId').value  = type === 'energy_resource' ? id : '';
        document.getElementById('hiddenDepVar').value            = name;

        const hint = document.getElementById('dependentHint');
        if (type === 'energy_data')     hint.textContent = 'Source: Energy Data — usage_gj values will be used as Y.';
        else if (type === 'energy_resource') hint.textContent = 'Source: Energy Resource — usage_gj values will be used as Y.';
        else hint.textContent = '';
    }

    // ── Sync hidden inputs from an independent variable select ────────────────
    function syncIndHiddens(sel, xi) {
        const opt  = sel.selectedOptions[0];
        const type = opt ? (opt.dataset.type || '') : '';
        const id   = opt ? (opt.dataset.id   || '') : '';
        const name = opt ? (opt.dataset.name || '') : '';

        document.getElementById('hiddenXName'   + xi).value = name;
        document.getElementById('hiddenXType'   + xi).value = type;
        document.getElementById('hiddenXProdId' + xi).value = type === 'monthly_production' ? id : '';
        document.getElementById('hiddenXVarId'  + xi).value = type === 'monthly_variable'   ? id : '';
    }

    // ── Number of variables → show/hide X blocks ──────────────────────────────
    function applyNumIV() {
        const n = parseInt(document.getElementById('numIV').value) || 1;
        [1,2,3,4].forEach(i => {
            const block = document.getElementById('xBlock' + i);
            const sel   = document.getElementById('xSelect' + i);
            if (i <= n) {
                block.style.display = '';
                if (i === 1) sel.required = true;
            } else {
                block.style.display = 'none';
                sel.required = false;
                // Clear hidden values for disabled vars
                document.getElementById('hiddenXName'   + i).value = '';
                document.getElementById('hiddenXType'   + i).value = '';
                document.getElementById('hiddenXProdId' + i).value = '';
                document.getElementById('hiddenXVarId'  + i).value = '';
            }
        });
    }

    document.getElementById('numIV').addEventListener('change', applyNumIV);

    // ── Year change inside modal ───────────────────────────────────────────────
    document.getElementById('modalYear').addEventListener('change', function () {
        loadAllDropdowns(this.value, '', {});
    });

    // ── Dependent select change ───────────────────────────────────────────────
    document.getElementById('dependentSelect').addEventListener('change', function () {
        syncDepHiddens(this);
    });

    // ── Independent select changes ────────────────────────────────────────────
    [1,2,3,4].forEach(i => {
        document.getElementById('xSelect' + i).addEventListener('change', function () {
            syncIndHiddens(this, i);
        });
    });

    // ── ADD button ────────────────────────────────────────────────────────────
    document.getElementById('btnAddModel')?.addEventListener('click', function () {
        document.getElementById('modelModalLabel').textContent = 'Add Model';
        document.getElementById('modelForm').reset();
        document.getElementById('modelForm').action = actionAdd;
        document.getElementById('modelFormMethod').value = 'POST';
        document.getElementById('modelId').value = '';
        document.getElementById('submitBtn').textContent = 'Add Now';

        // Set year to the currently selected page year and load options
        const pageYear = document.getElementById('yearSelect').value;
        document.getElementById('modalYear').value = pageYear;
        applyNumIV();
        loadAllDropdowns(pageYear, '', {});
    });

    // ── EDIT buttons ──────────────────────────────────────────────────────────
    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', () => {
            let model;
            try { model = JSON.parse(btn.getAttribute('data-model')); } catch { return; }

            document.getElementById('modelModalLabel').textContent =
                'Edit ' + (model.model_name || 'Model ' + model.id);
            document.getElementById('modelForm').action = actionEdit.replace(':id', model.id);
            document.getElementById('modelFormMethod').value = 'PUT';
            document.getElementById('modelId').value = model.id;
            document.getElementById('modelName').value = model.model_name ?? '';
            document.getElementById('submitBtn').textContent = 'Update';

            // Year
            document.getElementById('modalYear').value = model.year ?? new Date().getFullYear();

            // Number of vars
            document.getElementById('numIV').value = model.number_of_independent_variables ?? 1;
            applyNumIV();

            // Build the currently-saved dependent value string (e.g. "energy_data:5")
            let depCurrentVal = '';
            const depType = model.dependent_variable_type ?? 'monthly_variable';
            if (depType === 'energy_data'     && model.energy_data_id)     depCurrentVal = 'energy_data:'     + model.energy_data_id;
            if (depType === 'energy_resource'  && model.energy_resource_id) depCurrentVal = 'energy_resource:' + model.energy_resource_id;

            // Build current X values
            const indCurrentVals = {};
            [1,2,3,4].forEach(i => {
                const t  = model['independent_variable_type_x' + i];
                const pi = model['monthly_production_id_x'     + i];
                const vi = model['monthly_variable_id_x'       + i];
                if (t === 'monthly_production' && pi) indCurrentVals[i] = 'monthly_production:' + pi;
                else if (t === 'monthly_variable' && vi) indCurrentVals[i] = 'monthly_variable:' + vi;
                else indCurrentVals[i] = '';
            });

            loadAllDropdowns(model.year, depCurrentVal, indCurrentVals);
        });
    });

    // ── CALCULATE buttons ─────────────────────────────────────────────────────
    document.querySelectorAll('.btn-calculate').forEach(btn => {
        btn.addEventListener('click', () => {
            btn.classList.add('loading');
            btn.innerHTML = '<span class="spinner-border spinner-border-sm" style="width:14px;height:14px;"></span>';
            window.location.href = actionCalc.replace(':id', btn.getAttribute('data-id'));
        });
    });

    // ── DELETE buttons ────────────────────────────────────────────────────────
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', () => {
            const id   = btn.getAttribute('data-id');
            const name = btn.getAttribute('data-name');
            document.getElementById('deleteConfirmText').innerHTML =
                `Delete model <strong>${name}</strong>? This action cannot be undone.`;
            document.getElementById('deleteForm').action = actionDelete.replace(':id', id);
            new bootstrap.Modal(document.getElementById('confirmDeleteModal')).show();
        });
    });

    // ── Form submit: validate hidden fields are populated ─────────────────────
    document.getElementById('modelForm').addEventListener('submit', function (e) {
        // Force-sync hidden inputs from current select state before validating
        syncDepHiddens(document.getElementById('dependentSelect'));
        const activeN = parseInt(document.getElementById('numIV').value) || 1;
        for (let i = 1; i <= activeN; i++) {
            const sel = document.getElementById('xSelect' + i);
            if (sel) syncIndHiddens(sel, i);
        }

        const depType = document.getElementById('hiddenDepType').value;
        if (!depType) {
            e.preventDefault();
            document.getElementById('dependentSelect').classList.add('is-invalid');
            document.getElementById('dependentSelect').focus();
            return;
        }
        document.getElementById('dependentSelect').classList.remove('is-invalid');

        const n = parseInt(document.getElementById('numIV').value) || 1;
        for (let i = 1; i <= n; i++) {
            const xType = document.getElementById('hiddenXType' + i).value;
            if (!xType) {
                e.preventDefault();
                const sel = document.getElementById('xSelect' + i);
                sel.classList.add('is-invalid');
                sel.focus();
                return;
            }
            document.getElementById('xSelect' + i).classList.remove('is-invalid');
        }
    });
});
</script>
@endpush
