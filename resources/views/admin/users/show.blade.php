{{-- resources/views/admin/users/show.blade.php --}}
@extends('layouts.dashboard')

@section('title', 'User Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold text-primary-dark">User Details</h2>
                    <p class="text-muted">Detailed information for {{ $user->name }}</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary-light">
                        <i class="bi bi-pencil me-2"></i>Edit User
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Back to Users
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <!-- User Profile Card -->
                    <div class="card">
                        <div class="card-body text-center">
                            <div class="icon-shape bg-primary text-white rounded-circle mx-auto mb-3" style="width: 100px; height: 100px;">
                                <i class="bi bi-person-fill" style="font-size: 3rem;"></i>
                            </div>
                            <h4>{{ $user->name }}</h4>
                            <p class="text-muted">{{ $user->email }}</p>
                            @php
                                $roleColors = [
                                    'superadmin' => 'danger',
                                    'management' => 'warning',
                                    'rem' => 'info',
                                    'user' => 'secondary'
                                ];
                                $color = $roleColors[$user->role] ?? 'secondary';
                            @endphp
                            <span class="badge bg-{{ $color }} fs-6">{{ ucfirst($user->role) }}</span>
                        </div>
                    </div>

                    <!-- Account Info -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="mb-0">Account Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <small class="text-muted">Created</small>
                                    <p class="mb-2">{{ $user->created_at->format('M d, Y') }}</p>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">Last Updated</small>
                                    <p class="mb-2">{{ $user->updated_at->format('M d, Y') }}</p>
                                </div>
                            </div>
                            <hr>
                            <small class="text-muted">Status</small>
                            <p><span class="badge bg-success">Active</span></p>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <!-- Permissions Overview -->
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Permissions Overview</h6>
                            <a href="{{ route('admin.user-permissions.edit', $user) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil me-1"></i>Edit Permissions
                            </a>
                        </div>
                        <div class="card-body">
                            @if($user->permissions && is_array($user->permissions))
                                <div class="row">
                                    @php
                                        $modules = [
                                            'dashboard' => 'Dashboard',
                                            'issues' => 'Issues',
                                            'boundaries' => 'Boundaries',
                                            'legal' => 'Legal',
                                            'stakeholders' => 'Stakeholders',
                                            'committee' => 'Committee',
                                            'motivation' => 'Motivation',
                                            'communication' => 'Communication',
                                            'training' => 'Training'
                                        ];
                                    @endphp

                                    @foreach($modules as $moduleKey => $moduleName)
                                        @php
                                            $modulePermissions = $user->permissions[$moduleKey] ?? [];
                                            $hasAnyPermission = false;
                                            $permissionsList = [];

                                            if(isset($modulePermissions['can_view']) && $modulePermissions['can_view']) {
                                                $permissionsList[] = 'View';
                                                $hasAnyPermission = true;
                                            }
                                            if(isset($modulePermissions['can_add']) && $modulePermissions['can_add']) {
                                                $permissionsList[] = 'Add';
                                                $hasAnyPermission = true;
                                            }
                                            if(isset($modulePermissions['can_edit']) && $modulePermissions['can_edit']) {
                                                $permissionsList[] = 'Edit';
                                                $hasAnyPermission = true;
                                            }
                                            if(isset($modulePermissions['can_delete']) && $modulePermissions['can_delete']) {
                                                $permissionsList[] = 'Delete';
                                                $hasAnyPermission = true;
                                            }
                                        @endphp

                                        <div class="col-md-6 mb-3">
                                            <div class="border rounded p-3 {{ $hasAnyPermission ? 'border-success bg-light' : 'border-secondary' }}">
                                                <h6 class="mb-2">
                                                    {{ $moduleName }}
                                                    @if($hasAnyPermission)
                                                        <i class="bi bi-check-circle-fill text-success"></i>
                                                    @else
                                                        <i class="bi bi-x-circle-fill text-muted"></i>
                                                    @endif
                                                </h6>
                                                @if($hasAnyPermission)
                                                    <div class="d-flex flex-wrap gap-1">
                                                        @foreach($permissionsList as $permission)
                                                            <span class="badge bg-success">{{ $permission }}</span>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <small class="text-muted">No access</small>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="bi bi-shield-x display-4 text-muted"></i>
                                    <p class="text-muted mt-2">No permissions configured</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
