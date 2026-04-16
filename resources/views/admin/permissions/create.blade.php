@extends('layouts.dashboard')

@section('title', 'Add New Permission')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-6">

            <h2 class="mb-4">Add New Permission Module</h2>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.permissions.store') }}" method="POST" class="card shadow-sm border-0">
                @csrf
                <div class="card-body p-4">

                    <div class="mb-3">
                        <label for="module_name" class="form-label fw-semibold">Module Name</label>
                        <input type="text" name="module_name" id="module_name" class="form-control form-control-lg" placeholder="e.g. issues, dashboard" required>
                        <small class="text-muted">Enter the base module name without action suffix.</small>
                    </div>

                    <fieldset class="mb-3">
                        <legend class="fw-semibold mb-2">Assign Actions</legend>

                        @php
                        $actions = ['view', 'add', 'edit', 'delete', 'import'];
                        @endphp

                        <div class="d-flex gap-3 flex-wrap">
                            @foreach($actions as $action)
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="actions[]" value="{{ $action }}" id="action_{{ $action }}" checked>
                                <label class="form-check-label text-capitalize" for="action_{{ $action }}">{{ $action }}</label>
                            </div>
                            @endforeach
                        </div>

                        <small class="text-muted">Select which actions this module supports.</small>
                    </fieldset>

                    <div class="mb-4">
                        <label for="description" class="form-label fw-semibold">Description (optional)</label>
                        <textarea name="description" id="description" rows="3" class="form-control"></textarea>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.permissions.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4">Add Permissions</button>
                    </div>

                </div>
            </form>

        </div>
    </div>
</div>
@endsection
