@extends('layouts.dashboard')

@section('title', 'Scope & Boundaries')


<meta name="csrf-token" content="{{ csrf_token() }}">

@section('page-title', 'Scope & Boundaries')
@section('page-title-main', 'Scope & Boundaries')

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
                        <li>This worksheet includes both <strong>scope and boundaries details</strong>.</li>
                        <li class="mt-2">It should also document any items which are <strong>excluded</strong> from the scope or boundaries.</li>
                        <li class="mt-2"><span class="text-success fw-semibold">Green-highlighted cells</span> are intended for user input.</li>
                    </ul>
                </div>
            </div>
            <button class="btn-close" onclick="closeInstructions()"></button>
        </div>
    </div>
</div>

<!-- Scope List Section -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center p-4">
        <h5 class="fw-bold mb-0">Scope List</h5>
        <div class="d-flex align-items-center gap-2">
            <a style="width: 40px; height: 40px;">
                <img src="{{ asset('images/info.png') }}" alt="Info" style="width: 40px;">
            </a>
            @if(auth()->user()->hasPermission('scope-boundaries.add'))
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addScopeModal">Add Scope</button>
            @endif
              @if(auth()->user()->hasPermission('scope-boundaries.approval'))
                <a href="{{ route('admin.scopes.approval') }}" class="btn btn-outline-info">
                    Approval Page
                </a>
            @endif
            @if(auth()->user()->hasPermission('scope-boundaries.export'))
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
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="scopesTable">
                <thead class="table-light">
                    <tr>
                        <th>ID <i class="bi bi-chevron-down"></i></th>
                        <th>No</th>
                        <th>Included <i class="bi bi-chevron-down"></i></th>
                        <th>Excluded <i class="bi bi-chevron-down"></i></th>
                        <th>Rationale for excluding <i class="bi bi-chevron-down"></i></th>
                        <th>Modified <i class="bi bi-chevron-down"></i></th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($scopes as $index => $scope)
                    <tr>
                        <td class="py-3">{{ $scope->scope_id }}</td>
                        <td class="py-3">{{ $scopes->firstItem() + $index }}</td>
                        <td class="py-3">{{ strlen($scope->included) > 50 ? substr($scope->included, 0, 50) . '...' : $scope->included }}</td>
                        <td class="py-3">{{ strlen($scope->excluded) > 50 ? substr($scope->excluded, 0, 50) . '...' : $scope->excluded }}</td>
                        <td class="py-3">{{ strlen($scope->rationale_for_excluding) > 50 ? substr($scope->rationale_for_excluding, 0, 50) . '...' : $scope->rationale_for_excluding }}</td>
                        <td class="py-3">{{ $scope->updated_at->diffForHumans() }}</td>
                        <td class="py-3">
                            <div class="btn-group bg-light rounded px-1" role="group">
                                <button class="btn btn-sm btn-light border-0 text-success" onclick="viewScope({{ $scope->id }})" title="View">
                                    <i class="bi bi-eye"></i>
                                </button>
                                @if(auth()->user()->hasPermission('scope-boundaries.edit'))
                                <button class="btn btn-sm btn-light border-0 text-primary" onclick="editScope({{ $scope->id }})" title="Edit">
                                    <i class="bi bi-pencil-fill"></i>
                                </button>
                                @endif
                                @if(auth()->user()->hasPermission('scope-boundaries.delete'))
                                <button class="btn btn-sm btn-light border-0 text-danger" onclick="deleteScope({{ $scope->id }})" title="Delete">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="text-muted">
                                <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                                <h5 class="mt-2">No scopes found</h5>
                                <p>Click "Add Scope" to create your first scope.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($scopes->hasPages())
        <div class="d-flex justify-content-between align-items-center p-3">
            <div class="text-muted small">
                Showing {{ $scopes->firstItem() }} to {{ $scopes->lastItem() }} of {{ $scopes->total() }} results
            </div>
            <div>
                {{ $scopes->links() }}
            </div>
        </div>
        @else
        <div class="d-flex justify-content-end align-items-center p-3">
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted small">{{ $scopes->count() }} total</span>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Detail Scope Modal -->
<div class="modal fade" id="detailScopeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header border-0 pb-2">
                <h5 class="modal-title fw-bold text-dark">Detail Scope</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="detailScopeContent">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Scope Modal -->
<div class="modal fade" id="addScopeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header border-0 pb-2">
                <h5 class="modal-title fw-bold text-dark">Add Scope</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div id="addScopeAlert" class="alert alert-danger d-none" role="alert">
                    <ul class="mb-0" id="addScopeErrors"></ul>
                </div>
                <form id="addScopeForm">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="mb-3">
                        <label class="form-label text-primary">Included <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="included" rows="4" placeholder="Enter what is included" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-primary">Excluded <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="excluded" rows="4" placeholder="Enter what is excluded" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-primary">Rationale for excluding any source <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="rationale_for_excluding" rows="4" placeholder="Enter the rationale" required></textarea>
                    </div>
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary px-5 py-2" id="addScopeBtn" style="border-radius: 10px; min-width: 120px;">
                            <span class="spinner-border spinner-border-sm d-none me-2" role="status"></span>
                            Add Now
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Scope Modal -->
<div class="modal fade" id="editScopeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header border-0 pb-2">
                <h5 class="modal-title fw-bold text-dark">Edit Scope</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div id="editScopeAlert" class="alert alert-danger d-none" role="alert">
                    <ul class="mb-0" id="editScopeErrors"></ul>
                </div>
                <form id="editScopeForm">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" id="editScopeId" name="scope_id">

                    <div class="mb-3">
                        <label class="form-label text-primary">Included <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="included" id="editIncluded" rows="4" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-primary">Excluded <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="excluded" id="editExcluded" rows="4" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-primary">Rationale for excluding any source <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="rationale_for_excluding" id="editRationale" rows="4" required></textarea>
                    </div>
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary px-5 py-2" id="editScopeBtn" style="border-radius: 10px; min-width: 120px;">
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
        console.log('🚀 Scopes page loaded');

        // Get CSRF token
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
                const tableRows = document.querySelectorAll('#scopesTable tbody tr');

                tableRows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(searchTerm) ? '' : 'none';
                });
            });
        }

        // Add scope form
        const addForm = document.getElementById('addScopeForm');
        if (addForm) {
            addForm.addEventListener('submit', function(e) {
                e.preventDefault();
                console.log('📝 Add form submitted');

                const btn = document.getElementById('addScopeBtn');
                const spinner = btn.querySelector('.spinner-border');
                const alertDiv = document.getElementById('addScopeAlert');
                const errorsList = document.getElementById('addScopeErrors');

                alertDiv.classList.add('d-none');
                btn.disabled = true;
                spinner.classList.remove('d-none');

                const formData = new FormData(this);

                fetch('{{ route("scope-boundaries.store") }}', {
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
                        const addModal = bootstrap.Modal.getInstance(document.getElementById('addScopeModal'));
                        addModal.hide();

                        showSuccessModal(
                            'Scope Added!',
                            'The scope has been successfully created.',
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
        window.viewScope = function(id) {
            console.log('👁️ View scope:', id);
            const modal = new bootstrap.Modal(document.getElementById('detailScopeModal'));
            modal.show();

            fetch(`{{ url('/scope-boundaries') }}/${id}`, {
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
                    <div class="mb-3">
                        <label class="form-label text-primary">Scope ID</label>
                        <input type="text" class="form-control" value="${data.scope_id}" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-primary">Included</label>
                        <textarea class="form-control" rows="4" readonly>${data.included}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-primary">Excluded</label>
                        <textarea class="form-control" rows="4" readonly>${data.excluded}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-primary">Rationale for excluding any source</label>
                        <textarea class="form-control" rows="4" readonly>${data.rationale_for_excluding}</textarea>
                    </div>
                    <div class="text-center mt-4">
                        <button type="button" class="btn btn-primary px-5 py-2" data-bs-dismiss="modal" style="border-radius: 10px; min-width: 120px;">Done</button>
                    </div>
                `;
                document.getElementById('detailScopeContent').innerHTML = content;
            })
            .catch(error => {
                console.error('❌ View error:', error);
                document.getElementById('detailScopeContent').innerHTML = `
                    <div class="text-center text-danger">
                        <i class="bi bi-exclamation-triangle" style="font-size: 3rem;"></i>
                        <h5 class="mt-2">Error Loading Data</h5>
                        <p>Unable to load scope details.</p>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                `;
            });
        };

        window.editScope = function(id) {
            console.log('✏️ Edit scope:', id);

            fetch(`{{ url('/scope-boundaries') }}/${id}`, {
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
                document.getElementById('editScopeId').value = data.id;
                document.getElementById('editIncluded').value = data.included;
                document.getElementById('editExcluded').value = data.excluded;
                document.getElementById('editRationale').value = data.rationale_for_excluding;

                const modal = new bootstrap.Modal(document.getElementById('editScopeModal'));
                modal.show();
            })
            .catch(error => {
                console.error('❌ Edit load error:', error);
                showErrorModal('Load Error', 'Unable to load scope data. Please try again.');
            });
        };

        window.deleteScope = function(id) {
            console.log('🗑️ Delete scope:', id);

            showConfirmModal(
                'Delete Scope',
                'Are you sure you want to delete this scope? This action cannot be undone.',
                function() {
                    fetch(`{{ url('/scope-boundaries') }}/${id}`, {
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
                                'Scope Deleted!',
                                'The scope has been successfully deleted.',
                                () => location.reload()
                            );
                        } else {
                            showErrorModal('Delete Error', data.message || 'Unable to delete scope.');
                        }
                    })
                    .catch(error => {
                        console.error('❌ Delete error:', error);
                        showErrorModal('Network Error', 'Unable to delete scope. Please try again.');
                    });
                }
            );
        };

        // Edit form handler
        const editForm = document.getElementById('editScopeForm');
        if (editForm) {
            editForm.addEventListener('submit', function(e) {
                e.preventDefault();
                console.log('📝 Edit form submitted');

                const id = document.getElementById('editScopeId').value;
                const btn = document.getElementById('editScopeBtn');
                const spinner = btn.querySelector('.spinner-border');
                const alertDiv = document.getElementById('editScopeAlert');
                const errorsList = document.getElementById('editScopeErrors');

                alertDiv.classList.add('d-none');
                btn.disabled = true;
                spinner.classList.remove('d-none');

                const formData = new FormData(this);

                fetch(`{{ url('/scope-boundaries') }}/${id}`, {
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
                        const editModal = bootstrap.Modal.getInstance(document.getElementById('editScopeModal'));
                        editModal.hide();

                        showSuccessModal(
                            'Scope Updated!',
                            'The scope has been successfully updated.',
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
                            showErrorModal('Update Error', data.message || 'Unable to update scope.');
                        }
                    }
                })
                .catch(error => {
                    console.error('❌ Edit error:', error);
                    showErrorModal('Network Error', 'Unable to update scope. Please try again.');
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
