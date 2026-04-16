@extends('layouts.dashboard')

@section('title', 'Edit Permission')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-6">

            <h2 class="mb-4">Edit Permission Module</h2>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @php
                // Pisah permission ke module dan action
                $parts = explode('.', $permission->name);
                $module = $parts[0] ?? '';
                $action = $parts[1] ?? '';
                $actions = ['view', 'add', 'edit', 'delete', 'import'];
            @endphp

            <form action="{{ route('admin.permissions.update', $permission) }}" method="POST" class="card shadow-sm border-0">
                @csrf
                @method('PUT')

                <div class="card-body p-4">

                    <div class="mb-3">
                        <label for="module_name" class="form-label fw-semibold">Module Name</label>
                        <input type="text" name="module_name" id="module_name" class="form-control form-control-lg" value="{{ old('module_name', $module) }}" required>
                        <small class="text-muted">Enter the base module name without the action</small>
                    </div>

                    <fieldset class="mb-3">
                        <legend class="fw-semibold mb-2">Action</legend>

                        <div class="d-flex gap-3 flex-wrap">
                            @foreach($actions as $act)
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="radio" name="action" id="action_{{ $act }}" value="{{ $act }}"
                                    {{ old('action', $action) === $act ? 'checked' : '' }} required>
                                <label class="form-check-label text-capitalize" for="action_{{ $act }}">{{ $act }}</label>
                            </div>
                            @endforeach
                        </div>
                    </fieldset>

                    <div class="mb-4">
                        <label for="description" class="form-label fw-semibold">Description (optional)</label>
                        <textarea name="description" id="description" rows="3" class="form-control">{{ old('description', $permission->description) }}</textarea>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.permissions.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4">Save Changes</button>
                    </div>

                </div>
            </form>

        </div>
    </div>
</div>
@endsection
