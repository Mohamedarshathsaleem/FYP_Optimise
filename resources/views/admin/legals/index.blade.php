@extends('layouts.dashboard')

@section('title', 'Legislation & Regulation')

<meta name="csrf-token" content="{{ csrf_token() }}">
    
@section('page-title', 'Legislation & Regulation')
@section('page-title-main', 'Legislation & Regulation')

@section('content')


@include('partials._header-dashboard')


    
    <!-- Instructions Section -->
    <div class="card border-0 shadow-sm mb-4" id="instructionsCard"
        style="background: #e3f2fd; border-left: 4px solid #2196f3;">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-start">
                <div class="d-flex align-items-start">
                    <div class="me-3">
                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 30px; height: 30px;">
                            <i class="bi bi-info-lg text-white"></i>
                        </div>
                    </div>
                    <div>
                        <h6 class="fw-bold text-primary mb-2">Instructions</h6>
                        <p class="mb-0 text-dark lh-base">
                            This section documents applicable legislation and regulations relevant to the Energy Management
                            System
                            in accordance with <strong>Clause 4.2 – Understanding the Needs and Expectations of Interested
                                Parties</strong>.
                        </p>
                    </div>
                </div>
                <button class="btn-close" onclick="closeInstructions()"></button>
            </div>
        </div>
    </div>


    <div class="d-flex justify-content-end mb-4">
        <div class="d-flex gap-2">
            @if (auth()->user()->hasPermission('legals.add'))
                <div class="d-flex justify-content-end mb-4">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLegalModal">Add
                        Legal</button>
                </div>
            @endif
            @if (auth()->user()->hasPermission('legals.approval'))
                <a href="{{ route('admin.legals.approval') }}" class="btn btn-outline-info">
                    Approval Page
                </a>
            @endif


            @if (auth()->user()->hasPermission('legals.export'))
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" id="exportDropdown"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        Export
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="exportDropdown">
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2" href="#">
                                <img src="{{ asset('images/pdf.png') }}" alt="" style="width:20px;"> as PDF
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2" href="#">
                                <img src="{{ asset('images/excel.png') }}" alt="" style="width:20px;"> as Excel
                            </a>
                        </li>
                    </ul>
                </div>
            @endif

        </div>
    </div>

    <!-- Legislation & Regulation Section -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white p-4">
            <h5 class="fw-bold mb-0">Legislation & Regulation</h5>
        </div>
        <div class="card-body p-4">

            @forelse($legals ?? [] as $legal)
                <!-- Legal Card -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header text-white text-center py-3" style="background:#3965FF;">
                        <h5 class="mb-1 fw-bold">
                            {{ $legal->title ?? 'Guidelines on Energy Management System [GP/ST/No.46/2024]' }}</h5>
                        <h6 class="mb-0">{{ $legal->authority ?? 'Energy Commision (ST)' }}</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <p class="text-muted mb-0 fw-semibold">{{ $legal->legal_id ?? 'LR-ENMS-001' }}</p>
                            </div>
                            <div class="d-flex gap-2">
                                @if (auth()->user()->hasPermission('legals.edit'))
                                <button class="btn btn-sm btn-primary" onclick="editLegal({{ $legal->id ?? 1 }})"
                                    title="Edit">
                                    <i class="bi bi-pencil-fill"></i>
                                </button>
                                @endif
                                <button class="btn btn-sm btn-outline-primary" onclick="viewLegal({{ $legal->id ?? 1 }})"
                                    title="View">
                                    <i class="bi bi-file-text-fill"></i>
                                </button>
                                @if (auth()->user()->hasPermission('legals.delete'))
                                <button class="btn btn-sm btn-danger" onclick="deleteLegal({{ $legal->id ?? 1 }})"
                                    title="Delete">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                                @endif
                                <button class="btn btn-sm btn-secondary" title="More Options">
                                    <i class="bi bi-three-dots"></i>
                                </button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <td class="fw-semibold bg-light" style="width: 25%;">Relevant Clause / Section</td>
                                        <td style="width: 25%;">{{ $legal->relevant_clause ?? '4' }}</td>
                                        <td class="fw-semibold bg-light" style="width: 25%;">Reference to Others
                                            (Pre-requisite)</td>
                                        <td style="width: 25%;">{{ $legal->reference_others ?? 'LR-ENMS-001' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold bg-light">Category</td>
                                        <td>{{ $legal->category ?? 'Legal' }}</td>
                                        <td class="fw-semibold bg-light">Effective Date</td>
                                        <td>{{ $legal->effective_date ?? '2025-01-01' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold bg-light">Relevant (Y/N)</td>
                                        <td colspan="3">{{ $legal->relevant ?? 'Y' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold bg-light">Description of requirements</td>
                                        <td colspan="3">
                                            {{ $legal->description ?? 'A designated consumer with energy consumption above threshold falls under this act ( 25,800 GJ/year )' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold bg-light">What is affected by this requirement?</td>
                                        <td colspan="3">{{ $legal->what_affected ?? 'Company wide' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold bg-light">What action is required</td>
                                        <td colspan="3">
                                            {{ $legal->action_required ?? 'Check if energy consumption > 25800 GJ/year' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold bg-light">Responsible Person</td>
                                        <td colspan="3">{{ $legal->responsible_person ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold bg-light">Last Review Date</td>
                                        <td colspan="3">{{ $legal->last_review_date ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold bg-light">How often will this be reviewed</td>
                                        <td colspan="3">{{ $legal->review_frequency ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold bg-light">Does it require further action?</td>
                                        <td colspan="3">{{ $legal->further_action ?? 'See LR-EECA-002 to 007' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold bg-light">Compliance Status</td>
                                        <td colspan="3">{{ $legal->compliance_status ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold bg-light">Evidence of Compliance</td>
                                        <td colspan="3">{{ $legal->evidence_compliance ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold bg-light">Remarks / Notes</td>
                                        <td colspan="3">
                                            {{ $legal->remarks ?? 'Failure to appoint may result in a RM50,000 fine' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold bg-light">Last Updated</td>
                                        <td colspan="3">{{ $legal->updated_at?->diffForHumans() ?? '6 days ago' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @empty
            @endforelse

        </div>
    </div>

    <!-- Add Legal Modal -->
    <div class="modal fade" id="addLegalModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content" style="border-radius: 15px;">
                <div class="modal-header border-0 pb-2">
                    <h5 class="modal-title fw-bold text-dark">Add Legislation & Regulation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div id="addLegalAlert" class="alert alert-danger d-none" role="alert">
                        <ul class="mb-0" id="addLegalErrors"></ul>
                    </div>
                    <form id="addLegalForm">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">

                        <!-- Page 1 -->
                        <div class="form-page" id="page1">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-primary">Legislation / Regulation Title <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="title"
                                        placeholder="Enter legislation title" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-primary">Enforcing Authority / Agency <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" name="authority" required>
                                        <option value="">Choose...</option>
                                        <option value="Energy Commision (ST)">Energy Commision (ST)</option>
                                        <option value="Ministry of Energy">Ministry of Energy</option>
                                        <option value="Environmental Agency">Environmental Agency</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-primary">Relevant Clause / Section <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="relevant_clause"
                                        placeholder="Enter clause/section" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-primary">Reference to Others (Pre-requisite) <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="reference_others"
                                        placeholder="Enter reference" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-primary">Category <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" name="category" required>
                                        <option value="">Choose...</option>
                                        <option value="Legal">Legal</option>
                                        <option value="Regulatory">Regulatory</option>
                                        <option value="Standard">Standard</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-primary">Effective Date <span
                                            class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="effective_date" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-primary">Relevant (Y/N) <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" name="relevant" required>
                                        <option value="">Choose...</option>
                                        <option value="Y">Yes</option>
                                        <option value="N">No</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-primary">Description of requirements <span
                                        class="text-danger">*</span></label>
                                <textarea class="form-control" name="description" rows="4" placeholder="Enter description" required></textarea>
                            </div>
                        </div>

                        <!-- Page 2 -->
                        <div class="form-page d-none" id="page2">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-primary">What is affected by this requirement? <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="what_affected"
                                        placeholder="Enter what is affected" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-primary">What action is required <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="action_required"
                                        placeholder="Enter required action" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-primary">Responsible Person <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="responsible_person"
                                        placeholder="Enter responsible person" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-primary">Last Review Date <span
                                            class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="last_review_date" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-primary">How often will this be reviewed <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" name="review_frequency" required>
                                        <option value="">Choose...</option>
                                        <option value="Monthly">Monthly</option>
                                        <option value="Quarterly">Quarterly</option>
                                        <option value="Annually">Annually</option>
                                        <option value="Bi-annually">Bi-annually</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-primary">Does it require further action? <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" name="further_action_bool" required>
                                        <option value="">Choose...</option>
                                        <option value="Yes">Yes</option>
                                        <option value="No">No</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-primary">Further Action Details</label>
                                <input type="text" class="form-control" name="further_action"
                                    placeholder="Enter further action details">
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-primary">Compliance Status <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" name="compliance_status" required>
                                        <option value="">Choose...</option>
                                        <option value="Compliant">Compliant</option>
                                        <option value="In Progress">In Progress</option>
                                        <option value="Non-Compliant">Non-Compliant</option>
                                        <option value="Not Applicable">Not Applicable</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-primary">Evidence of Compliance <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="evidence_compliance"
                                        placeholder="Enter evidence" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-primary">Remarks / Notes <span
                                        class="text-danger">*</span></label>
                                <textarea class="form-control" name="remarks" rows="4" placeholder="Enter remarks" required></textarea>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-secondary" id="prevBtn" onclick="previousPage()"
                                style="display: none;">Previous</button>
                            <div class="ms-auto">
                                <button type="button" class="btn btn-success" id="nextBtn"
                                    onclick="nextPage()">Next</button>
                                <button type="submit" class="btn btn-primary" id="submitBtn" style="display: none;">
                                    <span class="spinner-border spinner-border-sm d-none me-2" role="status"></span>
                                    Add Now
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Legal Modal -->
    <div class="modal fade" id="editLegalModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content" style="border-radius: 15px;">
                <div class="modal-header border-0 pb-2">
                    <h5 class="modal-title fw-bold text-dark">Edit Legislation & Regulation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div id="editLegalAlert" class="alert alert-danger d-none" role="alert">
                        <ul class="mb-0" id="editLegalErrors"></ul>
                    </div>
                    <form id="editLegalForm">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="_method" value="PUT">
                        <input type="hidden" id="editLegalId" name="legal_id">

                        <!-- Edit Page 1 -->
                        <div class="form-page" id="editPage1">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-primary">Legislation / Regulation Title <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="title" id="editTitle" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-primary">Enforcing Authority / Agency <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" name="authority" id="editAuthority" required>
                                        <option value="">Choose...</option>
                                        <option value="Energy Commision (ST)">Energy Commision (ST)</option>
                                        <option value="Ministry of Energy">Ministry of Energy</option>
                                        <option value="Environmental Agency">Environmental Agency</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-primary">Relevant Clause / Section <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="relevant_clause"
                                        id="editRelevantClause" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-primary">Reference to Others (Pre-requisite) <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="reference_others"
                                        id="editReferenceOthers" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-primary">Category <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" name="category" id="editCategory" required>
                                        <option value="">Choose...</option>
                                        <option value="Legal">Legal</option>
                                        <option value="Regulatory">Regulatory</option>
                                        <option value="Standard">Standard</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-primary">Effective Date <span
                                            class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="effective_date"
                                        id="editEffectiveDate" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-primary">Relevant (Y/N) <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" name="relevant" id="editRelevant" required>
                                        <option value="">Choose...</option>
                                        <option value="Y">Yes</option>
                                        <option value="N">No</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-primary">Description of requirements <span
                                        class="text-danger">*</span></label>
                                <textarea class="form-control" name="description" id="editDescription" rows="4" required></textarea>
                            </div>
                        </div>

                        <!-- Edit Page 2 -->
                        <div class="form-page d-none" id="editPage2">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-primary">What is affected by this requirement? <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="what_affected"
                                        id="editWhatAffected" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-primary">What action is required <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="action_required"
                                        id="editActionRequired" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-primary">Responsible Person <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="responsible_person"
                                        id="editResponsiblePerson" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-primary">Last Review Date</label>
                                    <input type="date" class="form-control" name="last_review_date"
                                        id="editLastReviewDate">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-primary">How often will this be reviewed <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" name="review_frequency" id="editReviewFrequency"
                                        required>
                                        <option value="">Choose...</option>
                                        <option value="Monthly">Monthly</option>
                                        <option value="Quarterly">Quarterly</option>
                                        <option value="Annually">Annually</option>
                                        <option value="Bi-annually">Bi-annually</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-primary">Does it require further action? <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" name="further_action_bool" id="editFurtherActionBool"
                                        required>
                                        <option value="">Choose...</option>
                                        <option value="Yes">Yes</option>
                                        <option value="No">No</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-primary">Further Action Details</label>
                                <input type="text" class="form-control" name="further_action" id="editFurtherAction">
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-primary">Compliance Status <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" name="compliance_status" id="editComplianceStatus"
                                        required>
                                        <option value="">Choose...</option>
                                        <option value="Compliant">Compliant</option>
                                        <option value="In Progress">In Progress</option>
                                        <option value="Non-Compliant">Non-Compliant</option>
                                        <option value="Not Applicable">Not Applicable</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-primary">Evidence of Compliance <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="evidence_compliance"
                                        id="editEvidenceCompliance" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-primary">Remarks / Notes <span
                                        class="text-danger">*</span></label>
                                <textarea class="form-control" name="remarks" id="editRemarks" rows="4" required></textarea>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-secondary" id="editPrevBtn"
                                onclick="editPreviousPage()" style="display: none;">Previous</button>
                            <div class="ms-auto">
                                <button type="button" class="btn btn-success" id="editNextBtn"
                                    onclick="editNextPage()">Next</button>
                                <button type="submit" class="btn btn-primary" id="editSubmitBtn"
                                    style="display: none;">
                                    <span class="spinner-border spinner-border-sm d-none me-2" role="status"></span>
                                    Update Now
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- View Detail Modal -->
    <div class="modal fade" id="viewDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content" style="border-radius: 15px;">
                <div class="modal-header border-0 pb-2">
                    <h5 class="modal-title fw-bold text-dark">View Legal Document Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4" id="viewDetailContent">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 15px;">
                <div class="modal-body text-center p-4">
                    <div class="mb-3">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2" id="successTitle">Success!</h5>
                    <p class="text-muted mb-4" id="successMessage">Operation completed successfully.</p>
                    <button type="button" class="btn btn-primary px-4" data-bs-dismiss="modal"
                        style="border-radius: 10px;">OK</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Modal -->
    <div class="modal fade" id="errorModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 15px;">
                <div class="modal-body text-center p-4">
                    <div class="mb-3">
                        <i class="bi bi-exclamation-circle-fill text-danger" style="font-size: 4rem;"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2" id="errorTitle">Error!</h5>
                    <p class="text-muted mb-4" id="errorMessage">Something went wrong.</p>
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal"
                        style="border-radius: 10px;">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 15px;">
                <div class="modal-body text-center p-4">
                    <div class="mb-3">
                        <i class="bi bi-question-circle-fill text-warning" style="font-size: 4rem;"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2" id="confirmTitle">Are you sure?</h5>
                    <p class="text-muted mb-4" id="confirmMessage">This action cannot be undone.</p>
                    <div class="d-flex gap-2 justify-content-center">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal"
                            style="border-radius: 10px;">Cancel</button>
                        <button type="button" class="btn btn-danger px-4" id="confirmButton"
                            style="border-radius: 10px;">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 Legislation & Regulation page loaded');

            // Get CSRF token
            let csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                document.querySelector('input[name="_token"]')?.value ||
                '{{ csrf_token() }}';

            let currentPage = 1;
            const totalPages = 2;
            let editCurrentPage = 1;
            const editTotalPages = 2;

            // Modal Functions
            function showSuccessModal(title, message, callback = null) {
                document.getElementById('successTitle').textContent = title;
                document.getElementById('successMessage').textContent = message;
                const modal = new bootstrap.Modal(document.getElementById('successModal'));
                modal.show();

                if (callback) {
                    document.getElementById('successModal').addEventListener('hidden.bs.modal', function() {
                        callback();
                    }, {
                        once: true
                    });
                }
            }

            function showErrorModal(title, message) {
                document.getElementById('errorTitle').textContent = title;
                document.getElementById('errorMessage').textContent = message;
                const modal = new bootstrap.Modal(document.getElementById('errorModal'));
                modal.show();
            }

            function showConfirmModal(title, message, callback) {
                document.getElementById('confirmTitle').textContent = title;
                document.getElementById('confirmMessage').textContent = message;
                const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
                modal.show();

                document.getElementById('confirmButton').onclick = function() {
                    modal.hide();
                    callback();
                };
            }

            // Close instructions
            window.closeInstructions = function() {
                document.getElementById('instructionsCard').style.display = 'none';
            };

            // Multi-step form navigation for ADD
            window.nextPage = function() {
                if (currentPage < totalPages) {
                    document.getElementById(`page${currentPage}`).classList.add('d-none');
                    currentPage++;
                    document.getElementById(`page${currentPage}`).classList.remove('d-none');

                    document.getElementById('prevBtn').style.display = 'inline-block';

                    if (currentPage === totalPages) {
                        document.getElementById('nextBtn').style.display = 'none';
                        document.getElementById('submitBtn').style.display = 'inline-block';
                    }
                }
            };

            window.previousPage = function() {
                if (currentPage > 1) {
                    document.getElementById(`page${currentPage}`).classList.add('d-none');
                    currentPage--;
                    document.getElementById(`page${currentPage}`).classList.remove('d-none');

                    document.getElementById('nextBtn').style.display = 'inline-block';
                    document.getElementById('submitBtn').style.display = 'none';

                    if (currentPage === 1) {
                        document.getElementById('prevBtn').style.display = 'none';
                    }
                }
            };

            // Multi-step form navigation for EDIT
            window.editNextPage = function() {
                if (editCurrentPage < editTotalPages) {
                    document.getElementById(`editPage${editCurrentPage}`).classList.add('d-none');
                    editCurrentPage++;
                    document.getElementById(`editPage${editCurrentPage}`).classList.remove('d-none');

                    document.getElementById('editPrevBtn').style.display = 'inline-block';

                    if (editCurrentPage === editTotalPages) {
                        document.getElementById('editNextBtn').style.display = 'none';
                        document.getElementById('editSubmitBtn').style.display = 'inline-block';
                    }
                }
            };

            window.editPreviousPage = function() {
                if (editCurrentPage > 1) {
                    document.getElementById(`editPage${editCurrentPage}`).classList.add('d-none');
                    editCurrentPage--;
                    document.getElementById(`editPage${editCurrentPage}`).classList.remove('d-none');

                    document.getElementById('editNextBtn').style.display = 'inline-block';
                    document.getElementById('editSubmitBtn').style.display = 'none';

                    if (editCurrentPage === 1) {
                        document.getElementById('editPrevBtn').style.display = 'none';
                    }
                }
            };

            // Search functionality
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                searchInput.addEventListener('keyup', function() {
                    const searchTerm = this.value.toLowerCase();
                    const cards = document.querySelectorAll('.card .card-body');

                    cards.forEach(card => {
                        const text = card.textContent.toLowerCase();
                        const cardContainer = card.closest('.card');
                        if (cardContainer && !cardContainer.id === 'instructionsCard') {
                            cardContainer.style.display = text.includes(searchTerm) ? '' : 'none';
                        }
                    });
                });
            }

            // Add legal form
            const addForm = document.getElementById('addLegalForm');
            if (addForm) {
                addForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    console.log('📝 Add form submitted');

                    const btn = document.getElementById('submitBtn');
                    const spinner = btn.querySelector('.spinner-border');
                    const alertDiv = document.getElementById('addLegalAlert');
                    const errorsList = document.getElementById('addLegalErrors');

                    alertDiv.classList.add('d-none');
                    btn.disabled = true;
                    spinner.classList.remove('d-none');

                    const formData = new FormData(this);

                    fetch('{{ route('legals.store') }}', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => {
                            console.log('📡 Response status:', response.status);
                            if (!response.ok) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(data => {
                            console.log('✅ Response data:', data);
                            if (data.success) {
                                const addModal = bootstrap.Modal.getInstance(document.getElementById(
                                    'addLegalModal'));
                                addModal.hide();

                                showSuccessModal(
                                    'Legal Document Added!',
                                    'The legislation & regulation has been successfully added.',
                                    () => location.reload()
                                );
                            } else {
                                if (data.errors) {
                                    errorsList.innerHTML = '';
                                    Object.values(data.errors).forEach(errors => {
                                        errors.forEach(error => {
                                            const li = document.createElement('li');
                                            li.textContent = error;
                                            errorsList.appendChild(li);
                                        });
                                    });
                                    alertDiv.classList.remove('d-none');
                                } else {
                                    showErrorModal('Validation Error', data.message ||
                                        'Please check your input and try again.');
                                }
                            }
                        })
                        .catch(error => {
                            console.error('❌ Fetch error:', error);
                            showErrorModal('Network Error',
                                'Unable to connect to server. Please check your connection and try again.'
                                );
                        })
                        .finally(() => {
                            btn.disabled = false;
                            spinner.classList.add('d-none');
                        });
                });
            }

            // Edit legal form
            const editForm = document.getElementById('editLegalForm');
            if (editForm) {
                editForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    console.log('📝 Edit form submitted');

                    const id = document.getElementById('editLegalId').value;
                    const btn = document.getElementById('editSubmitBtn');
                    const spinner = btn.querySelector('.spinner-border');
                    const alertDiv = document.getElementById('editLegalAlert');
                    const errorsList = document.getElementById('editLegalErrors');

                    alertDiv.classList.add('d-none');
                    btn.disabled = true;
                    spinner.classList.remove('d-none');

                    const formData = new FormData(this);

                    fetch(`/legals/${id}`, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => {
                            if (!response.ok) throw new Error('Response not ok');
                            return response.json();
                        })
                        .then(data => {
                            console.log('✅ Edit response:', data);
                            if (data.success) {
                                const editModal = bootstrap.Modal.getInstance(document.getElementById(
                                    'editLegalModal'));
                                editModal.hide();

                                showSuccessModal(
                                    'Legal Document Updated!',
                                    'The legislation & regulation has been successfully updated.',
                                    () => location.reload()
                                );
                            } else {
                                if (data.errors) {
                                    errorsList.innerHTML = '';
                                    Object.values(data.errors).forEach(errors => {
                                        errors.forEach(error => {
                                            const li = document.createElement('li');
                                            li.textContent = error;
                                            errorsList.appendChild(li);
                                        });
                                    });
                                    alertDiv.classList.remove('d-none');
                                } else {
                                    showErrorModal('Update Error', data.message ||
                                        'Unable to update legal document.');
                                }
                            }
                        })
                        .catch(error => {
                            console.error('❌ Edit error:', error);
                            showErrorModal('Network Error',
                                'Unable to update legal document. Please try again.');
                        })
                        .finally(() => {
                            btn.disabled = false;
                            spinner.classList.add('d-none');
                        });
                });
            }

            // Function to populate edit form
            function populateEditForm(data) {
                document.getElementById('editLegalId').value = data.id;
                document.getElementById('editTitle').value = data.title || '';
                document.getElementById('editAuthority').value = data.authority || '';
                document.getElementById('editRelevantClause').value = data.relevant_clause || '';
                document.getElementById('editReferenceOthers').value = data.reference_others || '';
                document.getElementById('editCategory').value = data.category || '';
                document.getElementById('editEffectiveDate').value = data.effective_date || '';
                document.getElementById('editRelevant').value = data.relevant || '';
                document.getElementById('editDescription').value = data.description || '';
                document.getElementById('editWhatAffected').value = data.what_affected || '';
                document.getElementById('editActionRequired').value = data.action_required || '';
                document.getElementById('editResponsiblePerson').value = data.responsible_person || '';
                document.getElementById('editLastReviewDate').value = data.last_review_date || '';
                document.getElementById('editReviewFrequency').value = data.review_frequency || '';
                document.getElementById('editFurtherActionBool').value = data.further_action_bool || '';
                document.getElementById('editFurtherAction').value = data.further_action || '';
                document.getElementById('editComplianceStatus').value = data.compliance_status || '';
                document.getElementById('editEvidenceCompliance').value = data.evidence_compliance || '';
                document.getElementById('editRemarks').value = data.remarks || '';
            }

            // Function to show view detail content
            function showViewDetailContent(data) {
                const content = `
                <div class="card border-0 shadow-sm">
                    <div class="card-header text-white text-center py-3" style="background:#3965FF;">
                        <h5 class="mb-1 fw-bold">${data.title}</h5>
                        <h6 class="mb-0">${data.authority}</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <p class="text-muted mb-0 fw-semibold">${data.legal_id || 'N/A'}</p>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <td class="fw-semibold bg-light" style="width: 25%;">Relevant Clause / Section</td>
                                        <td style="width: 25%;">${data.relevant_clause || 'N/A'}</td>
                                        <td class="fw-semibold bg-light" style="width: 25%;">Reference to Others (Pre-requisite)</td>
                                        <td style="width: 25%;">${data.reference_others || 'N/A'}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold bg-light">Category</td>
                                        <td>${data.category || 'N/A'}</td>
                                        <td class="fw-semibold bg-light">Effective Date</td>
                                        <td>${data.effective_date || 'N/A'}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold bg-light">Relevant (Y/N)</td>
                                        <td colspan="3">${data.relevant || 'N/A'}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold bg-light">Description of requirements</td>
                                        <td colspan="3">${data.description || 'N/A'}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold bg-light">What is affected by this requirement?</td>
                                        <td colspan="3">${data.what_affected || 'N/A'}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold bg-light">What action is required</td>
                                        <td colspan="3">${data.action_required || 'N/A'}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold bg-light">Responsible Person</td>
                                        <td colspan="3">${data.responsible_person || 'N/A'}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold bg-light">Last Review Date</td>
                                        <td colspan="3">${data.last_review_date || 'N/A'}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold bg-light">How often will this be reviewed</td>
                                        <td colspan="3">${data.review_frequency || 'N/A'}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold bg-light">Does it require further action?</td>
                                        <td colspan="3">${data.further_action || 'N/A'}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold bg-light">Compliance Status</td>
                                        <td colspan="3">${data.compliance_status || 'N/A'}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold bg-light">Evidence of Compliance</td>
                                        <td colspan="3">${data.evidence_compliance || 'N/A'}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold bg-light">Remarks / Notes</td>
                                        <td colspan="3">${data.remarks || 'N/A'}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold bg-light">Last Updated</td>
                                        <td colspan="3">${data.updated_at || 'N/A'}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="text-center mt-4">
                    <button type="button" class="btn btn-primary px-5 py-2" data-bs-dismiss="modal" style="border-radius: 10px; min-width: 120px;">Close</button>
                </div>
            `;
                document.getElementById('viewDetailContent').innerHTML = content;
            }

            // Global functions for database records
            window.editLegal = function(id) {
                console.log('✏️ Edit legal:', id);

                fetch(`/legals/${id}/edit`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    })
                    .then(response => {
                        if (!response.ok) throw new Error('Response not ok');
                        return response.json();
                    })
                    .then(data => {
                        console.log('📝 Edit data received:', data);
                        populateEditForm(data);
                        const modal = new bootstrap.Modal(document.getElementById('editLegalModal'));
                        modal.show();
                    })
                    .catch(error => {
                        console.error('❌ Edit load error:', error);
                        showErrorModal('Load Error', 'Unable to load legal data. Please try again.');
                    });
            };

            window.viewLegal = function(id) {
                console.log('👁️ View legal:', id);

                window.location.href = `/legals/${id}/detail`;
            };

            window.deleteLegal = function(id) {
                console.log('🗑️ Delete legal:', id);

                showConfirmModal(
                    'Delete Legal Document',
                    'Are you sure you want to delete this legislation & regulation? This action cannot be undone.',
                    function() {
                        fetch(`/legals/${id}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': csrfToken,
                                    'Accept': 'application/json'
                                }
                            })
                            .then(response => {
                                if (!response.ok) throw new Error('Response not ok');
                                return response.json();
                            })
                            .then(data => {
                                if (data.success) {
                                    showSuccessModal(
                                        'Legal Document Deleted!',
                                        'The legislation & regulation has been successfully deleted.',
                                        () => location.reload()
                                    );
                                } else {
                                    showErrorModal('Delete Error', data.message ||
                                        'Unable to delete legal document.');
                                }
                            })
                            .catch(error => {
                                console.error('❌ Delete error:', error);
                                showErrorModal('Network Error',
                                    'Unable to delete legal document. Please try again.');
                            });
                    }
                );
            };

            // Global functions for default/demo data
            window.editLegalDefault = function(id) {
                console.log('✏️ Edit legal default:', id);

                // Sample data untuk demo
                const sampleData = {
                    id: id,
                    title: id === 1 ? 'Guidelines on Energy Management System [GP/ST/No.46/2024]' :
                        'Energy Efficiency and Conservation Act',
                    authority: 'Energy Commision (ST)',
                    relevant_clause: id === 1 ? '4' : '5',
                    reference_others: 'LR-ENMS-001',
                    category: 'Legal',
                    effective_date: '2025-01-01',
                    relevant: 'Y',
                    description: id === 1 ?
                        'A designated consumer with energy consumption above threshold falls under this act ( 25,800 GJ/year )' :
                        'Energy conservation and efficiency measures implementation requirements',
                    what_affected: id === 1 ? 'Company wide' : 'All energy consuming operations',
                    action_required: id === 1 ? 'Check if energy consumption > 25800 GJ/year' :
                        'Implement energy efficiency measures and reporting',
                    responsible_person: 'Energy Manager',
                    last_review_date: '2024-12-01',
                    review_frequency: 'Annually',
                    further_action_bool: 'Yes',
                    further_action: id === 1 ? 'See LR-EECA-002 to 007' :
                        'Implementation of energy monitoring system',
                    compliance_status: id === 1 ? 'Compliant' : 'In Progress',
                    evidence_compliance: id === 1 ? 'Energy consumption monitoring reports' :
                        'Energy audit reports, monitoring records',
                    remarks: id === 1 ? 'Failure to appoint may result in a RM50,000 fine' :
                        'Quarterly reporting required to ST'
                };

                populateEditForm(sampleData);
                const modal = new bootstrap.Modal(document.getElementById('editLegalModal'));
                modal.show();
            };

            window.viewLegalDefault = function(id) {
                console.log('👁️ View legal default:', id);

                const sampleData = {
                    id: id,
                    title: id === 1 ? 'Guidelines on Energy Management System [GP/ST/No.46/2024]' :
                        'Energy Efficiency and Conservation Act',
                    authority: 'Energy Commision (ST)',
                    legal_id: id === 1 ? 'LR-ENMS-001' : 'LR-EECA-002',
                    relevant_clause: id === 1 ? '4' : '5',
                    reference_others: 'LR-ENMS-001',
                    category: 'Legal',
                    effective_date: '2025-01-01',
                    relevant: 'Y',
                    description: id === 1 ?
                        'A designated consumer with energy consumption above threshold falls under this act ( 25,800 GJ/year )' :
                        'Energy conservation and efficiency measures implementation requirements',
                    what_affected: id === 1 ? 'Company wide' : 'All energy consuming operations',
                    action_required: id === 1 ? 'Check if energy consumption > 25800 GJ/year' :
                        'Implement energy efficiency measures and reporting',
                    responsible_person: 'Energy Manager',
                    last_review_date: id === 1 ? '-' : '2024-12-01',
                    review_frequency: id === 1 ? '-' : 'Annually',
                    further_action: id === 1 ? 'See LR-EECA-002 to 007' :
                        'Yes - Implementation of energy monitoring system',
                    compliance_status: id === 1 ? '-' : 'In Progress',
                    evidence_compliance: id === 1 ? '-' : 'Energy audit reports, monitoring records',
                    remarks: id === 1 ? 'Failure to appoint may result in a RM50,000 fine' :
                        'Quarterly reporting required to ST',
                    updated_at: id === 1 ? '6 days ago' : '3 days ago'
                };

                const modal = new bootstrap.Modal(document.getElementById('viewDetailModal'));
                modal.show();
                showViewDetailContent(sampleData);
            };

            window.deleteLegalDefault = function(id) {
                console.log('🗑️ Delete legal default:', id);

                const title = id === 1 ? 'Guidelines on Energy Management System [GP/ST/No.46/2024]' :
                    'Energy Efficiency and Conservation Act';

                showConfirmModal(
                    'Delete Legal Document',
                    `Are you sure you want to delete "${title}"? This action cannot be undone.`,
                    function() {
                        // Demo delete - just show success and hide the card
                        showSuccessModal(
                            'Legal Document Deleted!',
                            'The legislation & regulation has been successfully deleted.',
                            () => {
                                // Hide the specific card
                                const button = document.querySelector(
                                    `[onclick="deleteLegalDefault(${id})"]`);
                                if (button) {
                                    const card = button.closest('.card');
                                    if (card) {
                                        card.style.display = 'none';
                                    }
                                }
                            }
                        );
                    }
                );
            };

            // Reset modal forms when closed
            document.getElementById('addLegalModal').addEventListener('hidden.bs.modal', function() {
                currentPage = 1;
                document.getElementById('page1').classList.remove('d-none');
                document.getElementById('page2').classList.add('d-none');
                document.getElementById('prevBtn').style.display = 'none';
                document.getElementById('nextBtn').style.display = 'inline-block';
                document.getElementById('submitBtn').style.display = 'none';
                document.getElementById('addLegalForm').reset();
                document.getElementById('addLegalAlert').classList.add('d-none');
            });

            document.getElementById('editLegalModal').addEventListener('hidden.bs.modal', function() {
                editCurrentPage = 1;
                document.getElementById('editPage1').classList.remove('d-none');
                document.getElementById('editPage2').classList.add('d-none');
                document.getElementById('editPrevBtn').style.display = 'none';
                document.getElementById('editNextBtn').style.display = 'inline-block';
                document.getElementById('editSubmitBtn').style.display = 'none';
                document.getElementById('editLegalForm').reset();
                document.getElementById('editLegalAlert').classList.add('d-none');
            });
        });
    </script>

@endsection
