@extends('layouts.dashboard')

@section('title', 'Stakeholder Approval')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary">Stakeholder Approval</h2>
        <a href="#" class="btn btn-outline-secondary d-flex gap-2 align-items-center">
            <i class="bi bi-arrow-left"></i> Back to Stakeholders
        </a>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white p-4 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0">Stakeholder List - Needs Approval</h5>
            <a href="#" class="btn btn-primary">Export</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Stakeholder</th>
                            <th>Type</th>
                            <th>Role / Interest</th>
                            <th>Needs & Expectations</th>
                            <th>Influence Level</th>
                            <th>Communication Method</th>
                            <th>Engagement Frequency</th>
                            <th>Responsible Person</th>
                            <th>Remarks</th>
                            <th>Modified</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stakeholders as $stakeholder)
                        <tr>
                            <td>{{ $stakeholder->stakeholder_id }}</td>
                            <td>{{ $stakeholder->name }}</td>
                            <td>{{ $stakeholder->type }}</td>
                            <td>{{ $stakeholder->role }}</td>
                            <td>{{ $stakeholder->needs_expectations }}</td>
                            <td>{{ $stakeholder->influence_level }}</td>
                            <td>{{ $stakeholder->communication_method }}</td>
                            <td>{{ $stakeholder->engagement_frequency }}</td>
                            <td>{{ $stakeholder->responsible_person }}</td>
                            <td>{{ $stakeholder->remarks }}</td>
                            <td>{{ $stakeholder->updated_at->diffForHumans() }}</td>
                            <td>
                                <div class="d-flex gap-2">
                                    <form action="{{ route('admin.stakeholders.approve', $stakeholder->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                    </form>
                                    <form action="{{ route('admin.stakeholders.reject', $stakeholder->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-danger">Reject</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="12" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                                    <h5 class="mt-2">No stakeholders pending approval</h5>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($stakeholders->hasPages())
            <div class="d-flex justify-content-between align-items-center p-3">
                <div class="text-muted small">Showing {{ $stakeholders->firstItem() }} to {{ $stakeholders->lastItem() }} of {{ $stakeholders->total() }} results</div>
                <div>{{ $stakeholders->links() }}</div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
