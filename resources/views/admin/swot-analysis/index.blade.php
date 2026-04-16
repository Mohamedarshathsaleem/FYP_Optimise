{{-- resources/views/admin/swot-analysis/index.blade.php --}}
@extends('layouts.dashboard')

@section('title', 'SWOT Analysis')

<!-- CSRF Token Meta Tag -->
<meta name="csrf-token" content="{{ csrf_token() }}">

@section('page-title', 'SWOT Analysis')
@section('page-title-main', 'SWOT Analysis')

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
                    <p class="mb-2 text-dark">Please fill in the form by providing your insights under each section:</p>
                    <ul class="mb-0 text-dark lh-base">
                        <li><strong>Strengths</strong> – List the internal advantages, skills, or resources that give you or your organization an edge.</li>
                        <li><strong>Weaknesses</strong> – Identify internal areas that need improvement or may put you at a disadvantage.</li>
                        <li><strong>Opportunities</strong> – Highlight external factors or trends that you can take advantage of.</li>
                        <li><strong>Threats</strong> – Note external challenges or risks that may affect success.</li>
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
         @if(auth()->user()->hasPermission('swot-analysis.approval'))
            <a href="{{ route('admin.risks.approval') }}" class="btn btn-outline-info">
                Approval Page
            </a>
        @endif

        
        @if(auth()->user()->hasPermission('swot-analysis.export'))
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
        @if(auth()->user()->hasPermission('swot-analysis.add'))
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSwotModal">
           Add SWOT Analysis
        </button>
        @endif
    </div>
</div>

<!-- SWOT Analysis List -->
@forelse($swotAnalyses ?? [] as $swot)
<div class="card border-0 shadow-sm mb-4" id="swot-{{ $swot->id }}">
    <div class="card-header bg-white d-flex justify-content-between align-items-center p-4">
        <div>
            <h5 class="fw-bold mb-1">{{ $swot->title ?: 'SWOT Analysis' }}</h5>
            <small class="text-muted">{{ $swot->swot_id }} • Created {{ $swot->created_at->diffForHumans() }}</small>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <span class="badge {{ $swot->status_badge }}">{{ $swot->status }}</span>
            <div class="d-flex gap-1">
                <button class="btn btn-sm btn-primary" onclick="viewSwot({{ $swot->id }})" title="View">
                    <i class="bi bi-eye"></i>
                </button>
                @if(auth()->user()->hasPermission('swot-analysis.edit'))
                <button class="btn btn-sm btn-warning" onclick="editSwot({{ $swot->id }})" title="Edit">
                    <i class="bi bi-pencil"></i>
                </button>
                @endif
                @if(auth()->user()->hasPermission('swot-analysis.delete'))
                <button class="btn btn-sm btn-danger" onclick="deleteSwot({{ $swot->id }})" title="Delete">
                    <i class="bi bi-trash"></i>
                </button>
                @endif
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <!-- Internal Factors -->
        <div class="table-responsive">
            <table class="table table-bordered mb-0">
                <thead>
                    <tr class="bg-primary text-white">
                        <th class="fw-bold py-3 px-4" style="width: 20%;">Internal Factors</th>
                        <th class="fw-bold py-3 px-4">Description</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Strengths -->
                    <tr>
                        <td class="bg-primary text-white fw-bold py-4 px-4 align-top">
                            <h6 class="mb-0">Strengths</h6>
                        </td>
                        <td class="py-4 px-4">
                            <div class="swot-content">
                                @foreach($swot->strengths_array as $strength)
                                    <div class="mb-2">- {{ $strength }}</div>
                                @endforeach
                            </div>
                        </td>
                    </tr>
                    <!-- Weaknesses -->
                    <tr>
                        <td class="bg-primary text-white fw-bold py-4 px-4 align-top">
                            <h6 class="mb-0">Weaknesses</h6>
                        </td>
                        <td class="py-4 px-4">
                            <div class="swot-content">
                                @foreach($swot->weaknesses_array as $weakness)
                                    <div class="mb-2">- {{ $weakness }}</div>
                                @endforeach
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- External Factors -->
        <div class="table-responsive">
            <table class="table table-bordered mb-0">
                <thead>
                    <tr class="bg-primary text-white">
                        <th class="fw-bold py-3 px-4" style="width: 20%;">External Factors</th>
                        <th class="fw-bold py-3 px-4">Description</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Opportunities -->
                    <tr>
                        <td class="bg-primary text-white fw-bold py-4 px-4 align-top">
                            <h6 class="mb-0">Opportunities</h6>
                        </td>
                        <td class="py-4 px-4">
                            <div class="swot-content">
                                @foreach($swot->opportunities_array as $opportunity)
                                    <div class="mb-2">- {{ $opportunity }}</div>
                                @endforeach
                            </div>
                        </td>
                    </tr>
                    <!-- Threats -->
                    <tr>
                        <td class="bg-primary text-white fw-bold py-4 px-4 align-top">
                            <h6 class="mb-0">Threats</h6>
                        </td>
                        <td class="py-4 px-4">
                            <div class="swot-content">
                                @foreach($swot->threats_array as $threat)
                                    <div class="mb-2">- {{ $threat }}</div>
                                @endforeach
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@empty
<div class="card border-0 shadow-sm">
    <div class="card-body text-center py-5">
        <i class="bi bi-graph-up-arrow" style="font-size: 3rem; color: #6c757d;"></i>
        <h5 class="mt-3 text-muted">No SWOT Analysis found</h5>
        <p class="text-muted">Click "Add SWOT Analysis" to create your first analysis.</p>
    </div>
</div>
@endforelse

<!-- Pagination -->
@if(isset($swotAnalyses) && $swotAnalyses->hasPages())
<div class="d-flex justify-content-center mt-4">
    {{ $swotAnalyses->links() }}
</div>
@endif

<!-- Add SWOT Modal -->
<div class="modal fade" id="addSwotModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header border-0 pb-2">
                <h5 class="modal-title fw-bold text-dark">Add SWOT Analysis</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div id="addSwotAlert" class="alert alert-danger d-none" role="alert">
                    <ul class="mb-0" id="addSwotErrors"></ul>
                </div>
                <form id="addSwotForm">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">

                    <div class="mb-3">
                        <label class="form-label text-primary">Title (Optional)</label>
                        <input type="text" class="form-control" name="title" placeholder="Enter SWOT Analysis title">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-primary">Strengths <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="strengths" rows="5" placeholder="List internal advantages, skills, or resources (one per line)" required></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-primary">Weaknesses <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="weaknesses" rows="5" placeholder="Identify internal areas that need improvement (one per line)" required></textarea>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-primary">Opportunities <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="opportunities" rows="5" placeholder="Highlight external factors or trends (one per line)" required></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-primary">Threats <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="threats" rows="5" placeholder="Note external challenges or risks (one per line)" required></textarea>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-primary">Notes (Optional)</label>
                        <textarea class="form-control" name="notes" rows="3" placeholder="Additional notes or comments"></textarea>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary px-5 py-2" id="addSwotBtn" style="border-radius: 10px; min-width: 120px;">
                            <span class="spinner-border spinner-border-sm d-none me-2" role="status"></span>
                            Add Now
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit SWOT Modal -->
<div class="modal fade" id="editSwotModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header border-0 pb-2">
                <h5 class="modal-title fw-bold text-dark">Edit SWOT Analysis</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div id="editSwotAlert" class="alert alert-danger d-none" role="alert">
                    <ul class="mb-0" id="editSwotErrors"></ul>
                </div>
                <form id="editSwotForm">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" id="editSwotId" name="swot_id">

                    <div class="mb-3">
                        <label class="form-label text-primary">Title (Optional)</label>
                        <input type="text" class="form-control" name="title" id="editTitle" placeholder="Enter SWOT Analysis title">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-primary">Strengths <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="strengths" id="editStrengths" rows="5" required></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-primary">Weaknesses <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="weaknesses" id="editWeaknesses" rows="5" required></textarea>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-primary">Opportunities <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="opportunities" id="editOpportunities" rows="5" required></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-primary">Threats <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="threats" id="editThreats" rows="5" required></textarea>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-primary">Notes (Optional)</label>
                        <textarea class="form-control" name="notes" id="editNotes" rows="3"></textarea>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary px-5 py-2" id="editSwotBtn" style="border-radius: 10px; min-width: 120px;">
                            <span class="spinner-border spinner-border-sm d-none me-2" role="status"></span>
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Detail SWOT Modal -->
<div class="modal fade" id="detailSwotModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header border-0 pb-2">
                <h5 class="modal-title fw-bold text-dark">SWOT Analysis Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="detailSwotContent">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
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

<style>
.swot-content {
    line-height: 1.7;
    color: #495057;
    font-size: 0.95rem;
}

.table-bordered {
    border: 1px solid #dee2e6;
}

.table-bordered td, .table-bordered th {
    border: 1px solid #dee2e6;
    vertical-align: top;
}

.bg-primary {
    background-color: #2563eb !important;
}

.card {
    border-radius: 12px;
}

.search-box {
    max-width: 300px;
}

@media (max-width: 768px) {
    .swot-content {
        font-size: 0.9rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 SWOT Analysis page loaded');

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
            const cards = document.querySelectorAll('[id^="swot-"]');

            cards.forEach(card => {
                const text = card.textContent.toLowerCase();
                card.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    }

    // View SWOT
    window.viewSwot = function(id) {
        console.log('👁️ View SWOT:', id);
        const modal = new bootstrap.Modal(document.getElementById('detailSwotModal'));
        modal.show();

        fetch(`/swot-analysis/${id}`, {
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
            const swot = data.data;
            const content = `
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label text-primary">SWOT ID</label>
                        <input type="text" class="form-control" value="${swot.swot_id}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-primary">Title</label>
                        <input type="text" class="form-control" value="${swot.title || 'N/A'}" readonly>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-primary">Strengths</label>
                        <textarea class="form-control" rows="5" readonly>${swot.strengths}</textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-primary">Weaknesses</label>
                        <textarea class="form-control" rows="5" readonly>${swot.weaknesses}</textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-primary">Opportunities</label>
                        <textarea class="form-control" rows="5" readonly>${swot.opportunities}</textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-primary">Threats</label>
                        <textarea class="form-control" rows="5" readonly>${swot.threats}</textarea>
                    </div>
                </div>
                ${swot.notes ? `
                <div class="mb-3">
                    <label class="form-label text-primary">Notes</label>
                    <textarea class="form-control" rows="3" readonly>${swot.notes}</textarea>
                </div>
                ` : ''}
                <div class="text-center mt-4">
                    <button type="button" class="btn btn-primary px-5 py-2" data-bs-dismiss="modal" style="border-radius: 10px; min-width: 120px;">Done</button>
                </div>
            `;
            document.getElementById('detailSwotContent').innerHTML = content;
        })
        .catch(error => {
            console.error('❌ View error:', error);
            document.getElementById('detailSwotContent').innerHTML = `
                <div class="text-center text-danger">
                    <i class="bi bi-exclamation-triangle" style="font-size: 3rem;"></i>
                    <h5 class="mt-2">Error Loading Data</h5>
                    <p>Unable to load SWOT Analysis details.</p>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            `;
        });
    };

    // Edit SWOT
    window.editSwot = function(id) {
        console.log('✏️ Edit SWOT:', id);

        fetch(`/swot-analysis/${id}`, {
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
            const swot = data.data;

            document.getElementById('editSwotId').value = swot.id;
            document.getElementById('editTitle').value = swot.title || '';
            document.getElementById('editStrengths').value = swot.strengths;
            document.getElementById('editWeaknesses').value = swot.weaknesses;
            document.getElementById('editOpportunities').value = swot.opportunities;
            document.getElementById('editThreats').value = swot.threats;
            document.getElementById('editNotes').value = swot.notes || '';

            const modal = new bootstrap.Modal(document.getElementById('editSwotModal'));
            modal.show();
        })
        .catch(error => {
            console.error('❌ Edit load error:', error);
            showErrorModal('Load Error', 'Unable to load SWOT Analysis data. Please try again.');
        });
    };

    // Delete SWOT
    window.deleteSwot = function(id) {
        console.log('🗑️ Delete SWOT:', id);

        showConfirmModal(
            'Delete SWOT Analysis',
            'Are you sure you want to delete this SWOT Analysis? This action cannot be undone.',
            function() {
                fetch(`/swot-analysis/${id}`, {
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
                            'SWOT Analysis Deleted!',
                            'The SWOT Analysis has been successfully deleted.',
                            () => location.reload()
                        );
                    } else {
                        showErrorModal('Delete Error', data.message || 'Unable to delete SWOT Analysis.');
                    }
                })
                .catch(error => {
                    console.error('❌ Delete error:', error);
                    showErrorModal('Network Error', 'Unable to delete SWOT Analysis. Please try again.');
                });
            }
        );
    };

    // Add SWOT form
    const addForm = document.getElementById('addSwotForm');
    if (addForm) {
        addForm.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('📝 Add SWOT form submitted');

            const btn = document.getElementById('addSwotBtn');
            const spinner = btn.querySelector('.spinner-border');
            const alertDiv = document.getElementById('addSwotAlert');
            const errorsList = document.getElementById('addSwotErrors');

            alertDiv.classList.add('d-none');
            btn.disabled = true;
            spinner.classList.remove('d-none');

            const formData = new FormData(this);

            fetch('/swot-analysis', {
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
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addSwotModal'));
                    modal.hide();
                    this.reset();

                    showSuccessModal(
                        'SWOT Analysis Added!',
                        'The SWOT Analysis has been successfully created.',
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
    const editForm = document.getElementById('editSwotForm');
    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('📝 Edit SWOT form submitted');

            const id = document.getElementById('editSwotId').value;
            const btn = document.getElementById('editSwotBtn');
            const spinner = btn.querySelector('.spinner-border');
            const alertDiv = document.getElementById('editSwotAlert');
            const errorsList = document.getElementById('editSwotErrors');

            alertDiv.classList.add('d-none');
            btn.disabled = true;
            spinner.classList.remove('d-none');

            const formData = new FormData(this);

            fetch(`/swot-analysis/${id}`, {
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
                    const editModal = bootstrap.Modal.getInstance(document.getElementById('editSwotModal'));
                    editModal.hide();

                    showSuccessModal(
                        'SWOT Analysis Updated!',
                        'The SWOT Analysis has been successfully updated.',
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
                        showErrorModal('Update Error', data.message || 'Unable to update SWOT Analysis.');
                    }
                }
            })
            .catch(error => {
                console.error('❌ Edit error:', error);
                showErrorModal('Network Error', 'Unable to update SWOT Analysis. Please try again.');
            })
            .finally(() => {
                btn.disabled = false;
                spinner.classList.add('d-none');
            });
        });
    }

    // Export functionality
    const exportItems = document.querySelectorAll('.dropdown-item');
    exportItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const exportType = this.textContent.trim().split(' ')[1];
            alert(`Export to ${exportType} - Feature coming soon!`);
        });
    });
});
</script>

@endsection
