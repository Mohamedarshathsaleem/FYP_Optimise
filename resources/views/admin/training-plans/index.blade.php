@extends('layouts.dashboard')

@section('title', 'Training Plan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <p class="text-secondary small mb-1">Action Plan / Action Plan Development / Training Plan</p>
        <h3 class="fw-bold">Training Plan</h3>
    </div>
    <div class="d-flex align-items-center">
        <form method="GET" action="{{ route('admin.training-plans.index') }}" class="input-group search-box me-3">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="text" name="search" class="form-control" placeholder="Search competency, target group..." 
                   value="{{ request('search') }}">
            <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
        </form>
        <img src="{{ asset('images/user.png') }}" class="rounded-circle" alt="User" style="width: 40px; height: 40px;">
    </div>
</div>

{{-- FLASH MESSAGE --}}
@if (session('success'))
<div class="alert alert-success alert-dismissible fade show border-0" role="alert">
    <i class="bi bi-check-circle-fill me-2"></i>
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<p class="fw-bold text-primary-light mb-4">📊 User Dashboard - {{ $trainingPlans->total() }} records</p>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="card-title fw-bold mb-0">
                <i class="bi bi-list-ul me-2"></i>Training Plan List
            </h5>
            <div>
                <button class="btn btn-sm btn-outline-secondary me-2">
                    <i class="bi bi-pencil-square"></i> Edit
                </button>
                <button class="btn btn-sm btn-primary-light" data-bs-toggle="modal" data-bs-target="#addTrainingModal">
                    <i class="bi bi-plus-circle me-1"></i>Add Training
                </button>
            </div>
        </div>

        {{-- TABLE --}}
        @if($trainingPlans->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-4">
                <thead class="table-light">
                    <tr>
                        <th width="60">#</th>
                        <th>Competency Area</th>
                        <th>Knowledge</th>
                        <th>Target Group</th>
                        <th>Level</th>
                        <th>Needs</th>
                        <th>Method</th>
                        <th>Frequency</th>
                        <th width="80">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($trainingPlans as $index => $plan)
                    <tr>
                        <td>
                            <strong>{{ $trainingPlans->firstItem() + $index }}</strong>
                        </td>
                        <td>
                            <div class="fw-semibold">{{ Str::limit($plan->competency_area, 30) }}</div>
                            <small class="text-muted">{{ $plan->created_at->format('d M Y') }}</small>
                        </td>
                        <td>{{ Str::limit($plan->required_knowledge, 25) }}</td>
                        <td>
                            <span class="badge bg-info">{{ $plan->target_group }}</span>
                        </td>
                        <td>
                            @php $badgeClass = match($plan->competency_level) {
                                '1','2','3','4' => 'bg-primary',
                                '1 to 4','1 to 5' => 'bg-success',
                                '2 to 5' => 'bg-warning',
                                default => 'bg-secondary'
                            }; @endphp
                            <span class="badge {{ $badgeClass }} fs-6">
                                {{ $plan->competency_level }}
                            </span>
                        </td>
                        <td>{{ Str::limit($plan->training_needs, 20) }}</td>
                        <td>
                            <span class="badge bg-light text-dark px-2 py-1">
                                {{ Str::limit($plan->training_method, 15) }}
                            </span>
                        </td>
                        <td>{{ $plan->frequency }}</td>
                        <td>
                            <form action="{{ route('admin.training-plans.destroy', $plan) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" 
                                        onclick="return confirm('🗑️ Hapus "{{ $plan->competency_area }}"?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        <div class="d-flex justify-content-between align-items-center">
            <small class="text-muted">
                Showing {{ $trainingPlans->firstItem() }} to {{ $trainingPlans->lastItem() }} 
                of {{ $trainingPlans->total() }} results
            </small>
            {{ $trainingPlans->links() }}
        </div>

        @else
        <div class="text-center py-5">
            <i class="bi bi-clipboard-data display-1 text-muted mb-3"></i>
            <h5 class="text-muted mb-2">No Training Plans Yet</h5>
            <p class="text-muted mb-4">Start by adding your first training plan below.</p>
            <button class="btn btn-primary-light" data-bs-toggle="modal" data-bs-target="#addTrainingModal">
                <i class="bi bi-plus-circle me-2"></i>Add First Training
            </button>
        </div>
        @endif
    </div>
</div>

{{-- MODAL ADD TRAINING --}}
<div class="modal fade" id="addTrainingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 shadow">
            <form action="{{ route('admin.training-plans.store') }}" method="POST">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <div>
                        <h5 class="modal-title fw-bold mb-0">
                            <i class="bi bi-plus-circle-fill text-primary me-2"></i>
                            Add New Training Plan
                        </h5>
                        <small class="text-muted">Fill all required fields (*)</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    {{-- Competency Area & Knowledge --}}
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Competency Area <span class="text-danger">*</span></label>
                            <input type="text" name="competency_area" class="form-control @error('competency_area') is-invalid @enderror" 
                                   value="{{ old('competency_area') }}" required placeholder="e.g. ISO 50001 Implementation">
                            @error('competency_area') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Required Knowledge <span class="text-danger">*</span></label>
                            <textarea name="required_knowledge" class="form-control @error('required_knowledge') is-invalid @enderror" 
                                      rows="2" required placeholder="e.g. ISO 50001 principles">{{ old('required_knowledge') }}</textarea>
                            @error('required_knowledge') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    {{-- Target Group & Competency Level --}}
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Target Group <span class="text-danger">*</span></label>
                            <input type="text" name="target_group" class="form-control @error('target_group') is-invalid @enderror" 
                                   value="{{ old('target_group') }}" required placeholder="e.g. All Staffs">
                            @error('target_group') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Competency Level <span class="text-danger">*</span></label>
                            <select name="competency_level" class="form-select @error('competency_level') is-invalid @enderror" required>
                                <option value="">Choose level...</option>
                                <option value="1" {{ old('competency_level') == '1' ? 'selected' : '' }}>Level 1</option>
                                <option value="2" {{ old('competency_level') == '2' ? 'selected' : '' }}>Level 2</option>
                                <option value="1 to 4" {{ old('competency_level') == '1 to 4' ? 'selected' : '' }}>1 to 4</option>
                                <option value="1 to 5" {{ old('competency_level') == '1 to 5' ? 'selected' : '' }}>1 to 5</option>
                                <option value="2 to 5" {{ old('competency_level') == '2 to 5' ? 'selected' : '' }}>2 to 5</option>
                                <option value="3" {{ old('competency_level') == '3' ? 'selected' : '' }}>Level 3</option>
                                <option value="4" {{ old('competency_level') == '4' ? 'selected' : '' }}>Level 4</option>
                            </select>
                            @error('competency_level') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    {{-- Training Needs & Method --}}
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Training Needs <span class="text-danger">*</span></label>
                            <textarea name="training_needs" class="form-control @error('training_needs') is-invalid @enderror" 
                                      rows="2" required placeholder="e.g. Gap in standard understanding">{{ old('training_needs') }}</textarea>
                            @error('training_needs') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Training Method <span class="text-danger">*</span></label>
                            <input type="text" name="training_method" class="form-control @error('training_method') is-invalid @enderror" 
                                   value="{{ old('training_method') }}" required placeholder="e.g. Workshops">
                            @error('training_method') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    {{-- Frequency --}}
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Frequency <span class="text-danger">*</span></label>
                        <input type="text" name="frequency" class="form-control @error('frequency') is-invalid @enderror" 
                               value="{{ old('frequency') }}" required placeholder="e.g. Initial + 2 years">
                        @error('frequency') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="modal-footer border-0 justify-content-center gap-3 pt-0">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary-light btn-lg px-5">
                        <i class="bi bi-check-circle-fill me-2"></i>Add Training Plan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection