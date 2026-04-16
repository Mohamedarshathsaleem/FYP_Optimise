@extends('layouts.dashboard')

@section('title', 'Settings')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <p class="text-secondary small mb-1">Pages / Settings</p>
        <h3 class="fw-bold">Settings</h3>
    </div>
    <div class="d-flex align-items-center">
        <div class="input-group search-box me-3">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="text" class="form-control" placeholder="Search">
        </div>
        <img src="{{ asset('images/user.png') }}" class="rounded-circle" alt="User" style="width:40px;height:40px;">
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-5 text-center">
        <div class="mb-4">
            <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                 style="width:80px;height:80px;background:#f0f4ff;">
                <i class="bi bi-tools" style="font-size:2rem;color:#4472C4;"></i>
            </div>
        </div>
        <h4 class="fw-bold text-dark mb-2">This Page is Under Development</h4>
        <p class="text-muted mb-0" style="max-width:420px;margin:auto;">
            Settings features are currently being built. Check back soon for updates.
        </p>
    </div>
</div>

@endsection
