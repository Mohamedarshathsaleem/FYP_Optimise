@extends('layouts.dashboard')

@section('title', 'Energy Types Settings')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Header Section -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <p class="text-secondary small mb-1">Pages / Energy Types Settings</p>
        <h3 class="fw-bold">Energy Types Settings</h3>
    </div>
    <div class="d-flex align-items-center">
        <div class="input-group search-box me-3">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="text" class="form-control" id="searchInput" placeholder="Search" value="{{ request('search') }}">
        </div>
        <img src="{{ asset('images/user.png') }}" class="rounded-circle" style="width:40px; height:40px;">
    </div>
</div>

<!-- Instructions -->
<div class="card border-0 shadow-sm mb-4" style="background:#e3f2fd; border-left:4px solid #2196f3;">
    <div class="card-body p-4 d-flex">
        <div class="me-3 d-flex align-items-start">
            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;">
                <i class="bi bi-info-lg text-white"></i>
            </div>
        </div>
        <div>
            <h6 class="fw-bold text-primary mb-2">Instructions</h6>
            <p class="mb-0 text-dark">1. Please add the energy or energy resource and their respective conversion coefficients/equivalence</p>
        </div>
    </div>
</div>

<!-- Energy Types List -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">Energy Types List ({{ $energyTypes->total() ?? 0 }})</h5>
            <button class="btn btn-primary" id="btnAddEnergy">
                <i class="bi bi-plus-circle"></i> Add Energy Types
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead style="background:#4472C4; color:white;">
                    <tr>
                        <th class="py-3">Energy</th>
                        <th class="py-3">Conversion Coefficients/Equivalence</th>
                        <th class="py-3 text-center" style="width:120px;">Actions</th>
                    </tr>
                </thead>
                <tbody id="energyTableBody">
                    @forelse($energyTypes as $energyType)
                        <tr data-id="{{ $energyType->id }}">
                            <td class="py-3">{{ $energyType->name }}</td>
                            <td class="py-3">{!! nl2br(e($energyType->conversion_coefficient)) !!}</td>
                            <td class="py-3 text-center">
                                <button class="btn btn-sm btn-outline-warning me-1 btn-edit" data-id="{{ $energyType->id }}" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger btn-delete" data-id="{{ $energyType->id }}" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-3 text-center text-muted">No energy types found. <button class="btn btn-sm btn-primary" id="btnAddEnergyEmpty">Add first</button></td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if(isset($energyTypes) && $energyTypes->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $energyTypes->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Add Energy Modal -->
<div class="modal fade" id="addEnergyModal" tabindex="-1" aria-labelledby="addEnergyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius:15px;">
            <div class="modal-header border-0">
                <h5 class="fw-bold" id="addEnergyModalLabel">Add Energy Types</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="addEnergyForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Energy <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="energyName" name="name" placeholder="Enter energy type" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Conversion Coefficients <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="conversionCoeff" name="conversion_coefficient" rows="2" placeholder="e.g. 29.3076 GJ/tonne" required></textarea>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary px-5 py-2" id="addBtn" style="border-radius:10px;">
                            <span class="spinner-border spinner-border-sm d-none me-2" role="status"></span>
                            Add Now
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Energy Modal -->
<div class="modal fade" id="editEnergyModal" tabindex="-1" aria-labelledby="editEnergyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius:15px;">
            <div class="modal-header border-0">
                <h5 class="fw-bold" id="editEnergyModalLabel">Edit Energy Types</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="editEnergyForm">
                    @csrf
                    <input type="hidden" id="editEnergyId" name="id">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Energy <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editEnergyName" name="name" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Conversion Coefficients <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="editConversionCoeff" name="conversion_coefficient" rows="2" required></textarea>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary px-5 py-2" id="editBtn" style="border-radius:10px;">
                            <span class="spinner-border spinner-border-sm d-none me-2" role="status"></span>
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteEnergyModal" tabindex="-1" aria-labelledby="deleteEnergyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:15px;">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold" id="deleteEnergyModalLabel">Delete Energy Type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-3">
                    <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width:60px;height:60px;">
                        <i class="bi bi-exclamation-triangle text-danger" style="font-size:30px;"></i>
                    </div>
                </div>
                <p class="text-center text-muted mb-0">
                    Are you sure you want to delete this energy type?<br>
                    <strong class="text-dark" id="deleteEnergyName"></strong>
                </p>
                <p class="text-center text-danger small mt-2 mb-0">
                    This action cannot be undone.
                </p>
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger px-4" id="confirmDeleteBtn">
                    <span class="spinner-border spinner-border-sm d-none me-2" role="status"></span>
                    Delete
                </button>
            </div>
        </div>
    </div>
</div>

@endsection
@push('scripts')
<script>
// GLOBAL VARIABLES
let addEnergyModal, editEnergyModal, deleteEnergyModal;
let deleteEnergyId = null;

// EXACTLY SAME PATTERN seperti modal yang jalan
document.addEventListener('DOMContentLoaded', function() {
    // Init modals EXACTLY seperti yang jalan
    console.log('DOM Ready - Energy Types');
    const addModalEl = document.getElementById('addEnergyModal');
    const editModalEl = document.getElementById('editEnergyModal');
    const deleteModalEl = document.getElementById('deleteEnergyModal');
    
    if (addModalEl) addEnergyModal = new bootstrap.Modal(addModalEl);
    if (editModalEl) editEnergyModal = new bootstrap.Modal(editModalEl);
    if (deleteModalEl) deleteEnergyModal = new bootstrap.Modal(deleteModalEl);
    
    // Bind buttons EXACTLY seperti yang jalan
    document.getElementById('btnAddEnergy')?.addEventListener('click', openAddEnergyModal);
    document.getElementById('btnAddEnergyEmpty')?.addEventListener('click', openAddEnergyModal);
    
    // Event delegation untuk table buttons
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-edit')) {
            openEditModal(e.target.closest('.btn-edit').dataset.id);
        }
        if (e.target.closest('.btn-delete')) {
            const id = e.target.closest('.btn-delete').dataset.id;
            const row = e.target.closest('tr');
            const name = row.querySelector('td:first-child').textContent.trim();
            confirmDeleteEnergy(id, name);
        }
    });
    
    // Search
    document.getElementById('searchInput')?.addEventListener('input', function() {
        let timeout;
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            const params = new URLSearchParams(window.location.search);
            if (this.value.trim()) params.set('search', this.value.trim());
            else params.delete('search');
            window.location.search = params.toString();
        }, 500);
    });
    
    // Forms
    document.getElementById('addEnergyForm')?.addEventListener('submit', handleAddSubmit);
    document.getElementById('editEnergyForm')?.addEventListener('submit', handleEditSubmit);
    document.getElementById('confirmDeleteBtn')?.addEventListener('click', handleDeleteConfirm);
});

// FUNCTIONS - SIMPLE & DIRECT
function openAddEnergyModal() {
    document.getElementById('addEnergyForm').reset();
    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    addEnergyModal.show();
}

function openEditModal(id) {
    fetch(`/energy-type-settings/${id}/edit`)
        .then(res => res.json())
        .then(data => {
            document.getElementById('editEnergyId').value = data.id;
            document.getElementById('editEnergyName').value = data.name;
            document.getElementById('editConversionCoeff').value = data.conversion_coefficient;
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            editEnergyModal.show();
        })
        .catch(() => {
            showAlert('Failed to load data', 'danger');
        });
}

function confirmDeleteEnergy(id, name) {
    deleteEnergyId = id;
    document.getElementById('deleteEnergyName').textContent = name;
    deleteEnergyModal.show();
}

function handleDeleteConfirm() {
    if (!deleteEnergyId) return;
    
    showSpinner('confirmDeleteBtn', true);
    
    fetch(`/energy-type-settings/${deleteEnergyId}`, {
        method: 'DELETE', 
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            deleteEnergyModal.hide();
            // Remove row from table with animation
            const row = document.querySelector(`tr[data-id="${deleteEnergyId}"]`);
            if (row) {
                row.style.transition = 'opacity 0.3s';
                row.style.opacity = '0';
                setTimeout(() => {
                    location.reload();
                }, 300);
            } else {
                location.reload();
            }
        } else {
            deleteEnergyModal.hide();
            showAlert(data.message || 'Failed to delete energy type', 'danger');
        }
    })
    .catch(() => {
        deleteEnergyModal.hide();
        showAlert('Failed to delete energy type', 'danger');
    })
    .finally(() => {
        showSpinner('confirmDeleteBtn', false);
        deleteEnergyId = null;
    });
}

function handleAddSubmit(e) {
    e.preventDefault();
    showSpinner('addBtn', true);
    
    fetch('/energy-type-settings', {
        method: 'POST',
        body: new FormData(e.target),
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            addEnergyModal.hide();
            location.reload();
        } else if(data.errors) {
            showValidationErrors(data.errors);
        } else {
            showAlert(data.message || 'Failed to add energy type', 'danger');
        }
    })
    .catch(() => {
        showAlert('Failed to add energy type', 'danger');
    })
    .finally(() => showSpinner('addBtn', false));
}

function handleEditSubmit(e) {
    e.preventDefault();
    showSpinner('editBtn', true);
    
    const id = document.getElementById('editEnergyId').value;
    const data = Object.fromEntries(new FormData(e.target));
    
    fetch(`/energy-type-settings/${id}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            editEnergyModal.hide();
            location.reload();
        } else if(data.errors) {
            showValidationErrors(data.errors);
        } else {
            showAlert(data.message || 'Failed to update energy type', 'danger');
        }
    })
    .catch(() => {
        showAlert('Failed to update energy type', 'danger');
    })
    .finally(() => showSpinner('editBtn', false));
}

function showValidationErrors(errors) {
    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    Object.keys(errors).forEach(key => {
        const input = document.querySelector(`[name="${key}"]`);
        if(input) {
            input.classList.add('is-invalid');
            const feedback = input.parentElement.querySelector('.invalid-feedback');
            if(feedback) feedback.textContent = errors[key][0];
        }
    });
}

function showSpinner(btnId, show) {
    const btn = document.getElementById(btnId);
    const spinner = btn?.querySelector('.spinner-border');
    if(show) {
        spinner?.classList.remove('d-none');
        btn.disabled = true;
    } else {
        spinner?.classList.add('d-none');
        btn.disabled = false;
    }
}

function showAlert(message, type = 'success') {
    // Create alert element
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3`;
    alertDiv.style.zIndex = '9999';
    alertDiv.style.minWidth = '300px';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    // Auto dismiss after 3 seconds
    setTimeout(() => {
        alertDiv.classList.remove('show');
        setTimeout(() => alertDiv.remove(), 150);
    }, 3000);
}
</script>
@endpush