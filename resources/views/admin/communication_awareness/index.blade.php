{{-- resources/views/communication_awareness/index.blade.php --}}
@extends('layouts.dashboard')

@section('title', 'Communication & Awareness')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <p class="text-secondary small mb-1">Action Plan / Action Plan Development / Communication & Awareness</p>
        <h3 class="fw-bold">Communication & Awareness</h3>
    </div>
    <div class="d-flex align-items-center">
        <div class="input-group search-box me-3">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="text" class="form-control" placeholder="Search" name="search">
            <img src="{{ asset('images/user.png') }}" class="rounded-circle" alt="User" style="width: 40px; height: 40px;">
        </div>
    </div>
</div>

<p class="fw-bold text-primary-light">User Dashboard</p>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="card-title fw-bold">Communication & Awareness List</h5>
            <div>
                <button type="button"
                        class="btn btn-sm btn-primary-light"
                        data-bs-toggle="modal"
                        data-bs-target="#addCommunicationModal">
                    Add Communication
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Action / Initiative</th>
                        <th>Internal / External</th>
                        <th>Energy Message</th>
                        <th>Target Audience</th>
                        <th>Communication</th>
                        <th>Person In-Charge</th>
                        <th>Planned Date</th>
                        <th>Remarks</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($communications as $index => $item)
                        <tr>
                            <td>{{ $index + 1 + ($communications->currentPage() - 1) * $communications->perPage() }}</td>
                            <td>{{ $item->action_initiative }}</td>
                            <td>{{ $item->type }}</td>
                            <td>{{ $item->energy_message }}</td>
                            <td>{{ $item->target_audience }}</td>
                            <td>{{ $item->communication }}</td>
                            <td>{{ $item->person_in_charge }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->planned_date)->format('d M Y') }}</td>
                            <td>{{ $item->remarks ?? '-' }}</td>
                            <td>
                                <!-- Detail Button -->
                                <button type="button"
                                        class="btn btn-sm btn-outline-info"
                                        data-bs-toggle="modal"
                                        data-bs-target="#detailModal"
                                        data-action="{{ $item->action_initiative }}"
                                        data-type="{{ $item->type }}"
                                        data-message="{{ $item->energy_message }}"
                                        data-target="{{ $item->target_audience }}"
                                        data-communication="{{ $item->communication }}"
                                        data-person="{{ $item->person_in_charge }}"
                                        data-date="{{ \Carbon\Carbon::parse($item->planned_date)->format('d M Y') }}"
                                        data-remarks="{{ $item->remarks ?? '-' }}">
                                    Detail
                                </button>

                                <!-- Edit Button -->
                                <button type="button"
                                        class="btn btn-sm btn-outline-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editModal"
                                        data-id="{{ $item->id }}"
                                        data-action="{{ $item->action_initiative }}"
                                        data-type="{{ $item->type }}"
                                        data-message="{{ $item->energy_message }}"
                                        data-target="{{ $item->target_audience }}"
                                        data-communication="{{ $item->communication }}"
                                        data-person="{{ $item->person_in_charge }}"
                                        data-date="{{ $item->planned_date }}"
                                        data-remarks="{{ $item->remarks ?? '' }}">
                                    Edit
                                </button>

                                <!-- Delete Button -->
                               
                                <form action="{{ route('action-plan.communication-awarness.destroy', $item->id) }}"
                                      method="POST"
                                      class="d-inline"
                                      onsubmit="return confirm('Delete this communication & awareness?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted">No data available</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-secondary small">
                Showing {{ $communications->firstItem() ?? 0 }} to {{ $communications->lastItem() ?? 0 }}
                of {{ $communications->total() }}
            </div>
            {{ $communications->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

{{-- Modal Add Communication --}}
<div class="modal fade" id="addCommunicationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Add Communication & Awareness</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form method="POST" action="{{ route('action-plan.communication-awarness.store') }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Action / Initiative *</label>
                            <input type="text" class="form-control" name="action_initiative" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Internal / External *</label>
                            <select class="form-select" name="type" required>
                                <option value="">Choose...</option>
                                <option value="Internal">Internal</option>
                                <option value="External">External</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Energy Message *</label>
                            <input type="text" class="form-control" name="energy_message" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Target Audience *</label>
                            <select class="form-select" name="target_audience" required>
                                <option value="">Choose...</option>
                                <option value="All Employees">All Employees</option>
                                <option value="All office">All office</option>
                                <option value="Department head">Department head</option>
                                <option value="Management">Management</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Communication *</label>
                            <select class="form-select" name="communication" required>
                                <option value="">Choose...</option>
                                <option value="WhatsApp group">WhatsApp group</option>
                                <option value="Email">Email</option>
                                <option value="PDF report">PDF report</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Person In-Charge *</label>
                            <select class="form-select" name="person_in_charge" required>
                                <option value="">Choose...</option>
                                <option value="Energy Manager">Energy Manager</option>
                                <option value="Facility Supervisor">Facility Supervisor</option>
                                <option value="Compliance Offi">Compliance Offi</option>
                                <option value="Data Analyst">Data Analyst</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Planned Date *</label>
                            <input type="date" class="form-control" name="planned_date" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Remarks</label>
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

{{-- Modal Edit Communication --}}
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Edit Communication & Awareness</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form method="POST" id="editForm" action="">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Action / Initiative *</label>
                            <input type="text"
                                   class="form-control"
                                   name="action_initiative"
                                   required
                                   id="edit-action">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Internal / External *</label>
                            <select class="form-select" name="type" required id="edit-type">
                                <option value="Internal">Internal</option>
                                <option value="External">External</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Energy Message *</label>
                            <input type="text"
                                   class="form-control"
                                   name="energy_message"
                                   required
                                   id="edit-message">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Target Audience *</label>
                            <select class="form-select" name="target_audience" required id="edit-target">
                                <option value="All Employees">All Employees</option>
                                <option value="All office">All office</option>
                                <option value="Department head">Department head</option>
                                <option value="Management">Management</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Communication *</label>
                            <select class="form-select" name="communication" required id="edit-communication">
                                <option value="WhatsApp group">WhatsApp group</option>
                                <option value="Email">Email</option>
                                <option value="PDF report">PDF report</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Person In-Charge *</label>
                            <select class="form-select" name="person_in_charge" required id="edit-person">
                                <option value="Energy Manager">Energy Manager</option>
                                <option value="Facility Supervisor">Facility Supervisor</option>
                                <option value="Compliance Offi">Compliance Offi</option>
                                <option value="Data Analyst">Data Analyst</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Planned Date *</label>
                            <input type="date"
                                   class="form-control"
                                   name="planned_date"
                                   required
                                   id="edit-date">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Remarks</label>
                        <textarea class="form-control"
                                  rows="4"
                                  name="remarks"
                                  id="edit-remarks"></textarea>
                    </div>

                    <div class="text-center mt-4">
                        <button type="button"
                                class="btn btn-outline-secondary me-2"
                                data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary-light btn-lg px-5">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Modal Detail Communication --}}
<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Detail Communication & Awareness</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="text-secondary small mb-1">Action / Initiative</p>
                        <p class="fw-semibold" id="detail-action">-</p>
                    </div>
                    <div class="col-md-6">
                        <p class="text-secondary small mb-1">Internal / External</p>
                        <p class="fw-semibold" id="detail-type">-</p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="text-secondary small mb-1">Energy Message</p>
                        <p class="fw-semibold" id="detail-message">-</p>
                    </div>
                    <div class="col-md-6">
                        <p class="text-secondary small mb-1">Target Audience</p>
                        <p class="fw-semibold" id="detail-target">-</p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="text-secondary small mb-1">Communication</p>
                        <p class="fw-semibold" id="detail-communication">-</p>
                    </div>
                    <div class="col-md-6">
                        <p class="text-secondary small mb-1">Person In-Charge</p>
                        <p class="fw-semibold" id="detail-person">-</p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="text-secondary small mb-1">Planned Date</p>
                        <p class="fw-semibold" id="detail-date">-</p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-12">
                        <p class="text-secondary small mb-1">Remarks</p>
                        <p class="fw-semibold" id="detail-remarks">-</p>
                    </div>
                </div>

                <div class="text-center mt-2">
                    <button type="button"
                            class="btn btn-outline-secondary px-5"
                            data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Populate Edit Modal
    const editModal = document.getElementById('editModal');
    editModal.addEventListener('show.bs.modal', function (event) {
        const btn = event.relatedTarget;

        const id            = btn.getAttribute('data-id');
        const action        = btn.getAttribute('data-action');
        const type          = btn.getAttribute('data-type');
        const message       = btn.getAttribute('data-message');
        const target        = btn.getAttribute('data-target');
        const communication = btn.getAttribute('data-communication');
        const person        = btn.getAttribute('data-person');
        const date          = btn.getAttribute('data-date');
        const remarks       = btn.getAttribute('data-remarks');

        // Set form action URL dynamically
        document.getElementById('editForm').action =
            `/action-plan/communication-awareness/${id}`;

        document.getElementById('edit-action').value        = action;
        document.getElementById('edit-message').value       = message;
        document.getElementById('edit-date').value          = date;
        document.getElementById('edit-remarks').value       = remarks;

        // Set select values
        setSelectValue('edit-type',          type);
        setSelectValue('edit-target',        target);
        setSelectValue('edit-communication', communication);
        setSelectValue('edit-person',        person);
    });

    // Populate Detail Modal
    const detailModal = document.getElementById('detailModal');
    detailModal.addEventListener('show.bs.modal', function (event) {
        const btn = event.relatedTarget;

        document.getElementById('detail-action').textContent        = btn.getAttribute('data-action')        || '-';
        document.getElementById('detail-type').textContent          = btn.getAttribute('data-type')          || '-';
        document.getElementById('detail-message').textContent       = btn.getAttribute('data-message')       || '-';
        document.getElementById('detail-target').textContent        = btn.getAttribute('data-target')        || '-';
        document.getElementById('detail-communication').textContent = btn.getAttribute('data-communication') || '-';
        document.getElementById('detail-person').textContent        = btn.getAttribute('data-person')        || '-';
        document.getElementById('detail-date').textContent          = btn.getAttribute('data-date')          || '-';
        document.getElementById('detail-remarks').textContent       = btn.getAttribute('data-remarks')       || '-';
    });

    // Helper: set <select> value
    function setSelectValue(id, value) {
        const select = document.getElementById(id);
        if (!select) return;
        for (let option of select.options) {
            if (option.value === value) {
                option.selected = true;
                break;
            }
        }
    }
</script>
@endpush

@endsection