{{-- resources/views/admin/users/create.blade.php --}}
@extends('layouts.dashboard')

@section('title', 'Create User')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold text-primary-dark">Create New User</h2>
                    <p class="text-muted">Add a new user to the system</p>
                </div>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back to Users
                </a>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('admin.users.store') }}" method="POST">
                                @csrf

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Full Name</label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                   id="name" name="name" value="{{ old('name') }}" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email Address</label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                                   id="email" name="email" value="{{ old('email') }}" required>
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

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="password" class="form-label">Password</label>
                                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                                   id="password" name="password" required>
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                                            <input type="password" class="form-control"
                                                   id="password_confirmation" name="password_confirmation" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancel</a>
                                    <button type="submit" class="btn btn-primary-light">
                                        <i class="bi bi-check-lg me-2"></i>Create User
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">Role Information</h6>
                        </div>
                        <div class="card-body">
                            <div id="role-info">
                                <p class="text-muted">Select a role to see its permissions</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const roleInfo = {
    'superadmin': {
        name: 'Super Admin',
        description: 'Full access to all system features including user management',
        permissions: ['All Modules: Full Access', 'User Management', 'Permission Management']
    },
    'management': {
        name: 'Top Management',
        description: 'View-only access to all modules for reporting',
        permissions: ['All Modules: View Only']
    },
    'rem': {
        name: 'Internal REM',
        description: 'Can manage most energy management modules',
        permissions: ['Most Modules: Add/Edit/View', 'Boundaries: Limited Access']
    },
    'user': {
        name: 'User',
        description: 'Basic user with limited access',
        permissions: ['Dashboard: View Only']
    }
};

document.getElementById('role').addEventListener('change', function() {
    const role = this.value;
    const infoDiv = document.getElementById('role-info');

    if (role && roleInfo[role]) {
        const info = roleInfo[role];
        infoDiv.innerHTML = `
            <h6 class="text-primary">${info.name}</h6>
            <p class="small">${info.description}</p>
            <strong>Permissions:</strong>
            <ul class="small mt-2">
                ${info.permissions.map(p => `<li>${p}</li>`).join('')}
            </ul>
        `;
    } else {
        infoDiv.innerHTML = '<p class="text-muted">Select a role to see its permissions</p>';
    }
});
</script>
@endsection
