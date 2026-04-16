@extends('layouts.dashboard')

@section('title', 'Risks & Opportunities')


<meta name="csrf-token" content="{{ csrf_token() }}">

@section('page-title', 'Internal & External Issues')
@section('page-title-main', 'Risks & Opportunities')

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
                    <ul class="mb-0 text-dark small lh-base">
                        <li>Please use this section to document the external and internal strategic issues (label it as Risk or Opportunity) that affect your organization's ability to improve its energy performance and achieve the intended outcomes of the EnMS.</li>
                        <li class="mt-2">For each risk or opportunity identified, please rate its likelihood of occurrence, risk level (only for Risk), and impact on EnMS.</li>
                    </ul>
                </div>
            </div>
            <button class="btn-close" onclick="closeInstructions()"></button>
        </div>
    </div>
</div>

<!-- Action Buttons -->
<div class="d-flex justify-content-end mb-4">
    <div class="d-flex gap-2">
        <button class="btn btn-outline-primary d-none" data-bs-toggle="modal" data-bs-target="#swotAnalysisModal">
        SWOT Analysis
        </button>
        @if(auth()->user()->hasPermission('internal-external-issues.add'))
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRiskModal">
            Add Risk & Opportunities
        </button>
        @endif
       @if(auth()->user()->hasPermission('internal-external-issues.approval'))
            <a href="{{ route('admin.risks.approval') }}" class="btn btn-outline-info">
                Approval Page
            </a>
        @endif

        
        @if(auth()->user()->hasPermission('internal-external-issues.export'))
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

<!-- Risk and Opportunities Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center p-4">
        <h5 class="fw-bold mb-0">Risk and Opportunities of EnMS</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="risksTable">
                <thead class="table-light">
                    <tr>
                        <th>No <i class="bi bi-chevron-down"></i></th>
                        <th>Issue <i class="bi bi-chevron-down"></i></th>
                        <th>Internal or External <i class="bi bi-chevron-down"></i></th>
                        <th>Risk or Opportunity <i class="bi bi-chevron-down"></i></th>
                        <th>Likelihood <i class="bi bi-chevron-down"></i></th>
                        <th>Risk Level <i class="bi bi-chevron-down"></i></th>
                        <th>Modified <i class="bi bi-chevron-down"></i></th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($risks ?? [] as $index => $risk)
                    <tr>
                        <td class="py-3">{{ $loop->iteration }}</td>
                        <td class="py-3">{{ $risk->issue ?? 'No issue' }}</td>
                        <td class="py-3">
                            <span class="badge {{ ($risk->type ?? 'External') == 'External' ? 'bg-info' : 'bg-success' }}">
                                {{ $risk->type ?? 'External' }}
                            </span>
                        </td>
                        <td class="py-3">
                            <span class="badge {{ ($risk->category ?? 'Risk') == 'Risk' ? 'bg-danger' : 'bg-primary' }}">
                                {{ $risk->category ?? 'Risk' }}
                            </span>
                        </td>
                        <td class="py-3">{{ $risk->likelihood ?? 1 }}</td>
                        <td class="py-3">
                            <span class="badge
                                @if(($risk->risk_level ?? 'Medium') == 'Low') bg-info
                                @elseif(($risk->risk_level ?? 'Medium') == 'Medium') bg-warning
                                @else bg-danger @endif">
                                {{ $risk->risk_level ?? 'Medium' }}
                            </span>
                        </td>
                        <td class="py-3">{{ $risk->updated_at ? $risk->updated_at->diffForHumans() : 'Never' }}</td>
                        <td class="py-3">
                            <div class="d-flex gap-1">
                                <button class="btn btn-sm btn-primary" onclick="viewRisk({{ $risk->id }})" title="View" style="padding: 4px 8px;">
                                    <i class="bi bi-eye"></i>
                                </button>
                                @if(auth()->user()->hasPermission('internal-external-issues.edit'))
                                <button class="btn btn-sm btn-warning" onclick="editRisk({{ $risk->id }})" title="Edit" style="padding: 4px 8px;">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                @endif
                                <button class="btn btn-sm btn-success" onclick="viewActions({{ $risk->id }})" title="View Actions" style="padding: 4px 8px;">
                                    <i class="bi bi-list-task"></i>
                                </button>
                                @if(auth()->user()->hasPermission('internal-external-issues.delete'))
                                <button class="btn btn-sm btn-danger" onclick="deleteRisk({{ $risk->id }})" title="Delete" style="padding: 4px 8px;">
                                    <i class="bi bi-trash"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <div class="text-muted">
                                <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                                <h5 class="mt-2">No risks/opportunities found</h5>
                                <p>Click "Add Risk & Opportunities" to create your first entry.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if(isset($risks) && $risks->hasPages())
        <div class="d-flex justify-content-between align-items-center p-3">
            <div class="text-muted small">
                Showing {{ $risks->firstItem() }} to {{ $risks->lastItem() }} of {{ $risks->total() }} results
            </div>
            <div>
                {{ $risks->links() }}
            </div>
        </div>
        @else
        <div class="d-flex justify-content-end align-items-center p-3">
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted small">{{ isset($risks) ? $risks->count() : 0 }} total</span>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Add Risk Modal -->
<div class="modal fade" id="addRiskModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header border-0 pb-2">
                <h5 class="modal-title fw-bold text-dark">Add Risk and Opportunities of EnMS</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div id="addRiskAlert" class="alert alert-danger d-none" role="alert">
                    <ul class="mb-0" id="addRiskErrors"></ul>
                </div>
                <form id="addRiskForm">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">

                    <div class="mb-3">
                        <label class="form-label text-primary">Issue <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="issue" rows="3" placeholder="Describe the issue in detail" required></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-primary">Internal or External <span class="text-danger">*</span></label>
                            <select class="form-select" name="type" required>
                                <option value="">Choose...</option>
                                <option value="Internal">Internal</option>
                                <option value="External">External</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-primary">Risk or Opportunity <span class="text-danger">*</span></label>
                            <select class="form-select" name="category" required>
                                <option value="">Choose...</option>
                                <option value="Risk">Risk</option>
                                <option value="Opportunity">Opportunity</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-primary">Likelihood <span class="text-danger">*</span></label>
                            <select class="form-select" name="likelihood" required>
                                <option value="">Choose...</option>
                                <option value="1">1 - Rare</option>
                                <option value="2">2 - Unlikely</option>
                                <option value="3">3 - Possible</option>
                                <option value="4">4 - Likely</option>
                                <option value="5">5 - Almost Certain</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-primary">Risk Level <span class="text-danger">*</span></label>
                            <select class="form-select" name="risk_level" required>
                                <option value="">Choose...</option>
                                <option value="Low">Low</option>
                                <option value="Medium">Medium</option>
                                <option value="High">High</option>
                            </select>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary px-5 py-2" id="addRiskBtn" style="border-radius: 10px; min-width: 120px;">
                            <span class="spinner-border spinner-border-sm d-none me-2" role="status"></span>
                            Add Now
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

<!-- Edit Risk Modal -->
<div class="modal fade" id="editRiskModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header border-0 pb-2">
                <h5 class="modal-title fw-bold text-dark">Edit Risk & Opportunity</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div id="editRiskAlert" class="alert alert-danger d-none" role="alert">
                    <ul class="mb-0" id="editRiskErrors"></ul>
                </div>
                <form id="editRiskForm">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" id="editRiskId" name="risk_id">

                    <div class="mb-3">
                        <label class="form-label text-primary">Issue <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="issue" id="editIssue" rows="3" required></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-primary">Internal or External <span class="text-danger">*</span></label>
                            <select class="form-select" name="type" id="editType" required>
                                <option value="">Choose...</option>
                                <option value="Internal">Internal</option>
                                <option value="External">External</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-primary">Risk or Opportunity <span class="text-danger">*</span></label>
                            <select class="form-select" name="category" id="editCategory" required>
                                <option value="">Choose...</option>
                                <option value="Risk">Risk</option>
                                <option value="Opportunity">Opportunity</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-primary">Likelihood <span class="text-danger">*</span></label>
                            <select class="form-select" name="likelihood" id="editLikelihood" required>
                                <option value="">Choose...</option>
                                <option value="1">1 - Rare</option>
                                <option value="2">2 - Unlikely</option>
                                <option value="3">3 - Possible</option>
                                <option value="4">4 - Likely</option>
                                <option value="5">5 - Almost Certain</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-primary">Risk Level <span class="text-danger">*</span></label>
                            <select class="form-select" name="risk_level" id="editRiskLevel" required>
                                <option value="">Choose...</option>
                                <option value="Low">Low</option>
                                <option value="Medium">Medium</option>
                                <option value="High">High</option>
                            </select>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary px-5 py-2" id="editRiskBtn" style="border-radius: 10px; min-width: 120px;">
                            <span class="spinner-border spinner-border-sm d-none me-2" role="status"></span>
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Detail Risk Modal -->
<div class="modal fade" id="detailRiskModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header border-0 pb-2">
                <h5 class="modal-title fw-bold text-dark">Detail Risk & Opportunity</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="detailRiskContent">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
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
        console.log('🚀 Risks & Opportunities page loaded');

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
        console.log('🔐 CSRF Token:', csrfToken ? 'Found' : 'Missing');

        // Modal Functions
        function showSuccessModal(title, message, callback = null) {
            document.getElementById('successTitle').textContent = title;
            document.getElementById('successMessage').textContent = message;
            const modal = new bootstrap.Modal(document.getElementById('successModal'));
            modal.show();

            if (callback) {
                document.getElementById('successModal').addEventListener('hidden.bs.modal', callback, { once: true });
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
                const tableRows = document.querySelectorAll('#risksTable tbody tr');

                tableRows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(searchTerm) ? '' : 'none';
                });
            });
        }

        // View Risk
        window.viewRisk = function(id) {
            console.log('👁️ View risk:', id);
            const modal = new bootstrap.Modal(document.getElementById('detailRiskModal'));
            modal.show();

            fetch(`/internal-external-issues/${id}`, {
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
                const risk = data.data;
                const content = `
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-primary">Risk ID</label>
                            <input type="text" class="form-control" value="${risk.risk_id}" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-primary">Type</label>
                            <input type="text" class="form-control" value="${risk.type}" readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-primary">Category</label>
                            <input type="text" class="form-control" value="${risk.category}" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-primary">Risk Level</label>
                            <input type="text" class="form-control" value="${risk.risk_level}" readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-primary">Likelihood</label>
                            <input type="text" class="form-control" value="${risk.likelihood}" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-primary">Status</label>
                            <input type="text" class="form-control" value="${risk.status || 'Active'}" readonly>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-primary">Issue</label>
                        <textarea class="form-control" rows="3" readonly>${risk.issue}</textarea>
                    </div>
                    <div class="text-center mt-4">
                        <button type="button" class="btn btn-primary px-5 py-2" data-bs-dismiss="modal" style="border-radius: 10px; min-width: 120px;">Done</button>
                    </div>
                `;
                document.getElementById('detailRiskContent').innerHTML = content;
            })
            .catch(error => {
                console.error('❌ View error:', error);
                document.getElementById('detailRiskContent').innerHTML = `
                    <div class="text-center text-danger">
                        <i class="bi bi-exclamation-triangle" style="font-size: 3rem;"></i>
                        <h5 class="mt-2">Error Loading Data</h5>
                        <p>Unable to load risk/opportunity details.</p>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                `;
            });
        };

        // Edit Risk
        window.editRisk = function(id) {
            console.log('✏️ Edit risk:', id);

            fetch(`/internal-external-issues/${id}`, {
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
                const risk = data.data;

                document.getElementById('editRiskId').value = risk.id;
                document.getElementById('editIssue').value = risk.issue;
                document.getElementById('editType').value = risk.type;
                document.getElementById('editCategory').value = risk.category;
                document.getElementById('editLikelihood').value = risk.likelihood;
                document.getElementById('editRiskLevel').value = risk.risk_level;

                const modal = new bootstrap.Modal(document.getElementById('editRiskModal'));
                modal.show();
            })
            .catch(error => {
                console.error('❌ Edit load error:', error);
                showErrorModal('Load Error', 'Unable to load risk/opportunity data. Please try again.');
            });
        };

        // Delete Risk
        window.deleteRisk = function(id) {
            console.log('🗑️ Delete risk:', id);

            showConfirmModal(
                'Delete Risk/Opportunity',
                'Are you sure you want to delete this risk/opportunity? This action cannot be undone.',
                function() {
                    fetch(`/internal-external-issues/${id}`, {
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
                                'Risk/Opportunity Deleted!',
                                'The risk/opportunity has been successfully deleted.',
                                () => location.reload()
                            );
                        } else {
                            showErrorModal('Delete Error', data.message || 'Unable to delete risk/opportunity.');
                        }
                    })
                    .catch(error => {
                        console.error('❌ Delete error:', error);
                        showErrorModal('Network Error', 'Unable to delete risk/opportunity. Please try again.');
                    });
                }
            );
        };

        // View Actions
        window.viewActions = function(id) {
            console.log('📋 View actions for risk:', id);
            // Navigate to risk actions page (implement later)
            alert('View actions functionality - navigate to actions page for risk ID: ' + id);
        };

        // Add risk form
        const addForm = document.getElementById('addRiskForm');
        if (addForm) {
            addForm.addEventListener('submit', function(e) {
                e.preventDefault();
                console.log('📝 Add risk form submitted');

                const btn = document.getElementById('addRiskBtn');
                const spinner = btn.querySelector('.spinner-border');
                const alertDiv = document.getElementById('addRiskAlert');
                const errorsList = document.getElementById('addRiskErrors');

                alertDiv.classList.add('d-none');
                btn.disabled = true;
                spinner.classList.remove('d-none');

                const formData = new FormData(this);

                fetch('/internal-external-issues', {
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
                        const modal = bootstrap.Modal.getInstance(document.getElementById('addRiskModal'));
                        modal.hide();
                        this.reset();

                        showSuccessModal(
                            'Risk/Opportunity Added!',
                            'The risk/opportunity has been successfully created.',
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

        // Edit form handler
        const editForm = document.getElementById('editRiskForm');
        if (editForm) {
            editForm.addEventListener('submit', function(e) {
                e.preventDefault();
                console.log('📝 Edit form submitted');

                const id = document.getElementById('editRiskId').value;
                const btn = document.getElementById('editRiskBtn');
                const spinner = btn.querySelector('.spinner-border');
                const alertDiv = document.getElementById('editRiskAlert');
                const errorsList = document.getElementById('editRiskErrors');

                alertDiv.classList.add('d-none');
                btn.disabled = true;
                spinner.classList.remove('d-none');

                const formData = new FormData(this);

                fetch(`/internal-external-issues/${id}`, {
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
                        const editModal = bootstrap.Modal.getInstance(document.getElementById('editRiskModal'));
                        editModal.hide();

                        showSuccessModal(
                            'Risk/Opportunity Updated!',
                            'The risk/opportunity has been successfully updated.',
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
                            showErrorModal('Update Error', data.message || 'Unable to update risk/opportunity.');
                        }
                    }
                })
                .catch(error => {
                    console.error('❌ Edit error:', error);
                    showErrorModal('Network Error', 'Unable to update risk/opportunity. Please try again.');
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
