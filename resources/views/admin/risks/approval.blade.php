@extends('layouts.dashboard')

@section('title', 'Risk Approval')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary">Approval Risks & Opportunities</h2>
        <a href="#" class="btn btn-outline-secondary d-flex gap-2 align-items-center">
            <i class="bi bi-arrow-left"></i> Back to Risks
        </a>
    </div>

    <!-- Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white p-4 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0">Needs Approval</h5>
            <a href="#" class="btn btn-primary">
                Export
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Issue</th>
                            <th>Type</th>
                            <th>Category</th>
                            <th>Likelihood</th>
                            <th>Risk Level</th>
                            <th>Submitter</th>
                            <th>Modified</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($risks as $risk)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $risk->issue }}</td>
                            <td>
                                <span class="badge {{ $risk->type == 'External' ? 'bg-info' : 'bg-success' }}">{{ $risk->type }}</span>
                            </td>
                            <td>
                                <span class="badge {{ $risk->category == 'Risk' ? 'bg-danger' : 'bg-primary' }}">{{ $risk->category }}</span>
                            </td>
                            <td>{{ $risk->likelihood }}</td>
                            <td>
                                <span class="badge
                                    @if($risk->risk_level == 'Low') bg-info
                                    @elseif($risk->risk_level == 'Medium') bg-warning
                                    @else bg-danger @endif">
                                    {{ $risk->risk_level }}
                                </span>
                            </td>
                            <td>{{ $risk->submitter ? $risk->submitter->name : '-' }}</td>
                            <td>{{ $risk->updated_at ? $risk->updated_at->diffForHumans() : '-' }}</td>
                            <td>
                                <div class="d-flex gap-2">
                                    <form action="{{ route('admin.risks.approve', $risk->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                    </form>
                                    <form action="{{ route('admin.risks.reject', $risk->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-danger">Reject</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                                    <h5 class="mt-2">No pending risks found</h5>
                                    <p>All risks have been approved or rejected.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if(isset($risks) && $risks->hasPages())
            <div class="d-flex justify-content-between align-items-center p-3">
                <div class="text-muted small">Showing {{ $risks->firstItem() }} to {{ $risks->lastItem() }} of {{ $risks->total() }} results</div>
                <div>{{ $risks->links() }}</div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
