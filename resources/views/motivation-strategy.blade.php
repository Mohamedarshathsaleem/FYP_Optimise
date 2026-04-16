@extends('layouts.dashboard')

@section('title', 'Motivation Strategy')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <p class="text-secondary small mb-1">Action Plan / Action Plan Development / Motivation Strategy</p>
        <h3 class="fw-bold">Motivation Strategy</h3>
    </div>
    <div class="d-flex align-items-center">
        <div class="input-group search-box me-3">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="text" class="form-control" placeholder="Search">
            <img src="{{ asset('images/user.png') }}" class="rounded-circle" alt="User" style="width: 40px; height: 40px;">
        </div>
    </div>
</div>

<p class="fw-bold text-primary-light">User Dashboard</p>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="card-title fw-bold">Motivation Strategy List</h5>
            <div>
                <button class="btn btn-sm btn-outline-secondary">Edit Motivation</button>
                <button class="btn btn-sm btn-primary-light" data-bs-toggle="modal" data-bs-target="#addMotivationModal">Add Motivation</button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead><tr><th>No</th><th>Motivation Activity</th><th>Target Group</th><th>Criteria for Recognition</th><th>Recognition Method</th><th>Frequency / Timing</th><th>Responsible Dept.</th><th>Remarks / Notes</th></tr></thead>
                <tbody>
                    <tr><td>1</td><td>Energy Efficiency Award</td><td>All departments</td><td>Department with highest %...</td><td>Award & certificate...</td><td>Annually</td><td>Energy Team / HR</td><td>Create energy dashboards...</td></tr>
                    <tr><td>2</td><td>Monthly Recognition in...</td><td>Individual employees</td><td>Most energy-saving...</td><td>Name and photo in...</td><td>Monthly</td><td>HR / Comm.</td><td>Encourage ideas via a...</td></tr>
                    <tr><td>3</td><td>Team Lunch or Voucher...</td><td>Teams achieving group...</td><td>Achieve EnPI reduction goals...</td><td>Lunch outing</td><td>Quarterly</td><td>Energy Team</td><td>Link reward to measurable...</td></tr>
                    <tr><td>4</td><td>Wall of fame</td><td>Top-performing...</td><td>Most energy-efficient SEU...</td><td>Display on notice...</td><td>Bi-annually</td><td>Admin</td><td>Track progress via EnPIs...</td></tr>
                </tbody>
            </table>
        </div>
        <nav><ul class="pagination pagination-sm justify-content-end mb-0"><li class="page-item disabled"><a class="page-link" href="#">1 - 4 of 100</a></li><li class="page-item"><a class="page-link" href="#">&lt;</a></li><li class="page-item"><a class="page-link" href="#">&gt;</a></li></ul></nav>
    </div>
</div>

<div class="modal fade" id="addMotivationModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content" style="border-radius: 15px;">
      <div class="modal-header">
        <h5 class="modal-title fw-bold">Add Motivation Strategy</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <form>
            <div class="row">
                <div class="col-md-6 mb-3"><label class="form-label">Motivation Activity*</label><input type="text" class="form-control"></div>
                <div class="col-md-6 mb-3"><label class="form-label">Target Group*</label><input type="text" class="form-control"></div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3"><label class="form-label">Criteria for Recognition*</label><input type="text" class="form-control"></div>
                <div class="col-md-6 mb-3"><label class="form-label">Recognition Method*</label><input type="text" class="form-control"></div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3"><label class="form-label">Frequency / Timing*</label><select class="form-select"><option selected>Choose...</option></select></div>
                <div class="col-md-6 mb-3"><label class="form-label">Responsive Method*</label><input type="text" class="form-control"></div>
            </div>
            <div class="mb-3">
                <label class="form-label">Remarks*</label>
                <textarea class="form-control" rows="4"></textarea>
            </div>
        </form>
      </div>
      <div class="modal-footer justify-content-center border-0">
        <button type="button" class="btn btn-primary-light btn-lg px-5">Add Now</button>
      </div>
    </div>
  </div>
</div>
@endsection
