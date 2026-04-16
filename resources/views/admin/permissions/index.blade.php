@extends('layouts.dashboard')


@section('title', 'Permissions List')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary-dark">Permissions List</h2>
        <a href="{{ route('admin.permissions.create') }}" class="btn btn-primary-light">
            <i class="bi bi-plus-lg me-2"></i>Add New Permission
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if($permissions->isEmpty())
    <div class="text-center py-5 text-muted">
        <i class="bi bi-shield-lock display-1 mb-3"></i>
        <h5>No permissions found</h5>
        <p>Start by creating a new permission.</p>
        <a href="{{ route('admin.permissions.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-2"></i>Add New Permission
        </a>
    </div>
    @else
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0 ps-4">Permission Name</th>
                            <th class="border-0">Description</th>
                            <th class="border-0 text-center" width="180px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($permissions as $permission)
                        <tr>
                            <td class="ps-4 py-3 text-break">{{ $permission->name }}</td>
                            <td class="py-3">{{ $permission->description ?? '-' }}</td>
                            <td class="py-3 text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.permissions.edit', $permission) }}"
                                       class="btn btn-sm btn-outline-warning rounded-pill mx-1"
                                       data-bs-toggle="tooltip"
                                       title="Edit Permission">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.permissions.destroy', $permission) }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete this permission?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill"
                                                data-bs-toggle="tooltip" title="Delete Permission">
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

            <!-- ✅ PAGINATION SECTION - FIXED -->
            @if($permissions->hasPages())
            <div class="card-footer bg-transparent border-top px-4 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        Showing {{ $permissions->firstItem() }} to {{ $permissions->lastItem() }}
                        of {{ $permissions->total() }} entries
                    </div>
                    <div>
                        {{ $permissions->links() }}
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif
</div>

<style>
/* Table Styles */
.table > :not(caption) > * > * {
    border-bottom: 1px solid rgba(0,0,0,0.05);
    padding: 1rem 0.75rem;
}

.table tbody tr:hover {
    background-color: rgba(0,0,0,0.02);
    transform: translateX(2px);
    transition: all 0.2s ease;
}

.btn-group .btn {
    margin: 0 2px;
}

[data-bs-toggle="tooltip"] {
    cursor: pointer;
}

/* ✅ PAGINATION CUSTOM STYLES - FIXED */
.pagination {
    margin-bottom: 0;
    gap: 0.25rem;
}

.pagination .page-link {
    border-radius: 8px;
    border: 1px solid rgba(var(--color-brown-600-rgb), 0.2);
    color: var(--color-text);
    padding: 0.5rem 0.75rem;
    margin: 0 0.125rem;
    transition: all 0.2s ease;
    font-size: 0.875rem;
}

/* ✅ SMALLER CHEVRON ARROWS */
.pagination .page-link svg {
    width: 12px;
    height: 12px;
}

/* If using icon fonts (Bootstrap Icons) */
.pagination .page-link i,
.pagination .page-link .bi {
    font-size: 0.75rem;
}

.pagination .page-link:hover {
    background-color: var(--color-secondary);
    border-color: var(--color-border);
    color: var(--color-text);
}

.pagination .page-item.active .page-link {
    background-color: var(--color-primary);
    border-color: var(--color-primary);
    color: white;
    font-weight: 600;
}

.pagination .page-item.disabled .page-link {
    background-color: transparent;
    border-color: rgba(var(--color-brown-600-rgb), 0.1);
    color: var(--color-text-secondary);
    opacity: 0.5;
}

/* Dark mode pagination */
@media (prefers-color-scheme: dark) {
    .pagination .page-link {
        border-color: rgba(var(--color-gray-400-rgb), 0.3);
        background-color: var(--color-surface);
    }

    .pagination .page-link:hover {
        background-color: var(--color-secondary-hover);
    }
}

[data-color-scheme="dark"] .pagination .page-link {
    border-color: rgba(var(--color-gray-400-rgb), 0.3);
    background-color: var(--color-surface);
}

[data-color-scheme="dark"] .pagination .page-link:hover {
    background-color: var(--color-secondary-hover);
}

/* Card footer styling */
.card-footer {
    background-color: rgba(var(--color-brown-600-rgb), 0.02);
}

@media (prefers-color-scheme: dark) {
    .card-footer {
        background-color: rgba(var(--color-gray-400-rgb), 0.05);
    }
}

[data-color-scheme="dark"] .card-footer {
    background-color: rgba(var(--color-gray-400-rgb), 0.05);
}

/* Responsive pagination */
@media (max-width: 576px) {
    .pagination {
        font-size: 0.75rem;
    }

    .pagination .page-link {
        padding: 0.375rem 0.5rem;
    }

    .card-footer > div {
        flex-direction: column;
        gap: 1rem;
    }

    .card-footer .text-muted {
        text-align: center;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endsection
