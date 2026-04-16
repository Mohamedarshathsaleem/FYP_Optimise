{{-- resources/views/admin/users/index.blade.php --}}
@extends('layouts.dashboard')

@section('title', 'User Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold text-primary-dark">User Management</h2>
                    <p class="text-muted">Manage system users and their basic information</p>
                </div>
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary-light">
                    <i class="bi bi-plus-lg me-2"></i>Add New User
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Users Table -->
            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="border-0 ps-4">User</th>
                                    <th class="border-0">Role</th>
                                    <th class="border-0">Created</th>
                                    <th class="border-0">Status</th>
                                    <th class="border-0 text-center" width="200px">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                <tr>
                                    <td class="ps-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <!-- Improved circular profile icon -->
                                            <div class="user-avatar bg-primary text-white d-flex align-items-center justify-content-center me-3">
                                                <i class="bi bi-person-fill"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-semibold">{{ $user->name }}</h6>
                                                <small class="text-muted">{{ $user->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        @php
                                            $roleColors = [
                                                'superadmin' => 'danger',
                                                'management' => 'warning',
                                                'rem' => 'info',
                                                'user' => 'secondary'
                                            ];
                                            $color = $roleColors[$user->role] ?? 'secondary';
                                        @endphp
                                        <span class="badge bg-{{ $color }}-subtle text-{{ $color }} border border-{{ $color }}-subtle">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td class="py-3">
                                        <div>
                                            <div class="fw-medium">{{ $user->created_at->format('M d, Y') }}</div>
                                            <small class="text-muted">{{ $user->created_at->format('H:i') }}</small>
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">
                                            <i class="bi bi-check-circle-fill me-1"></i>Active
                                        </span>
                                    </td>
                                    <td class="py-3 text-center">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.users.show', $user) }}"
                                               class="btn btn-sm btn-outline-info rounded-pill"
                                               data-bs-toggle="tooltip"
                                               title="View Details">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.users.edit', $user) }}"
                                               class="btn btn-sm btn-outline-primary rounded-pill mx-1"
                                               data-bs-toggle="tooltip"
                                               title="Edit User">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            @if($user->id !== auth()->id())
                                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                                  style="display: inline;"
                                                  onsubmit="return confirm('Are you sure you want to delete this user?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="btn btn-sm btn-outline-danger rounded-pill"
                                                        data-bs-toggle="tooltip"
                                                        title="Delete User">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="empty-state">
                                            <div class="empty-icon text-muted mb-3">
                                                <i class="bi bi-people display-1"></i>
                                            </div>
                                            <h5 class="text-muted">No users found</h5>
                                            <p class="text-muted mb-4">Get started by adding your first user.</p>
                                            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                                                <i class="bi bi-plus-lg me-2"></i>Add First User
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($users->hasPages())
                    <div class="card-footer bg-transparent border-top-0 px-4 py-3">
                        {{ $users->links() }}
                    </div>
                    @endif
                </div>
            </div>

            <!-- Quick Stats -->
            @if($users->count() > 0)
            <div class="row mt-4">
                <div class="col-md-3">
                    <div class="card text-center border-0 bg-primary-subtle">
                        <div class="card-body">
                            <div class="text-primary display-6 mb-2">
                                <i class="bi bi-people-fill"></i>
                            </div>
                            <h3 class="fw-bold text-primary">{{ $users->total() }}</h3>
                            <p class="text-muted mb-0">Total Users</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center border-0 bg-success-subtle">
                        <div class="card-body">
                            <div class="text-success display-6 mb-2">
                                <i class="bi bi-check-circle-fill"></i>
                            </div>
                            <h3 class="fw-bold text-success">{{ $users->total() }}</h3>
                            <p class="text-muted mb-0">Active Users</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center border-0 bg-warning-subtle">
                        <div class="card-body">
                            <div class="text-warning display-6 mb-2">
                                <i class="bi bi-shield-check"></i>
                            </div>
                            <h3 class="fw-bold text-warning">{{ $users->where('role', 'superadmin')->count() }}</h3>
                            <p class="text-muted mb-0">Admins</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center border-0 bg-info-subtle">
                        <div class="card-body">
                            <div class="text-info display-6 mb-2">
                                <i class="bi bi-person-plus-fill"></i>
                            </div>
                            <h3 class="fw-bold text-info">{{ $users->where('created_at', '>=', now()->subDays(30))->count() }}</h3>
                            <p class="text-muted mb-0">New This Month</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
/* User Avatar Styling */
.user-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    font-size: 18px;
    font-weight: 600;
    position: relative;
    flex-shrink: 0;
}

.user-avatar::before {
    content: '';
    position: absolute;
    width: 100%;
    height: 100%;
    border-radius: 50%;
    background: inherit;
    opacity: 0.1;
    transform: scale(1.3);
    z-index: -1;
}

/* Table Improvements */
.table > :not(caption) > * > * {
    border-bottom: 1px solid rgba(0,0,0,0.05);
    padding: 1rem 0.75rem;
}

.table tbody tr:hover {
    background-color: rgba(0,0,0,0.02);
    transform: translateX(2px);
    transition: all 0.2s ease;
}

/* Badge Improvements */
.badge {
    font-weight: 500;
    padding: 0.5rem 0.8rem;
    font-size: 0.75rem;
}

/* Button Group */
.btn-group .btn {
    margin: 0 2px;
}

.btn-outline-info:hover,
.btn-outline-primary:hover,
.btn-outline-danger:hover {
    transform: translateY(-1px);
    transition: all 0.2s ease;
}

/* Empty State */
.empty-state {
    padding: 2rem;
}

.empty-icon {
    opacity: 0.3;
}

/* Card improvements */
.card {
    border-radius: 12px;
    transition: all 0.3s ease;
}

/* Stats Cards */
.bg-primary-subtle { background-color: rgba(13, 110, 253, 0.1) !important; }
.bg-success-subtle { background-color: rgba(25, 135, 84, 0.1) !important; }
.bg-warning-subtle { background-color: rgba(255, 193, 7, 0.1) !important; }
.bg-info-subtle { background-color: rgba(13, 202, 240, 0.1) !important; }

/* Tooltip */
[data-bs-toggle="tooltip"] {
    cursor: pointer;
}
</style>

<script>
// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endsection
