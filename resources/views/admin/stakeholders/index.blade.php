@extends('layouts.dashboard')

@section('title', 'Stakeholders')

<meta name="csrf-token" content="{{ csrf_token() }}">

@section('page-title', 'Stakeholders')
@section('page-title-main', 'Stakeholders')

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
                    <ul class="mb-0 text-dark lh-base">
                        <li>The purpose is to identify and document internal and external stakeholders relevant to the Energy Management System, their roles, expectations, and influence in accordance with Clause 4.2 – Understanding the Needs and Expectations of Interested Parties.</li>
                        <li class="mt-2">Stakeholders are person or organization (3.1.1) that can affect, be affected by, or perceive itself to be affected by a</li>
                    </ul>
                </div>
            </div>
            <button class="btn-close" onclick="closeInstructions()"></button>
        </div>
    </div>
</div>

<!-- Stakeholder List Section -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center p-4">
        <h5 class="fw-bold mb-0">Stakeholder List</h5>
        @if(auth()->user()->hasPermission('stakeholders.add'))
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStakeholderModal">Add Stakeholder</button>
        @endif
        <div class="d-flex justify-content-end mb-4">
        <div class="d-flex gap-2">
           @if(auth()->user()->hasPermission('stakeholders.approval'))
                <a href="{{ route('admin.stakeholders.approval') }}" class="btn btn-outline-info">
                    Approval Page
                </a>
            @endif
            @if(auth()->user()->hasPermission('stakeholders.export'))
              <div class="dropdown">
                  <button class="btn btn-primary dropdown-toggle" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    Export
                  </button>
                  <ul class="dropdown-menu" aria-labelledby="exportDropdown">
                    <li>
                      <a class="dropdown-item d-flex align-items-center gap-2" href="#">
                        <img src="{{ asset('images/pdf.png') }}" alt="" style="width:20px;"> as PDF
                      </a>
                    </li>
                    <li>
                      <a class="dropdown-item d-flex align-items-center gap-2" href="#">
                        <img src="{{ asset('images/excel.png') }}" alt="" style="width:20px;"> as Excel
                      </a>
                    </li>
                  </ul>
                </div>
            @endif
        </div>
</div>

    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="stakeholdersTable">
                <thead class="table-light">
                    <tr>
                        <th>ID <i class="bi bi-chevron-down"></i></th>
                        <th>Stakeholder <i class="bi bi-chevron-down"></i></th>
                        <th>Type <i class="bi bi-chevron-down"></i></th>
                        <th>Role / Interest <i class="bi bi-chevron-down"></i></th>
                        <th>Needs & Expectations <i class="bi bi-chevron-down"></i></th>
                        <th>Influence Level <i class="bi bi-chevron-down"></i></th>
                        <th>Communication Method <i class="bi bi-chevron-down"></i></th>
                        <th>Engagement Frequency <i class="bi bi-chevron-down"></i></th>
                        <th>Responsible Person <i class="bi bi-chevron-down"></i></th>
                        <th>Remarks <i class="bi bi-chevron-down"></i></th>
                        <th>Modified <i class="bi bi-chevron-down"></i></th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stakeholders as $stakeholder)
                    <tr>
                        <td class="py-3">{{ $stakeholder->stakeholder_id }}</td>
                        <td class="py-3">{{ $stakeholder->name }}</td>
                        <td class="py-3">{{ $stakeholder->type }}</td>
                        <td class="py-3">{{ strlen($stakeholder->role) > 30 ? substr($stakeholder->role, 0, 30) . '...' : $stakeholder->role }}</td>
                        <td class="py-3">{{ strlen($stakeholder->needs_expectations) > 30 ? substr($stakeholder->needs_expectations, 0, 30) . '...' : $stakeholder->needs_expectations }}</td>
                        <td class="py-3">
                            <span class="badge
                                @if($stakeholder->influence_level == 'Low') bg-info
                                @elseif($stakeholder->influence_level == 'Medium') bg-warning
                                @else bg-danger
                                @endif">
                                {{ $stakeholder->influence_level }}
                            </span>
                        </td>
                        <td class="py-3">{{ strlen($stakeholder->communication_method) > 20 ? substr($stakeholder->communication_method, 0, 20) . '...' : $stakeholder->communication_method }}</td>
                        <td class="py-3">{{ $stakeholder->engagement_frequency }}</td>
                        <td class="py-3">{{ $stakeholder->responsible_person }}</td>
                        <td class="py-3">{{ strlen($stakeholder->remarks) > 20 ? substr($stakeholder->remarks, 0, 20) . '...' : $stakeholder->remarks }}</td>
                        <td class="py-3">{{ $stakeholder->updated_at->diffForHumans() }}</td>
                        <td class="py-3">
                            <div class="d-flex gap-1">
                                <button class="btn btn-sm btn-primary" onclick="viewStakeholder({{ $stakeholder->id }})" title="View" style="padding: 4px 8px;">
                                    <i class="bi bi-eye"></i>
                                </button>
                                @if(auth()->user()->hasPermission('stakeholders.edit'))
                                <button class="btn btn-sm btn-warning" onclick="editStakeholder({{ $stakeholder->id }})" title="Edit" style="padding: 4px 8px;">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                @endif
                                @if(auth()->user()->hasPermission('stakeholders.delete'))
                                <button class="btn btn-sm btn-danger" onclick="deleteStakeholder({{ $stakeholder->id }})" title="Delete" style="padding: 4px 8px;">
                                    <i class="bi bi-trash"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="12" class="text-center py-5">
                            <div class="text-muted">
                                <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                                <h5 class="mt-2">No stakeholders found</h5>
                                <p>Click "Add Stakeholder" to create your first stakeholder.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($stakeholders->hasPages())
        <div class="d-flex justify-content-between align-items-center p-3">
            <div class="text-muted small">
                Showing {{ $stakeholders->firstItem() }} to {{ $stakeholders->lastItem() }} of {{ $stakeholders->total() }} results
            </div>
            <div>
                {{ $stakeholders->links() }}
            </div>
        </div>
        @else
        <div class="d-flex justify-content-end align-items-center p-3">
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted small">{{ $stakeholders->count() }} total</span>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Detail Stakeholder Modal -->
<div class="modal fade" id="detailStakeholderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header border-0 pb-2">
                <h5 class="modal-title fw-bold text-dark">Detail Stakeholder</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="detailStakeholderContent">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Stakeholder Modal -->
<div class="modal fade" id="addStakeholderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header border-0 pb-2">
                <h5 class="modal-title fw-bold text-dark">Add Stakeholder</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div id="addStakeholderAlert" class="alert alert-danger d-none" role="alert">
                    <ul class="mb-0" id="addStakeholderErrors"></ul>
                </div>
                <form id="addStakeholderForm">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-primary">Stakeholder Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" placeholder="Enter stakeholder name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-primary">Type (Internal / External) <span class="text-danger">*</span></label>
                            <select class="form-select" name="type" required>
                                <option value="">Choose...</option>
                                <option value="Internal">Internal</option>
                                <option value="External">External</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-primary">Role / Interest <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="role" placeholder="Enter role or interest" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-primary">Needs & Expectations <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="needs_expectations" placeholder="Enter needs and expectations" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-primary">Influence Level <span class="text-danger">*</span></label>
                            <select class="form-select" name="influence_level" required>
                                <option value="">Choose...</option>
                                <option value="Low">Low</option>
                                <option value="Medium">Medium</option>
                                <option value="High">High</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-primary">Communication Method <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="communication_method" placeholder="Enter communication method" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-primary">Engagement Frequency <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="engagement_frequency" placeholder="Enter engagement frequency" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-primary">Responsible Person <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="responsible_person" placeholder="Enter responsible person" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-primary">Remarks <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="remarks" rows="3" placeholder="Enter remarks" required></textarea>
                    </div>
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary px-5 py-2" id="addStakeholderBtn" style="border-radius: 10px; min-width: 120px;">
                            <span class="spinner-border spinner-border-sm d-none me-2" role="status"></span>
                            Add Now
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Stakeholder Modal -->
<div class="modal fade" id="editStakeholderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header border-0 pb-2">
                <h5 class="modal-title fw-bold text-dark">Edit Stakeholder</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div id="editStakeholderAlert" class="alert alert-danger d-none" role="alert">
                    <ul class="mb-0" id="editStakeholderErrors"></ul>
                </div>
                <form id="editStakeholderForm">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" id="editStakeholderId" name="stakeholder_id">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-primary">Stakeholder Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" id="editName" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-primary">Type (Internal / External) <span class="text-danger">*</span></label>
                            <select class="form-select" name="type" id="editType" required>
                                <option value="">Choose...</option>
                                <option value="Internal">Internal</option>
                                <option value="External">External</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-primary">Role / Interest <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="role" id="editRole" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-primary">Needs & Expectations <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="needs_expectations" id="editNeedsExpectations" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-primary">Influence Level <span class="text-danger">*</span></label>
                            <select class="form-select" name="influence_level" id="editInfluenceLevel" required>
                                <option value="">Choose...</option>
                                <option value="Low">Low</option>
                                <option value="Medium">Medium</option>
                                <option value="High">High</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-primary">Communication Method <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="communication_method" id="editCommunicationMethod" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-primary">Engagement Frequency <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="engagement_frequency" id="editEngagementFrequency" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-primary">Responsible Person <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="responsible_person" id="editResponsiblePerson" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-primary">Remarks <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="remarks" id="editRemarks" rows="3" required></textarea>
                    </div>
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary px-5 py-2" id="editStakeholderBtn" style="border-radius: 10px; min-width: 120px;">
                            <span class="spinner-border spinner-border-sm d-none me-2" role="status"></span>
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-body text-center p-4">
                <div class="mb-3">
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                </div>
                <h5 class="fw-bold text-dark mb-2" id="successTitle">Success!</h5>
                <p class="text-muted mb-4" id="successMessage">Operation completed successfully.</p>
                <button type="button" class="btn btn-primary px-4" data-bs-dismiss="modal" style="border-radius: 10px;">OK</button>
            </div>
        </div>
    </div>
</div>

<!-- Error Modal -->
<div class="modal fade" id="errorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-body text-center p-4">
                <div class="mb-3">
                    <i class="bi bi-exclamation-circle-fill text-danger" style="font-size: 4rem;"></i>
                </div>
                <h5 class="fw-bold text-dark mb-2" id="errorTitle">Error!</h5>
                <p class="text-muted mb-4" id="errorMessage">Something went wrong.</p>
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal" style="border-radius: 10px;">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-body text-center p-4">
                <div class="mb-3">
                    <i class="bi bi-question-circle-fill text-warning" style="font-size: 4rem;"></i>
                </div>
                <h5 class="fw-bold text-dark mb-2" id="confirmTitle">Are you sure?</h5>
                <p class="text-muted mb-4" id="confirmMessage">This action cannot be undone.</p>
                <div class="d-flex gap-2 justify-content-center">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal" style="border-radius: 10px;">Cancel</button>
                    <button type="button" class="btn btn-danger px-4" id="confirmButton" style="border-radius: 10px;">Delete</button>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('🚀 Stakeholders page loaded');

        // Get CSRF token dari meta tag atau form
        let csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                        document.querySelector('input[name="_token"]')?.value ||
                        '{{ csrf_token() }}';

        console.log('🔐 CSRF Token:', csrfToken ? 'Found' : 'Missing');

        // Modal Functions
        function showSuccessModal(title, message, callback = null) {
            document.getElementById('successTitle').textContent = title;
            document.getElementById('successMessage').textContent = message;
            const modal = new bootstrap.Modal(document.getElementById('successModal'));
            modal.show();

            if (callback) {
                document.getElementById('successModal').addEventListener('hidden.bs.modal', function() {
                    callback();
                }, { once: true });
            }
        }

        function showErrorModal(title, message) {
            document.getElementById('errorTitle').textContent = title;
            document.getElementById('errorMessage').textContent = message;
            const modal = new bootstrap.Modal(document.getElementById('errorModal'));
            modal.show();
        }

        function showConfirmModal(title, message, callback) {
            document.getElementById('confirmTitle').textContent = title;
            document.getElementById('confirmMessage').textContent = message;
            const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
            modal.show();

            document.getElementById('confirmButton').onclick = function() {
                modal.hide();
                callback();
            };
        }

        // Close instructions
        window.closeInstructions = function() {
            document.getElementById('instructionsCard').style.display = 'none';
        };

        // Search functionality
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('keyup', function() {
                const searchTerm = this.value.toLowerCase();
                const tableRows = document.querySelectorAll('#stakeholdersTable tbody tr');

                tableRows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(searchTerm) ? '' : 'none';
                });
            });
        }

        // Add stakeholder form
        const addForm = document.getElementById('addStakeholderForm');
        if (addForm) {
            addForm.addEventListener('submit', function(e) {
                e.preventDefault();
                console.log('📝 Add form submitted');

                const btn = document.getElementById('addStakeholderBtn');
                const spinner = btn.querySelector('.spinner-border');
                const alertDiv = document.getElementById('addStakeholderAlert');
                const errorsList = document.getElementById('addStakeholderErrors');

                alertDiv.classList.add('d-none');
                btn.disabled = true;
                spinner.classList.remove('d-none');

                const formData = new FormData(this);

                fetch('{{ route("stakeholders.store") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    console.log('📡 Response status:', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('✅ Response data:', data);
                    if (data.success) {
                        // Close add modal first
                        const addModal = bootstrap.Modal.getInstance(document.getElementById('addStakeholderModal'));
                        addModal.hide();

                        // Show success modal with reload callback
                        showSuccessModal(
                            'Stakeholder Added!',
                            'The stakeholder has been successfully created.',
                            () => location.reload()
                        );
                    } else {
                        if (data.errors) {
                            errorsList.innerHTML = '';
                            Object.values(data.errors).forEach(errors => {
                                errors.forEach(error => {
                                    const li = document.createElement('li');
                                    li.textContent = error;
                                    errorsList.appendChild(li);
                                });
                            });
                            alertDiv.classList.remove('d-none');
                        } else {
                            showErrorModal('Validation Error', data.message || 'Please check your input and try again.');
                        }
                    }
                })
                .catch(error => {
                    console.error('❌ Fetch error:', error);
                    showErrorModal('Network Error', 'Unable to connect to server. Please check your connection and try again.');
                })
                .finally(() => {
                    btn.disabled = false;
                    spinner.classList.add('d-none');
                });
            });
        }

        // Global functions
        window.viewStakeholder = function(id) {
            console.log('👁️ View stakeholder:', id);
            const modal = new bootstrap.Modal(document.getElementById('detailStakeholderModal'));
            modal.show();

            fetch(`{{ url('/stakeholders') }}/${id}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(response => {
                if (!response.ok) throw new Error('Response not ok');
                return response.json();
            })
            .then(data => {
                console.log('📄 View data received:', data);
                const content = `
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-primary">Stakeholder ID</label>
                            <input type="text" class="form-control" value="${data.stakeholder_id}" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-primary">Type</label>
                            <input type="text" class="form-control" value="${data.type}" readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-primary">Name</label>
                            <input type="text" class="form-control" value="${data.name}" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-primary">Role / Interest</label>
                            <input type="text" class="form-control" value="${data.role}" readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-primary">Influence Level</label>
                            <input type="text" class="form-control" value="${data.influence_level}" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-primary">Engagement Frequency</label>
                            <input type="text" class="form-control" value="${data.engagement_frequency}" readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-primary">Communication Method</label>
                            <input type="text" class="form-control" value="${data.communication_method}" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-primary">Responsible Person</label>
                            <input type="text" class="form-control" value="${data.responsible_person}" readonly>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-primary">Needs & Expectations</label>
                        <textarea class="form-control" rows="2" readonly>${data.needs_expectations}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-primary">Remarks</label>
                        <textarea class="form-control" rows="3" readonly>${data.remarks}</textarea>
                    </div>
                    <div class="text-center mt-4">
                        <button type="button" class="btn btn-primary px-5 py-2" data-bs-dismiss="modal" style="border-radius: 10px; min-width: 120px;">Done</button>
                    </div>
                `;
                document.getElementById('detailStakeholderContent').innerHTML = content;
            })
            .catch(error => {
                console.error('❌ View error:', error);
                document.getElementById('detailStakeholderContent').innerHTML = `
                    <div class="text-center text-danger">
                        <i class="bi bi-exclamation-triangle" style="font-size: 3rem;"></i>
                        <h5 class="mt-2">Error Loading Data</h5>
                        <p>Unable to load stakeholder details.</p>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                `;
            });
        };

        window.editStakeholder = function(id) {
            console.log('✏️ Edit stakeholder:', id);

            fetch(`{{ url('/stakeholders') }}/${id}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(response => {
                if (!response.ok) throw new Error('Response not ok');
                return response.json();
            })
            .then(data => {
                console.log('📝 Edit data received:', data);
                document.getElementById('editStakeholderId').value = data.id;
                document.getElementById('editName').value = data.name;
                document.getElementById('editType').value = data.type;
                document.getElementById('editRole').value = data.role;
                document.getElementById('editNeedsExpectations').value = data.needs_expectations;
                document.getElementById('editInfluenceLevel').value = data.influence_level;
                document.getElementById('editCommunicationMethod').value = data.communication_method;
                document.getElementById('editEngagementFrequency').value = data.engagement_frequency;
                document.getElementById('editResponsiblePerson').value = data.responsible_person;
                document.getElementById('editRemarks').value = data.remarks;

                const modal = new bootstrap.Modal(document.getElementById('editStakeholderModal'));
                modal.show();
            })
            .catch(error => {
                console.error('❌ Edit load error:', error);
                showErrorModal('Load Error', 'Unable to load stakeholder data. Please try again.');
            });
        };

        window.deleteStakeholder = function(id) {
            console.log('🗑️ Delete stakeholder:', id);

            showConfirmModal(
                'Delete Stakeholder',
                'Are you sure you want to delete this stakeholder? This action cannot be undone.',
                function() {
                    fetch(`{{ url('/stakeholders') }}/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        if (!response.ok) throw new Error('Response not ok');
                        return response.json();
                    })
                    .then(data => {
                        console.log('🗑️ Delete response:', data);
                        if (data.success) {
                            showSuccessModal(
                                'Stakeholder Deleted!',
                                'The stakeholder has been successfully deleted.',
                                () => location.reload()
                            );
                        } else {
                            showErrorModal('Delete Error', data.message || 'Unable to delete stakeholder.');
                        }
                    })
                    .catch(error => {
                        console.error('❌ Delete error:', error);
                        showErrorModal('Network Error', 'Unable to delete stakeholder. Please try again.');
                    });
                }
            );
        };

        // Edit form handler
        const editForm = document.getElementById('editStakeholderForm');
        if (editForm) {
            editForm.addEventListener('submit', function(e) {
                e.preventDefault();
                console.log('📝 Edit form submitted');

                const id = document.getElementById('editStakeholderId').value;
                const btn = document.getElementById('editStakeholderBtn');
                const spinner = btn.querySelector('.spinner-border');
                const alertDiv = document.getElementById('editStakeholderAlert');
                const errorsList = document.getElementById('editStakeholderErrors');

                alertDiv.classList.add('d-none');
                btn.disabled = true;
                spinner.classList.remove('d-none');

                const formData = new FormData(this);

                fetch(`{{ url('/stakeholders') }}/${id}`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) throw new Error('Response not ok');
                    return response.json();
                })
                .then(data => {
                    console.log('✅ Edit response:', data);
                    if (data.success) {
                        // Close edit modal first
                        const editModal = bootstrap.Modal.getInstance(document.getElementById('editStakeholderModal'));
                        editModal.hide();

                        // Show success modal with reload callback
                        showSuccessModal(
                            'Stakeholder Updated!',
                            'The stakeholder has been successfully updated.',
                            () => location.reload()
                        );
                    } else {
                        if (data.errors) {
                            errorsList.innerHTML = '';
                            Object.values(data.errors).forEach(errors => {
                                errors.forEach(error => {
                                    const li = document.createElement('li');
                                    li.textContent = error;
                                    errorsList.appendChild(li);
                                });
                            });
                            alertDiv.classList.remove('d-none');
                        } else {
                            showErrorModal('Update Error', data.message || 'Unable to update stakeholder.');
                        }
                    }
                })
                .catch(error => {
                    console.error('❌ Edit error:', error);
                    showErrorModal('Network Error', 'Unable to update stakeholder. Please try again.');
                })
                .finally(() => {
                    btn.disabled = false;
                    spinner.classList.add('d-none');
                });
            });
        }
    });
    </script>

@endsection
