{{-- resources/views/admin/users/edit.blade.php --}}
@extends('layouts.dashboard')

@section('title', 'Edit User')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold text-primary-dark">Edit User</h2>
                    <p class="text-muted">Update user information for: <strong>{{ $user->name }}</strong></p>
                </div>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back to Users
                </a>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('admin.users.update', $user) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Full Name</label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                   id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email Address</label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                                   id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="role" class="form-label">Role</label>
                                    <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                                        <option value="">Select Role</option>
                                        @foreach($roles as $roleName)
                                            <option value="{{ $roleName }}" {{ old('role', $user->role ?? '') === $roleName ? 'selected' : '' }}>
                                                {{ ucfirst($roleName) }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @error('role')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle me-2"></i>
                                    <strong>Password Update:</strong> Leave password fields empty to keep current password.
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="password" class="form-label">New Password (Optional)</label>
                                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                                   id="password" name="password">
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                            <input type="password" class="form-control"
                                                   id="password_confirmation" name="password_confirmation">
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between">
                                    <div>
                                        @if($user->id !== auth()->id())
                                        <button type="button" class="btn btn-outline-danger" onclick="confirmDelete()">
                                            <i class="bi bi-trash me-2"></i>Delete User
                                        </button>
                                        @endif
                                    </div>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancel</a>
                                        <button type="submit" class="btn btn-primary-light">
                                            <i class="bi bi-check-lg me-2"></i>Update User
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <!-- User Info Card -->
                    <div class="card mb-3">
                        <div class="card-body text-center">
                            <div class="icon-shape bg-primary text-white rounded-circle mx-auto mb-3" style="width: 80px; height: 80px;">
                                <i class="bi bi-person-fill" style="font-size: 2rem;"></i>
                            </div>
                            <h5>{{ $user->name }}</h5>
                            <p class="text-muted">{{ $user->email }}</p>
                            <span class="badge bg-info">{{ ucfirst($user->role) }}</span>
                            <hr>
                            <small class="text-muted">
                                Created: {{ $user->created_at->format('M d, Y') }}<br>
                                Last Updated: {{ $user->updated_at->format('M d, Y') }}
                            </small>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="card d-none">
                        <div class="card-header">
                            <h6 class="mb-0">Quick Actions</h6>
                        </div>
                        <div class="card-body">
                            <a href="{{ route('admin.user-permissions.edit', $user) }}" class="btn btn-sm btn-outline-primary w-100 mb-2">
                                <i class="bi bi-key me-2"></i>Manage Permissions
                            </a>
                            <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-outline-info w-100">
                                <i class="bi bi-eye me-2"></i>View Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Form -->
@if($user->id !== auth()->id())
<form id="deleteForm" action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endif

<script>
function confirmDelete() {
    if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        document.getElementById('deleteForm').submit();
    }
}
</script>
@endsection
