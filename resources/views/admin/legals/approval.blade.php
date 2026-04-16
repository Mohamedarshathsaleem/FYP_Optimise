@extends('layouts.dashboard')
@section('title', 'Legal Approval')
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary">Legislation & Regulation Approval</h2>
        <a href="#" class="btn btn-outline-secondary d-flex gap-2 align-items-center">
            <i class="bi bi-arrow-left"></i> Back to Legal List
        </a>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white p-4 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0">Pending Legal Documents</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Authority</th>
                            <th>Category</th>
                            <th>Effective Date</th>
                            <th>Relevant</th>
                            <th>Status</th>
                            <th>Modified</th>
                            <!--<th>Detail</th>-->
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($legals as $legal)
                        <tr>
                            <td>{{ $legal->legal_id }}</td>
                            <td>{{ $legal->title }}</td>
                            <td>{{ $legal->authority }}</td>
                            <td>{{ $legal->category }}</td>
                            <td>{{ $legal->effective_date }}</td>
                            <td>{{ $legal->relevant }}</td>
                            <td>
                                <span class="badge 
                                    @if($legal->status_approval == 'pending') bg-warning
                                    @elseif($legal->status_approval == 'approved') bg-success
                                    @else bg-danger @endif">
                                    {{ ucfirst($legal->status_approval) }}
                                </span>
                            </td>
                            <td>{{ $legal->updated_at?->diffForHumans() }}</td>
                           
                            <td>
                                <div class="d-flex gap-2">
                                    <form action="{{ route('admin.legals.approve', $legal->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                    </form>
                                    <form action="{{ route('admin.legals.reject', $legal->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-danger">Reject</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                                    <h5 class="mt-2">No legal documents pending approval</h5>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($legals->hasPages())
            <div class="d-flex justify-content-between align-items-center p-3">
                <div class="text-muted small">Showing {{ $legals->firstItem() }} to {{ $legals->lastItem() }} of {{ $legals->total() }} results</div>
                <div>{{ $legals->links() }}</div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
