@extends('layouts.dashboard')

@section('title', 'User Permissions')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">

            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold text-primary-dark">User Permissions</h2>
                    <p class="text-muted">Manage user permissions and roles</p>
                </div>
                <button class="btn btn-primary-light" data-bs-toggle="modal" data-bs-target="#bulkUpdateModal">
                    <i class="bi bi-people-fill me-2"></i>Bulk Update
                </button>
            </div>

            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <!-- Users Table -->
            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="border-0 ps-4">
                                        <input type="checkbox" id="selectAll" class="form-check-input">
                                    </th>
                                    <th class="border-0">User</th>
                                    <th class="border-0">Role(s)</th>
                                    <th class="border-0">Permissions Count</th>
                                    <th class="border-0">Status</th>
                                    <th class="border-0">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                @php
                                    $totalPermissions = $user->roles->flatMap->permissions->unique('id')->count();
                                @endphp
                                <tr>
                                    <td class="ps-4 py-3">
                                        <input type="checkbox" name="user_ids[]" value="{{ $user->id }}" class="form-check-input user-checkbox">
                                    </td>
                                    <td class="py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="user-avatar bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width:48px; height:48px; border-radius:50%; font-size:18px;">
                                                <i class="bi bi-person-fill"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-semibold">{{ $user->name }}</h6>
                                                <small class="text-muted">{{ $user->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        @foreach($user->roles as $role)
                                            @php
                                                $roleColors = [
                                                    'superadmin' => 'danger',
                                                    'management' => 'warning',
                                                    'rem' => 'info',
                                                    'user' => 'secondary'
                                                ];
                                                $color = $roleColors[$role->name] ?? 'secondary';
                                            @endphp
                                            <span class="badge bg-{{ $color }}-subtle text-{{ $color }} border border-{{ $color }}-subtle me-1">
                                                {{ ucfirst($role->name) }}
                                            </span>
                                        @endforeach
                                    </td>
                                    <td class="py-3">
                                        <span class="badge bg-light text-dark border">{{ $totalPermissions }} permissions</span>
                                    </td>
                                    <td class="py-3">
                                        @if($totalPermissions > 0)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle">
                                                <i class="bi bi-check-circle-fill me-1"></i>Active
                                            </span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">
                                                <i class="bi bi-dash-circle me-1"></i>Limited
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.user-permissions.edit', $user) }}" class="btn btn-sm btn-outline-primary rounded-pill">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button class="btn btn-sm btn-outline-info rounded-pill mx-1"
                                                onclick="viewPermissions({{ $user->id }}, '{{ $user->name }}')">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if(method_exists($users, 'links'))
                    <div class="px-4 py-3">
                        {{ $users->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Update Modal -->
<div class="modal fade" id="bulkUpdateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.user-permissions.bulk-update') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Bulk Update Permissions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Selected Users</label>
                        <div id="selectedUsers" class="border rounded p-2 bg-light">
                            <small class="text-muted">Select users from the table first</small>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Change Role To</label>
                        <select name="bulk_role" class="form-select">
                            <option value="">-- Keep Current Role --</option>
                            <option value="superadmin">Super Admin</option>
                            <option value="management">Management</option>
                            <option value="rem">Internal REM</option>
                            <option value="user">User</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary-light">Update Selected</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.user-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    font-size: 18px;
    font-weight: 600;
    flex-shrink: 0;
}
</style>

<script>
document.getElementById('selectAll').addEventListener('change', function() {
    const userCheckboxes = document.querySelectorAll('.user-checkbox');
    userCheckboxes.forEach(cb => cb.checked = this.checked);
    updateSelectedUsers();
});

function updateSelectedUsers() {
    const selectedCheckboxes = document.querySelectorAll('.user-checkbox:checked');
    const selectedUsersDiv = document.getElementById('selectedUsers');

    if (selectedCheckboxes.length === 0) {
        selectedUsersDiv.innerHTML = '<small class="text-muted">No users selected</small>';
    } else {
        const userNames = Array.from(selectedCheckboxes).map(cb => {
            const row = cb.closest('tr');
            const userName = row.querySelector('h6').textContent;
            return `<span class="badge bg-primary me-1">${userName}</span>`;
        });
        selectedUsersDiv.innerHTML = userNames.join('');
    }
}

document.addEventListener('change', function(e) {
    if (e.target.classList.contains('user-checkbox')) {
        updateSelectedUsers();
    }
});

function viewPermissions(userId, userName) {
    alert(`View permissions for ${userName} - Feature coming soon!`);
}
</script>
@endsection
