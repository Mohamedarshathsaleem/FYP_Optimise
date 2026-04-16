@extends('layouts.dashboard')

@section('title', $role->exists ? 'Edit Role' : 'Create Role')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">

            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold text-primary">{{ $role->exists ? 'Edit Role' : 'Create New Role' }}</h2>
                <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-1">
                    <i class="bi bi-arrow-left"></i> Back to Roles
                </a>
            </div>

            <form action="{{ $role->exists ? route('admin.roles.update', $role) : route('admin.roles.store') }}" method="POST" class="card shadow-sm border-0">
                @csrf
                @if ($role->exists)
                    @method('PUT')
                @endif
                <div class="card-body p-4">

                    <div class="mb-4">
                        <label for="name" class="form-label fw-semibold">Role Name</label>
                        <input type="text" name="name" id="name" class="form-control form-control-lg @error('name') is-invalid @enderror"
                               value="{{ old('name', $role->name) }}" placeholder="e.g. Super Admin" required autocomplete="off" autofocus>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <h5 class="fw-semibold mb-3">Permissions</h5>
                        <div class="d-flex justify-content-end mb-2 gap-3">
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="togglePermissions(true)">
                                <i class="bi bi-check-all me-1"></i> Select All
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="togglePermissions(false)">
                                <i class="bi bi-x-circle me-1"></i> Deselect All
                            </button>
                        </div>

                        <div class="table-responsive shadow border rounded">
                            <table class="table table-hover m-0">
                                <thead class="table-light position-sticky top-0" style="z-index:10;">
                                    <tr>
                                        <th class="ps-4" style="width: 35%;">Module</th>
                                        @foreach(['view', 'add', 'edit', 'delete', 'import', 'approval', 'export'] as $action)
                                            <th class="text-center text-capitalize">{{ $action }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $groupedPermissions = $permissions->groupBy(function($perm) {
                                            return explode('.', $perm->name)[0];
                                        });

                                        $rolePermIds = $role->permissions->pluck('id')->toArray();
                                    @endphp

                                    @foreach($groupedPermissions as $module => $perms)
                                    <tr>
                                        <td class="ps-4 fw-semibold text-capitalize">{{ $module }}</td>
                                        @foreach(['view', 'add', 'edit', 'delete', 'import', 'approval', 'export'] as $action)
                                            @php
                                                $perm = $perms->firstWhere('name', $module.'.'.$action);
                                            @endphp
                                            <td class="text-center">
                                                @if($perm)
                                                <div class="form-check form-switch d-flex justify-content-center">
                                                    <input class="form-check-input permission-checkbox" type="checkbox"
                                                           name="permissions[]" value="{{ $perm->id }}" id="perm_{{ $module }}_{{ $action }}"
                                                           {{ in_array($perm->id, $rolePermIds) ? 'checked' : '' }}>
                                                </div>
                                                @else
                                                <span class="text-muted">&mdash;</span>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="mt-4 d-flex justify-content-end gap-3">
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4">
                            {{ $role->exists ? 'Update Role' : 'Create Role' }}
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>

<style>
.table thead th {
    border-bottom: 2px solid #dee2e6;
}
</style>

<script>
    function togglePermissions(select = true) {
        document.querySelectorAll('.permission-checkbox').forEach(cb => cb.checked = select);
    }
</script>
@endsection
