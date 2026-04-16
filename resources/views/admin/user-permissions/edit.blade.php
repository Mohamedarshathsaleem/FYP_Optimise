{{-- resources/views/admin/user-permissions/edit.blade.php --}}
@extends('layouts.dashboard')

@section('title', 'Edit Roles for User')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">

            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold text-primary-dark">Edit Roles</h2>
                    <p class="text-muted">Assign roles for user: <strong>{{ $user->name }}</strong></p>
                </div>
                <a href="{{ route('admin.user-permissions.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back to List
                </a>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <form action="{{ route('admin.user-permissions.update', $user) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="roles" class="form-label">Select Roles</label>
                            <select name="roles[]" id="roles" class="form-select" multiple>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ $user->roles->contains($role) ? 'selected' : '' }}>
                                        {{ ucfirst($role->name) }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">You can assign one or more roles to this user.</small>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.user-permissions.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-lg me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-2"></i>Update Roles
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
