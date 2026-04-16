@extends('layouts.dashboard')

@section('title', 'Main Dashboard')

@section('content')

@include('partials._header-dashboard')

<p class="fw-bold mb-4">User Dashboard</p>

<!-- ✅ 3 CARDS EXACT UI AWAL + DATA DB -->
<div class="row g-4 mb-4">
    <!-- Total Users -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4 d-flex align-items-center">
                <div class="p-3 bg-light rounded-circle me-3">
                    <i class="bi bi-people-fill fs-4 text-primary-light"></i>
                </div>
                <div>
                    <p class="text-secondary mb-0">Total Users</p>
                    <h4 class="fw-bold">{{ $total_users ?? 0 }}</h4>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Users Today -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4 d-flex align-items-center">
                <div class="p-3 bg-light rounded-circle me-3">
                    <i class="bi bi-person-check-fill fs-4 text-primary-light"></i>
                </div>
                <div>
                    <p class="text-secondary mb-0">Users Today</p>
                    <h4 class="fw-bold">{{ $users_today ?? 0 }}</h4>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Active 7d -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4 d-flex align-items-center">
                <div class="p-3 bg-light rounded-circle me-3">
                    <i class="bi bi-graph-up fs-4 text-primary-light"></i>
                </div>
                <div>
                    <p class="text-secondary mb-0">Active (7d)</p>
                    <h4 class="fw-bold">{{ $active_users ?? 0 }}</h4>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tasks Table (sama persis) -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="card-title fw-bold">Task</h5>
            <a href="#" class="text-secondary"><i class="bi bi-three-dots"></i></a>
        </div>
        <table class="table align-middle">
            <thead><tr><th>NAME</th><th>DATE</th><th>PROGRESS</th></tr></thead>
            <tbody>
                <tr><td>Task A</td><td>18 Apr 2021</td><td><div class="progress" style="height: 8px;"><div class="progress-bar" style="width: 80%;"></div></div></td></tr>
                <tr><td>Task B</td><td>18 Apr 2021</td><td><div class="progress" style="height: 8px;"><div class="progress-bar" style="width: 40%;"></div></div></td></tr>
                <tr><td>Task C</td><td>20 May 2021</td><td><div class="progress" style="height: 8px;"><div class="progress-bar" style="width: 95%;"></div></div></td></tr>
                <tr><td>Task D</td><td>12 Jul 2021</td><td><div class="progress" style="height: 8px;"><div class="progress-bar" style="width: 60%;"></div></div></td></tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Complex Table (sama persis) -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="card-title fw-bold">Complex Table</h5>
            <a href="#" class="text-secondary"><i class="bi bi-three-dots"></i></a>
        </div>
        <table class="table align-middle">
            <thead><tr><th>NAME</th><th>STATUS</th><th>DATE</th><th>PROGRESS</th></tr></thead>
            <tbody>
                <tr>
                    <td>Horizon UI PRO</td>
                    <td><span class="badge bg-success-subtle text-success-emphasis rounded-pill"><i class="bi bi-check-circle-fill"></i> Approved</span></td>
                    <td>18 Apr 2021</td>
                    <td><div class="progress" style="height: 8px;"><div class="progress-bar" style="width: 75%;"></div></div></td>
                </tr>
                <tr>
                    <td>Horizon UI Free</td>
                    <td><span class="badge bg-warning-subtle text-warning-emphasis rounded-pill"><i class="bi bi-exclamation-triangle-fill"></i> Disable</span></td>
                    <td>18 Apr 2021</td>
                    <td><div class="progress" style="height: 8px;"><div class="progress-bar" style="width: 25%;"></div></div></td>
                </tr>
                <tr>
                    <td>Marketplace</td>
                    <td><span class="badge bg-danger-subtle text-danger-emphasis rounded-pill"><i class="bi bi-x-circle-fill"></i> Error</span></td>
                    <td>20 May 2021</td>
                    <td><div class="progress" style="height: 8px;"><div class="progress-bar" style="width: 90%;"></div></div></td>
                </tr>
                <tr>
                    <td>Weekly Updates</td>
                    <td><span class="badge bg-success-subtle text-success-emphasis rounded-pill"><i class="bi bi-check-circle-fill"></i> Approved</span></td>
                    <td>12 Jul 2021</td>
                    <td><div class="progress" style="height: 8px;"><div class="progress-bar" style="width: 50%;"></div></div></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection