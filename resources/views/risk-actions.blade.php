@extends('layouts.dashboard')

@section('title', 'Actions to Address Risks')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <p class="text-secondary small mb-1">Pages / Internal & External Issues / Actions</p>
        <h3 class="fw-bold">Actions to Address Risks and Opportunities</h3>
    </div>
    <div class="d-flex align-items-center">
        <div class="input-group search-box me-3">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="text" class="form-control" placeholder="Search">
            <img src="{{ asset('images/user.png') }}" class="rounded-circle" alt="User" style="width: 40px; height: 40px;">
        </div>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <p class="fw-bold text-primary-green mb-0">User Dashboard</p>
    <div>
        <button class="btn btn-primary-light" data-bs-toggle="modal" data-bs-target="#addActionModal">Add Action</button>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table align-middle table-hover">
                <thead class="table-light">
                    <tr>
                        <th class="fw-semibold">No</th>
                        <th class="fw-semibold">Action to address risk/opportunity</th>
                        <th class="fw-semibold">Status</th>
                        <th class="fw-semibold">Process to evaluate effectiveness?</th>
                        <th class="fw-semibold">Effectiveness evaluation</th>
                        <th class="fw-semibold">Modified</th>
                        <th class="text-end fw-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Ensure demonstration of potential benefits to Municipal Leaders</td>
                        <td>Implemented</td>
                        <td>Yes</td>
                        <td>3</td>
                        <td>6 days ago</td>
                        <td class="text-end">
                             <div class="btn-group bg-light rounded px-1" role="group">
                                <a href="#" class="btn btn-sm btn-light border-0 text-secondary"><i class="bi bi-eye-fill"></i></a>
                                <a href="#" class="btn btn-sm btn-light border-0 text-primary"><i class="bi bi-pencil-fill"></i></a>
                                <a href="#" class="btn btn-sm btn-light border-0 text-danger"><i class="bi bi-trash-fill"></i></a>
                            </div>
                        </td>
                    </tr>
                    </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end align-items-center small text-muted">
            <span>1 - 5 of 100</span>
            <div class="ms-3">
                <a href="#" class="text-muted me-1"><i class="bi bi-chevron-left"></i></a>
                <a href="#" class="text-muted"><i class="bi bi-chevron-right"></i></a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addActionModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content" style="border-radius: 15px;">
      <div class="modal-header">
        <h5 class="modal-title fw-bold">Add Action to Address Risks and Opportunities</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <form>
            <div class="mb-3">
                <label class="form-label">Related Risks and Opportunities*</label>
                <input type="text" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">Action to Address Risk / Opportunity*</label>
                <input type="text" class="form-control">
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Status*</label>
                    <input type="text" class="form-control">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Process to Evaluate*</label>
                    <input type="text" class="form-control">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Effectiveness Evaluation*</label>
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
