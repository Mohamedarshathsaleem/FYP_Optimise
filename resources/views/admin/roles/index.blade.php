@extends('layouts.dashboard')

@section('title', 'Role Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">

            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold text-primary-dark">Role Management</h2>
                    <p class="text-muted">Manage system roles and their permissions</p>
                </div>
                <a href="{{ route('admin.roles.create') }}" class="btn btn-primary-light">
                    <i class="bi bi-plus-lg me-2"></i>Add New Role
                </a>
            </div>

            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @if($roles->isEmpty())
            <div class="alert alert-info text-center py-5">
                <i class="bi bi-shield-lock display-1 text-muted mb-3"></i>
                <h5 class="text-muted">No roles found</h5>
                <p class="text-muted mb-4">Start by creating a new role.</p>
                <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-2"></i>Add New Role
                </a>
            </div>
            @else

            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="border-0 ps-4">Role</th>
                                    <th class="border-0">Permissions Count</th>
                                    <th class="border-0">Created</th>
                                    <th class="border-0 text-center" width="180px">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($roles as $role)
                                <tr>
                                    <td class="ps-4 py-3 fw-semibold text-capitalize">{{ $role->name }}</td>
                                    <td class="py-3">
                                        <span class="badge bg-light text-dark border">{{ $role->permissions()->count() }} permissions</span>
                                    </td>
                                    <td class="py-3">
                                        <div>
                                            <div class="fw-medium">{{ $role->created_at->format('M d, Y') }}</div>
                                            <small class="text-muted">{{ $role->created_at->format('H:i') }}</small>
                                        </div>
                                    </td>
                                    <td class="py-3 text-center">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.roles.edit', $role) }}"
                                               class="btn btn-sm btn-outline-primary rounded-pill mx-1"
                                               data-bs-toggle="tooltip"
                                               title="Edit Role">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form method="POST" action="{{ route('admin.roles.destroy', $role) }}"
                                                  style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this role?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="btn btn-sm btn-outline-danger rounded-pill"
                                                        data-bs-toggle="tooltip"
                                                        title="Delete Role">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($roles->hasPages())
                    <div class="card-footer bg-transparent border-top-0 px-4 py-3">
                        {{ $roles->links() }}
                    </div>
                    @endif
                </div>
            </div>

            @endif
        </div>
    </div>
</div>

<style>
/* Role table customizations like user table */
.table > :not(caption) > * > * {
    border-bottom: 1px solid rgba(0,0,0,0.05);
    padding: 1rem 0.75rem;
}

.table tbody tr:hover {
    background-color: rgba(0,0,0,0.02);
    transform: translateX(2px);
    transition: all 0.2s ease;
}

.badge {
    font-weight: 500;
    padding: 0.5rem 0.8rem;
    font-size: 0.75rem;
}

.btn-group .btn {
    margin: 0 2px;
}

.btn-outline-primary:hover,
.btn-outline-danger:hover {
    transform: translateY(-1px);
    transition: all 0.2s ease;
}

[data-bs-toggle="tooltip"] {
    cursor: pointer;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endsection
