@extends('layouts.dashboard')

@section('title', 'Action Plan Overview')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <p class="text-secondary small mb-1">Action Plan / Overview</p>
        <h3 class="fw-bold">Action Plan Overview</h3>
    </div>
    <div class="d-flex align-items-center">
        <div class="input-group search-box me-3">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="text" class="form-control" placeholder="Search">
            <img src="{{ asset('images/user.png') }}" class="rounded-circle" alt="User" style="width: 40px; height: 40px;">
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <h5 class="card-title fw-bold">Action Plan Overview</h5>
        <p class="text-muted">This page is under development.</p>
    </div>
</div>

@endsection
