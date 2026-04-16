@extends('layouts.dashboard')

@section('title', 'Motivation Strategy')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <p class="text-secondary small mb-1">Action Plan / Action Plan Development / Motivation Strategy</p>
        <h3 class="fw-bold">Motivation Strategy</h3>
    </div>
    <div class="d-flex align-items-center">
        <div class="input-group search-box me-3">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="text" class="form-control" placeholder="Search" name="search">
            <img src="{{ asset('images/user.png') }}" class="rounded-circle" alt="User"
                 style="width: 40px; height: 40px;">
        </div>
    </div>
</div>

<p class="fw-bold text-primary-light">User Dashboard</p>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="card-title fw-bold">Motivation Strategy List</h5>
            <div>
                <button type="button"
                        class="btn btn-sm btn-primary-light"
                        data-bs-toggle="modal"
                        data-bs-target="#motivationModal">
                    Add Motivation
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Motivation Activity</th>
                        <th>Target Group</th>
                        <th>Criteria for Recognition</th>
                        <th>Recognition Method</th>
                        <th>Frequency</th>
                        <th>Responsible Dept.</th>
                        <th>Remarks</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($motivations as $index => $item)
                        <tr>
                            <td>{{ $index + 1 + ($motivations->currentPage() - 1) * $motivations->perPage() }}</td>
                            <td>{{ $item->motivation_activity }}</td>
                            <td>{{ $item->target_group }}</td>
                            <td>{{ $item->criteria_for_recognition }}</td>
                            <td>{{ $item->recognition_method }}</td>
                            <td>{{ $item->frequency }}</td>
                            <td>{{ $item->responsible_dept }}</td>
                            <td>{{ $item->remarks }}</td>
                            <td>
                                <!-- Detail Button -->
                                <button type="button"
                                        class="btn btn-sm btn-outline-info"
                                        data-bs-toggle="modal"
                                        data-bs-target="#detailModal"
                                        data-id="{{ $item->id }}"
                                        data-activity="{{ $item->motivation_activity }}"
                                        data-target="{{ $item->target_group }}"
                                        data-criteria="{{ $item->criteria_for_recognition }}"
                                        data-method="{{ $item->recognition_method }}"
                                        data-frequency="{{ $item->frequency }}"
                                        data-responsible="{{ $item->responsible_dept }}"
                                        data-remarks="{{ $item->remarks ?? '-' }}">
                                    Detail
                                </button>

                                <!-- Edit Button -->
                                <button type="button"
                                        class="btn btn-sm btn-outline-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editModal"
                                        data-id="{{ $item->id }}"
                                        data-activity="{{ $item->motivation_activity }}"
                                        data-target="{{ $item->target_group }}"
                                        data-criteria="{{ $item->criteria_for_recognition }}"
                                        data-method="{{ $item->recognition_method }}"
                                        data-frequency="{{ $item->frequency }}"
                                        data-responsible="{{ $item->responsible_dept }}"
                                        data-remarks="{{ $item->remarks ?? '' }}">
                                    Edit
                                </button>

                                <!-- Delete Button -->
                                <form action="{{ route('action-plan.motivation-strategy.destroy', $item->id) }}"
                                      method="POST"
                                      class="d-inline"
                                      onsubmit="return confirm('Delete this motivation strategy?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted">No data available</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Profesional Pagination (Laravel + Bootstrap) -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-secondary small">
                Showing {{ $motivations->firstItem() ?? 0 }} to {{ $motivations->lastItem() ?? 0 }}
                of {{ $motivations->total() }}
            </div>
            {{ $motivations->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

{{-- Modal Add Motivation --}}
<div class="modal fade" id="motivationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Add Motivation Strategy</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form method="POST" action="{{ route('action-plan.motivation-strategy.store') }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Motivation Activity *</label>
                            <input type="text"
                                   class="form-control"
                                   name="motivation_activity"
                                   required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Target Group *</label>
                            <input type="text"
                                   class="form-control"
                                   name="target_group"
                                   required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Criteria for Recognition *</label>
                            <input type="text"
                                   class="form-control"
                                   name="criteria_for_recognition"
                                   required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Recognition Method *</label>
                            <input type="text"
                                   class="form-control"
                                   name="recognition_method"
                                   required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Frequency / Timing *</label>
                            <select class="form-select" name="frequency" required>
                                <option value="">Choose...</option>
                                <option value="Monthly">Monthly</option>
                                <option value="Quarterly">Quarterly</option>
                                <option value="Annually">Annually</option>
                                <option value="Bi-annually">Bi‑annually</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Responsible Dept. *</label>
                            <input type="text"
                                   class="form-control"
                                   name="responsible_dept"
                                   required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Remarks *</label>
                        <textarea class="form-control" rows="4" name="remarks"></textarea>
                    </div>

                    <div class="text-center mt-4">
                        <button type="button"
                                class="btn btn-outline-secondary me-2"
                                data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary-light btn-lg px-5">
                            Add Now
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Modal Edit Motivation --}}
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Edit Motivation Strategy</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form method="POST" id="editForm">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Motivation Activity *</label>
                            <input type="text"
                                   class="form-control"
                                   name="motivation_activity"
                                   required
                                   id="edit-activity">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Target Group *</label>
                            <input type="text"
                                   class="form-control"
                                   name="target_group"
                                   required
                                   id="edit-target">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Criteria for Recognition *</label>
                            <input type="text"
                                   class="form-control"
                                   name="criteria_for_recognition"
                                   required
                                   id="edit-criteria">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Recognition Method *</label>
                            <input type="text"
                                   class="form-control"
                                   name="recognition_method"
                                   required
                                   id="edit-method">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Frequency / Timing *</label>
                            <select class="form-select" name="frequency" required id="edit-frequency">
                                <option value="Monthly">Monthly</option>
                                <option value="Quarterly">Quarterly</option>
                                <option value="Annually">Annually</option>
                                <option value="Bi-annually">Bi‑annually</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Responsible Dept. *</label>
                            <input type="text"
                                   class="form-control"
                                   name="responsible_dept"
                                   required
                                   id="edit-responsible">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Remarks *</label>
                        <textarea class="form-control" rows="4" name="remarks" id="edit-remarks"></textarea>
                    </div>

                    <div class="text-center mt-4">
                        <button type="button" class="btn btn-outline-secondary me-2" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary-light btn-lg px-5">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Modal Detail Motivation --}}
<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Motivation Strategy Detail</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <dl class="row mb-0">
                    <dt class="col-sm-3 fw-bold">Motivation Activity</dt>
                    <dd class="col-sm-9" id="detail-activity"></dd>

                    <dt class="col-sm-3 fw-bold">Target Group</dt>
                    <dd class="col-sm-9" id="detail-target"></dd>

                    <dt class="col-sm-3 fw-bold">Criteria for Recognition</dt>
                    <dd class="col-sm-9" id="detail-criteria"></dd>

                    <dt class="col-sm-3 fw-bold">Recognition Method</dt>
                    <dd class="col-sm-9" id="detail-method"></dd>

                    <dt class="col-sm-3 fw-bold">Frequency</dt>
                    <dd class="col-sm-9" id="detail-frequency"></dd>

                    <dt class="col-sm-3 fw-bold">Responsible Dept.</dt>
                    <dd class="col-sm-9" id="detail-responsible"></dd>

                    <dt class="col-sm-3 fw-bold">Remarks</dt>
                    <dd class="col-sm-9" id="detail-remarks"></dd>
                </dl>
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Modal Detail
    const detailModal = document.getElementById('detailModal');
    if (detailModal) {
        detailModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            document.getElementById('detail-activity').textContent = button.getAttribute('data-activity');
            document.getElementById('detail-target').textContent = button.getAttribute('data-target');
            document.getElementById('detail-criteria').textContent = button.getAttribute('data-criteria');
            document.getElementById('detail-method').textContent = button.getAttribute('data-method');
            document.getElementById('detail-frequency').textContent = button.getAttribute('data-frequency');
            document.getElementById('detail-responsible').textContent = button.getAttribute('data-responsible');
            document.getElementById('detail-remarks').textContent = button.getAttribute('data-remarks');
        });
    }

    // Modal Edit
    const editModal = document.getElementById('editModal');
    if (editModal) {
        editModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');

            document.getElementById('edit-activity').value = button.getAttribute('data-activity');
            document.getElementById('edit-target').value = button.getAttribute('data-target');
            document.getElementById('edit-criteria').value = button.getAttribute('data-criteria');
            document.getElementById('edit-method').value = button.getAttribute('data-method');
            document.getElementById('edit-frequency').value = button.getAttribute('data-frequency');
            document.getElementById('edit-responsible').value = button.getAttribute('data-responsible');
            document.getElementById('edit-remarks').value = button.getAttribute('data-remarks');

            // route: /action-plan/motivation-strategy/{id} → PUT
            document.getElementById('editForm').action =
                "{{ route('action-plan.motivation-strategy.update', '') }}/" + id;
        });
    }
</script>
@endpush