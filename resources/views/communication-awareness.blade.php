@extends('layouts.dashboard')

@section('title', 'Communication & Awareness')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <p class="text-secondary small mb-1">Action Plan / Action Plan Development / Communication & Awareness</p>
        <h3 class="fw-bold">Communication & Awareness</h3>
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
            <h5 class="card-title fw-bold">Communication & Awareness List</h5>
            <div>
                <button class="btn btn-sm btn-outline-secondary">Edit Communication</button>
                <button class="btn btn-sm btn-primary-light" data-bs-toggle="modal" data-bs-target="#addCommunicationModal">Add Communication</button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead><tr><th>No</th><th>Action / Initiative</th><th>Internal / External</th><th>Energy Message</th><th>Target Audience</th><th>Communication</th><th>Person In-Charge</th><th>Planned Date</th><th>Remarks</th></tr></thead>
                <tbody>
                    <tr><td>1</td><td>Energy Policy...</td><td>Internal</td><td>Introduction of...</td><td>All Employees</td><td>Email</td><td>Energy Manager</td><td>6/10/2025</td><td>Policy displayed at all locations</td></tr>
                    <tr><td>2</td><td>Energy-saving...</td><td>Internal</td><td>How to save...</td><td>All Office</td><td>WhatsApp group</td><td>Facility Supervisor</td><td>12/10/2021</td><td>Reinforce via toolbox talks</td></tr>
                    <tr><td>3</td><td>ISO 50001 Audit...</td><td>Internal</td><td>Audit schedule...</td><td>Department Head</td><td>Email</td><td>Compliance Offi...</td><td>17/10/2025</td><td>Conducted mock audit</td></tr>
                    <tr><td>4</td><td>Utility data...</td><td>External</td><td>Quarterly ener...</td><td>Management</td><td>PDF report</td><td>Data Analyst</td><td>18/10/2025</td><td>Report used for performance ...</td></tr>
                </tbody>
            </table>
        </div>
        <nav><ul class="pagination pagination-sm justify-content-end mb-0"><li class="page-item disabled"><a class="page-link" href="#">1 - 4 of 100</a></li><li class="page-item"><a class="page-link" href="#">&lt;</a></li><li class="page-item"><a class="page-link" href="#">&gt;</a></li></ul></nav>
    </div>
</div>

<div class="modal fade" id="addCommunicationModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content" style="border-radius: 15px;">
      <div class="modal-header">
        <h5 class="modal-title fw-bold">Add Communication & Awareness</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <form>
            <div class="row">
                <div class="col-md-6 mb-3"><label class="form-label">Action / Initiative*</label><input type="text" class="form-control"></div>
                <div class="col-md-6 mb-3"><label class="form-label">Internal / External*</label><select class="form-select"><option selected>Choose...</option></select></div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3"><label class="form-label">Energy Message*</label><input type="text" class="form-control"></div>
                <div class="col-md-6 mb-3"><label class="form-label">Target Audience*</label><select class="form-select"><option selected>Choose...</option></select></div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3"><label class="form-label">Communication*</label><select class="form-select"><option selected>Choose...</option></select></div>
                <div class="col-md-6 mb-3"><label class="form-label">Person In-Charge*</label><input type="text" class="form-control"></div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3"><label class="form-label">Planned Date*</label><input type="date" class="form-control"></div>
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
