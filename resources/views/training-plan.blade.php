@extends('layouts.dashboard')

@section('title', 'Training Plan')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <p class="text-secondary small mb-1">Action Plan / Action Plan Development / Training Plan</p>
        <h3 class="fw-bold">Training Plan</h3>
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
            <h5 class="card-title fw-bold">Training Plan List</h5>
            <div>
                <button class="btn btn-sm btn-outline-secondary">Edit Training</button>
                <button class="btn btn-sm btn-primary-light" data-bs-toggle="modal" data-bs-target="#addTrainingModal">Add Training</button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead><tr><th>No</th><th>Competency Area</th><th>Required Knowledge</th><th>Target Group</th><th>Competency Level</th><th>Training Needs</th><th>Training Method</th><th>Frequency</th></tr></thead>
                <tbody>
                    <tr><td>1</td><td>ISO 50001:2018...</td><td>ISO 50001 principles</td><td>All Staffs</td><td>1 to 4</td><td>Gap in standard...</td><td>Workshops</td><td>Initial + 2 years</td></tr>
                    <tr><td>2</td><td>Energy Review...</td><td>Identifying SEUs...</td><td>Energy Manager</td><td>2 to 5</td><td>Improve technical...</td><td>On-site analysis...</td><td>Initial + anually</td></tr>
                    <tr><td>3</td><td>Internal Auditing</td><td>Audits on EnMS...</td><td>Internal Auditor</td><td>3</td><td>ISO 50001 audit...</td><td>Certified auditor...</td><td>Every 3 years</td></tr>
                    <tr><td>4</td><td>Energy Efficient...</td><td>Procurement of...</td><td>Maintenance...</td><td>4</td><td>Lifecycle costing...</td><td>Policy training</td><td>Anually</td></tr>
                </tbody>
            </table>
        </div>
        <nav><ul class="pagination pagination-sm justify-content-end mb-0"><li class="page-item disabled"><a class="page-link" href="#">1 - 4 of 100</a></li><li class="page-item"><a class="page-link" href="#">&lt;</a></li><li class="page-item"><a class="page-link" href="#">&gt;</a></li></ul></nav>
    </div>
</div>

<div class="modal fade" id="addTrainingModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content" style="border-radius: 15px;">
      <div class="modal-header">
        <h5 class="modal-title fw-bold">Add Training Plan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <form>
            <div class="row">
                <div class="col-md-6 mb-3"><label class="form-label">Competency Area*</label><input type="text" class="form-control"></div>
                <div class="col-md-6 mb-3"><label class="form-label">Required Knowledge*</label><input type="text" class="form-control"></div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3"><label class="form-label">Target Group*</label><select class="form-select"><option selected>Choose...</option></select></div>
                <div class="col-md-6 mb-3"><label class="form-label">Competency Level*</label><select class="form-select"><option selected>Choose...</option></select></div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3"><label class="form-label">Training Needs*</label><input type="text" class="form-control"></div>
                <div class="col-md-6 mb-3"><label class="form-label">Training Method*</label><input type="text" class="form-control"></div>
            </div>
            <div class="mb-3">
                <label class="form-label">Frequency*</label>
                <input type="text" class="form-control">
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
