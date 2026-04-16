ti
@extends('layouts.app')

@section('title', 'My Details - Settings')

@section('content')

<div style="height: 200px; background-image: url('{{ asset('images/background_setting.png') }}'); background-size: cover; background-position: center;">
</div>
<div class="container pb-5">

    <div class="d-flex align-items-end" style="margin-top: -60px;">
        <div class="p-2 rounded-circle bg-primary-light d-inline-block z-1 shadow-sm">
            <i class="bi bi-person-fill text-white" style="font-size: 90px;"></i>
        </div>
        <nav class="nav nav-tabs border-0 ps-4">
            <a class="nav-link active fw-bold text-dark" href="#">My details</a>
            <a class="nav-link text-secondary" href="#">Password</a>
            <a class="nav-link text-secondary" href="#">Plan</a>
            <a class="nav-link text-secondary" href="#">Billing</a>
            <a class="nav-link text-secondary" href="#">Notifications</a>
        </nav>
    </div>

    <div class="bg-white p-4 p-md-5 rounded-3 mt-4 shadow-sm">
        <h3 class="fw-bold mb-4">Settings</h3>
        <form>
            <div class="row g-4">
                <div class="col-md-6">
                    <label for="fullName" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="fullName" placeholder="Your First Name">
                </div>
                <div class="col-md-6">
                    <label for="nickName" class="form-label">Nick Name</label>
                    <input type="text" class="form-control" id="nickName" placeholder="Your Nick Name">
                </div>
                <div class="col-md-6">
                    <label for="gender" class="form-label">Gender</label>
                    <select id="gender" class="form-select">
                        <option selected>Choose...</option>
                        <option>Male</option>
                        <option>Female</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="country" class="form-label">Country</label>
                    <input type="text" class="form-control" id="country">
                </div>
                <div class="col-md-6">
                    <label for="language" class="form-label">Language</label>
                    <input type="text" class="form-control" id="language">
                </div>
                <div class="col-md-6">
                    <label for="timeZone" class="form-label">Time Zone</label>
                    <input type="text" class="form-control" id="timeZone">
                </div>
            </div>

            <div class="d-flex justify-content-end mt-5">
                <button type="button" class="btn btn-light me-3">Cancel</button>
                <button type="submit" class="btn btn-primary-light px-4">Save</button>
            </div>
        </form>
    </div>
</div>

@endsection
