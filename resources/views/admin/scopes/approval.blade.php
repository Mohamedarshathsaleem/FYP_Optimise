@extends('layouts.dashboard')

@section('title', 'Scope Approval')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary">Scope Approval</h2>
        <a href="#" class="btn btn-outline-secondary d-flex gap-2 align-items-center">
            <i class="bi bi-arrow-left"></i> Back to Scope
        </a>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white p-4 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0">Scopes & Boundaries - Needs Approval</h5>
            <a href="#" class="btn btn-primary">Export</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Included</th>
                            <th>Excluded</th>
                            <th>Rationale</th>
                            <th>Status</th>
                            <th>Modified</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($scopes as $scope)
                        <tr>
                            <td>{{ $scope->scope_id }}</td>
                            <td>{{ Str::limit($scope->included, 50) }}</td>
                            <td>{{ Str::limit($scope->excluded, 50) }}</td>
                            <td>{{ Str::limit($scope->rationale_for_excluding, 50) }}</td>
                            <td>
                                <span class="badge 
                                    @if($scope->status == 'pending') bg-warning
                                    @elseif($scope->status == 'approved') bg-success
                                    @else bg-danger @endif">
                                    {{ ucfirst($scope->status) }}
                                </span>
                            </td>
                            <td>{{ $scope->updated_at->diffForHumans() }}</td>
                            <td>
                                <div class="d-flex gap-2">
                                    <form action="{{ route('admin.scopes.approve', $scope->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                    </form>
                                    <form action="{{ route('admin.scopes.reject', $scope->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-danger">Reject</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                                    <h5 class="mt-2">No scopes pending approval</h5>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            @if($scopes->hasPages())
            <div class="d-flex justify-content-between align-items-center p-3">
                <div class="text-muted small">Showing {{ $scopes->firstItem() }} to {{ $scopes->lastItem() }} of {{ $scopes->total() }} results</div>
                <div>{{ $scopes->links() }}</div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
