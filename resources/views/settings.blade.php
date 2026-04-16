@extends('layouts.settings')

@section('title', 'Settings')

@section('content')

<div style="height: 200px; background-image: url('{{ asset('images/background_setting.png') }}'); background-size: cover; background-position: center;"></div>

<div class="container pb-5">

    <div class="d-flex align-items-end" style="margin-top: -60px;">
        <div class="rounded-circle bg-primary-light d-inline-flex align-items-center justify-content-center z-1 shadow" style="border: 5px solid white; width: 130px; height: 130px;">
            <i class="bi bi-person-fill text-white" style="font-size: 80px;"></i>
        </div>
        <nav class="nav settings-tabs ps-4" id="settings-tabs">
            <a class="nav-link fs-5 active" data-bs-toggle="tab" href="#details-pane">My details</a>
            <a class="nav-link fs-5" data-bs-toggle="tab" href="#password-pane">Password</a>
            <a class="nav-link fs-5" data-bs-toggle="tab" href="#plan-pane">Plan</a>
            <a class="nav-link fs-5" data-bs-toggle="tab" href="#billing-pane">Billing</a>
            <a class="nav-link fs-5" data-bs-toggle="tab" href="#notifications-pane">Notifications</a>
        </nav>
    </div>

    <div class="tab-content mt-4">
        <div class="tab-pane fade show active" id="details-pane"><div class="bg-white p-4 p-md-5 rounded-3 shadow-sm">@include('settings._details-form')</div></div>
        <div class="tab-pane fade" id="password-pane"><div class="bg-white p-4 p-md-5 rounded-3 shadow-sm">@include('settings._password-form')</div></div>
        <div class="tab-pane fade" id="plan-pane"><div class="bg-white p-4 p-md-5 rounded-3 shadow-sm">@include('settings._plan-form')</div></div>
        <div class="tab-pane fade" id="billing-pane"><div class="bg-white p-4 p-md-5 rounded-3 shadow-sm">@include('settings._billing-info')</div></div>
        <div class="tab-pane fade" id="notifications-pane"><div class="bg-white p-4 p-md-5 rounded-3 shadow-sm"><p>Notifications settings content goes here.</p></div></div>
    </div>
</div>

@endsection
