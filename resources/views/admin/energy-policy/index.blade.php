@extends('layouts.dashboard')

@section('title', 'Energy Policy')

@section('content')
    <!-- CSRF Token Meta Tag -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <p class="text-secondary small mb-1">Pages / Energy Policy</p>
            <h3 class="fw-bold">Energy Policy</h3>
        </div>
        <div class="d-flex align-items-center">
            <div class="input-group search-box me-3">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control" id="searchInput" placeholder="Search">
            </div>
            <img src="{{ asset('images/user.png') }}" class="rounded-circle" alt="User"
                style="width: 40px; height: 40px;">
        </div>
    </div>

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
                       <ul class="mb-0 text-dark lh-base">
                           <li>Please form an energy team and obtained authority from top management for it
                            to oversee the EnMS and carry out assigned responsibilities
                        </li>
                        <li>
                         Please complete the energy team using the table
                        </li>
                             </ul>
                    </div>
                </div>
                <button class="btn-close" onclick="closeInstructions()"></button>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="d-flex justify-content-end mb-4">
        <div class="d-flex gap-2">
            <div class="dropdown">
                @if (auth()->user()->hasPermission('energy-policy.add')) 
                <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    Add Policy
                </button>
                @endif
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" onclick="openUseTemplateModal()">Use template</a></li>
                    <li><a class="dropdown-item" href="#" onclick="openUploadPolicyModal()">Upload own Policy</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Edit Policy Modal -->
    <div class="modal fade" id="editPolicyModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" style="border-radius: 15px;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark">Edit Energy Policy</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div id="editPolicyAlert" class="alert alert-danger d-none" role="alert">
                        <ul class="mb-0" id="editPolicyErrors"></ul>
                    </div>
                    <form id="editPolicyForm">
                        @csrf
                        <input type="hidden" name="_method" value="PUT">
                        <input type="hidden" id="editPolicyId" name="policy_id">

                        <div class="mb-3">
                            <label class="form-label">Policy Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="title" id="editTitle" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Company Name</label>
                            <input type="text" class="form-control" name="company_name" id="editCompanyName">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Policy Statement</label>
                            <textarea class="form-control" name="policy_statement" id="editPolicyStatement" rows="4"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Energy Standard</label>
                            <select class="form-select" name="energy_standard" id="editEnergyStandard">
                                <option value="">Select standard...</option>
                                <option value="ISO 50001:2018">ISO 50001:2018</option>
                                <option value="ISO 50001:2011">ISO 50001:2011</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" id="editStatus">
                                <option value="draft">Draft</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-primary px-5 py-2"
                                style="border-radius: 8px; min-width: 120px;">
                                <span class="spinner-border spinner-border-sm d-none me-2" role="status"></span>
                                Update Policy
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirm Delete Modal -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 15px;">
                <div class="modal-body text-center p-4">
                    <div class="mb-3">
                        <i class="bi bi-question-circle-fill text-warning" style="font-size: 4rem;"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Delete Energy Policy</h5>
                    <p class="text-muted mb-4">Are you sure you want to delete this policy? This action cannot be undone.
                    </p>
                    <div class="d-flex gap-2 justify-content-center">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal"
                            style="border-radius: 10px;">Cancel</button>
                        <button type="button" class="btn btn-danger px-4" id="confirmDeleteBtn"
                            style="border-radius: 10px;">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Policy List -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white p-4">
            <h5 class="fw-bold mb-0">Energy Policy List</h5>
        </div>
        <div class="card-body p-4">
            @forelse($energyPolicies ?? [] as $index => $policy)
                <div class="row align-items-center py-3 border-bottom policy-item">
                    <div class="col-md-1">
                        <span class="fw-semibold">{{ $index + 1 }}</span>
                    </div>
                    <div class="col-md-2">
                        <span class="text-primary fw-semibold">{{ $policy->title }}</span>
                        @if ($policy->status === 'approved')
                            <span class="badge bg-success ms-1">Approved</span>
                        @elseif($policy->status === 'rejected')
                            <span class="badge bg-danger ms-1">Rejected</span>
                        @else
                            <span class="badge bg-warning ms-1">Draft</span>
                        @endif
                    </div>
                    <div class="col-md-3">
                        <span class="text-muted">{{ Str::limit($policy->summary ?? 'No summary', 50) }}</span>
                    </div>
                    <div class="col-md-2">
                        <span class="text-muted small">{{ $policy->updated_at->diffForHumans() }}</span>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex gap-2 justify-content-end">
                            <button class="btn btn-sm btn-outline-info" onclick="viewPolicy({{ $policy->id }})"
                                title="View">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-primary" onclick="editPolicy({{ $policy->id }})"
                                title="Edit">
                                <i class="bi bi-pencil-fill"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="deletePolicy({{ $policy->id }})"
                                title="Delete">
                                <i class="bi bi-trash-fill"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <i class="bi bi-file-text" style="font-size: 3rem; color: #6c757d;"></i>
                    <h5 class="mt-2 text-muted">No Energy Policies Found</h5>
                    <p class="text-muted">Start by creating your first energy policy.</p>
                    <button class="btn btn-primary" onclick="openUseTemplateModal()">
                        <i class="bi bi-plus-circle me-1"></i>Create First Policy
                    </button>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Use Template Modal - SESUAI FIGMA -->
    <div class="modal fade" id="useTemplateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content" style="border-radius: 15px;">
                <div class="modal-header border-0 pb-2">
                    <h5 class="modal-title fw-bold text-dark">Use Energy Policy Template</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body pt-0">
                    <form id="useTemplateForm">
                        @csrf

                        <!-- Step 1: PERSIS FIGMA - Table Format -->
                        <div class="template-step" id="template-step-1" style="display: block;">
                            <h6 class="fw-bold mb-3">Draft your Energy Policy Statement</h6>

                            <!-- Table Section 1: Energy policy includes commitments -->
                            <div class="mb-4">
                                <table class="table table-bordered">
                                    <thead style="background: #4285f4; color: white;">
                                        <tr>
                                            <th colspan="2">Energy policy includes commitments to the following:</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td style="width: 50px; text-align: center;">
                                                <input class="form-check-input" type="checkbox" name="commitments[]"
                                                    value="A commitment to ensure the availability of information and necessary resources to achieve objectives and energy targets"
                                                    id="commit1">
                                            </td>
                                            <td>A commitment to ensure the availability of information and necessary
                                                resources to achieve objectives and energy targets</td>
                                        </tr>
                                        <tr>
                                            <td style="width: 50px; text-align: center;">
                                                <input class="form-check-input" type="checkbox" name="commitments[]"
                                                    value="A commitment to satisfy applicable legal requirements and other requirements related to energy efficiency, energy use and energy consumption"
                                                    id="commit2" checked>
                                            </td>
                                            <td>A commitment to satisfy applicable legal requirements and other requirements
                                                related to energy efficiency, energy use and energy consumption</td>
                                        </tr>
                                        <tr>
                                            <td style="width: 50px; text-align: center;">
                                                <input class="form-check-input" type="checkbox" name="commitments[]"
                                                    value="A commitment to continual improvement of energy performance and the EnMS"
                                                    id="commit3">
                                            </td>
                                            <td>A commitment to continual improvement of energy performance and the EnMS
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width: 50px; text-align: center;">
                                                <input class="form-check-input" type="checkbox" name="commitments[]"
                                                    value="A commitment to support the procurement of energy efficient products and services that impact energy performance"
                                                    id="commit4" checked>
                                            </td>
                                            <td>A commitment to support the procurement of energy efficient products and
                                                services that impact energy performance</td>
                                        </tr>
                                        <tr>
                                            <td style="width: 50px; text-align: center;">
                                                <input class="form-check-input" type="checkbox" name="commitments[]"
                                                    value="A commitment to communicate the energy policy within the organization"
                                                    id="commit5">
                                            </td>
                                            <td>A commitment to communicate the energy policy within the organization</td>
                                        </tr>
                                        <tr>
                                            <td style="width: 50px; text-align: center;">
                                                <input class="form-check-input" type="checkbox" name="commitments[]"
                                                    value="A commitment to review periodically and update it as necessary"
                                                    id="commit6">
                                            </td>
                                            <td>A commitment to review periodically and update it as necessary</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Table Section 2: Enter your policy statement -->
                            <div class="mb-4">
                                <table class="table table-bordered">
                                    <thead style="background: #4285f4; color: white;">
                                        <tr>
                                            <th>Enter your policy statement</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <textarea class="form-control border-0" name="custom_policy_statement" id="customPolicyStatement" rows="4"
                                                    placeholder="Meet all relevant legal and regulatory requirements, as well as other obligations related to energy use, energy efficiency, and energy consumption that the company subscribes to.

Make this policy visible and accessible throughout the organization, and link it to daily operations. We will encourage employee involvement and integrate energy management responsibilities into roles and functions.">Meet all relevant legal and regulatory requirements, as well as other obligations related to energy use, energy efficiency, and energy consumption that the company subscribes to.

Make this policy visible and accessible throughout the organization, and link it to daily operations. We will encourage employee involvement and integrate energy management responsibilities into roles and functions.</textarea>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Table Section 3: Energy Policy Completion -->
                            <div class="mb-4">
                                <table class="table table-bordered">
                                    <thead style="background: #4285f4; color: white;">
                                        <tr>
                                            <th colspan="2">Energy Policy Completion</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td style="width: 50px; text-align: center;">
                                                <input class="form-check-input" type="checkbox"
                                                    name="policy_completion[]" value="completed" id="completed">
                                            </td>
                                            <td>Energy policy has been completed:</td>
                                        </tr>
                                        <tr>
                                            <td style="width: 50px; text-align: center;">
                                                <input class="form-check-input" type="checkbox"
                                                    name="policy_completion[]" value="date_completed"
                                                    id="date_completed">
                                            </td>
                                            <td>Date completed:</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Table Section 4: Top Management Approval -->
                            <div class="mb-4">
                                <table class="table table-bordered">
                                    <thead style="background: #4285f4; color: white;">
                                        <tr>
                                            <th colspan="2">Top Management Approval</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td style="width: 50px; text-align: center;">
                                                <input class="form-check-input" type="checkbox" name="approval[]"
                                                    value="date_approved" id="date_approved">
                                            </td>
                                            <td>Date approved:</td>
                                        </tr>
                                        <tr>
                                            <td style="width: 50px; text-align: center;">
                                                <input class="form-check-input" type="checkbox" name="approval[]"
                                                    value="who_approved" id="who_approved">
                                            </td>
                                            <td>Who approved:</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="text-end">
                                <button type="button" class="btn btn-primary px-4 py-2" onclick="nextTemplateStep()"
                                    style="border-radius: 8px;">Next</button>
                            </div>
                        </div>

                        <!-- Step 2: PERSIS FIGMA - Company Details -->
                        <div class="template-step" id="template-step-2" style="display: none;">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-4">
                                        <label class="form-label fw-bold">Company Name <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="company_name"
                                            style="background: #f8f9fa;" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-4">
                                        <label class="form-label fw-bold">Company Logo <span
                                                class="text-danger">*</span></label>
                                        <div class="upload-area d-flex flex-column align-items-center justify-content-center"
                                            onclick="document.getElementById('templateLogoUpload').click()"
                                            style="height: 300px; border: 2px dashed #ccc; border-radius: 12px; cursor: pointer; background: #f8f9fa;">
                                            <div class="upload-content text-center" id="uploadContent">
                                                <div class="mb-3">
                                                    <i class="bi bi-file-earmark-image"
                                                        style="font-size: 4rem; color: #9ca3af;"></i>
                                                </div>
                                                <p class="mb-1 text-muted">drag your company logo's image here</p>
                                                <small class="text-muted">(jpeg, png)</small>
                                            </div>
                                            <div id="logoPreview" style="display: none;">
                                                <img id="logoPreviewImg" src="" alt="Logo Preview"
                                                    style="max-width: 250px; max-height: 200px; border-radius: 8px;">
                                            </div>
                                            <input type="file" id="templateLogoUpload" name="company_logo"
                                                accept="image/*" style="display: none;">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-4">
                                        <label class="form-label fw-bold">Energy Management Standard <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select" name="energy_standard" style="background: #f8f9fa;"
                                            required>
                                            <option value="">Select standard...</option>
                                            <option value="ISO 50001:2018">ISO 50001:2018</option>
                                            <option value="ISO 50001:2011">ISO 50001:2011</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="text-end">
                                <button type="button" class="btn btn-primary px-4 py-2" onclick="nextTemplateStep()"
                                    style="border-radius: 8px;">View Draft</button>
                            </div>
                        </div>

                        <!-- Step 3: Preview - SUDAH BENAR -->
                        <div class="template-step" id="template-step-3" style="display: none;">
                            <div class="d-flex align-items-center mb-3">
                                <button type="button" class="btn btn-outline-secondary btn-sm me-3"
                                    onclick="prevTemplateStep()">
                                    <i class="bi bi-arrow-left"></i>
                                </button>
                                <h4 class="mb-0 flex-grow-1 text-center">Energy Policy of <span
                                        id="previewCompanyName">[Company Name]</span></h4>
                                <button type="button" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-pencil"></i>
                                </button>
                            </div>

                            <div class="preview-document" id="templatePreviewContent"
                                style="min-height: 500px; background: #f8f9fa; border-radius: 12px; padding: 1rem;">
                                <!-- Preview content will be generated here -->
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" id="templatePrevBtn" onclick="prevTemplateStep()"
                        style="display: none;">
                        Previous
                    </button>
                    <button type="button" class="btn btn-primary px-4 py-2" id="templateSaveBtn"
                        onclick="saveTemplatePolicy()" style="display: none; border-radius: 8px;">
                        Save
                    </button>
                </div>

            </div>
        </div>
    </div>

    <!-- Upload Policy Modal -->
    <div class="modal fade" id="uploadPolicyModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" style="border-radius: 15px;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark">Upload Energy Policy</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="uploadPolicyForm" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4">
                            <label class="form-label text-muted">Policy Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="title" placeholder="Enter policy title"
                                required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label text-muted">Upload Document <span
                                    class="text-danger">*</span></label>
                            <div class="upload-area text-center py-4"
                                style="border: 2px dashed #dee2e6; border-radius: 10px; background: #f8f9fa;">
                                <i class="bi bi-cloud-upload" style="font-size: 3rem; color: #6c757d;"></i>
                                <p class="text-muted mt-2 mb-2">Click to select file or drag and drop</p>
                                <p class="text-muted small">Supported formats: PDF, DOC, DOCX</p>
                                <input type="file" name="document" accept=".pdf,.doc,.docx" style="display: none;"
                                    id="uploadFile" required>
                                <button type="button" class="btn btn-outline-primary"
                                    onclick="document.getElementById('uploadFile').click()">Choose File</button>
                            </div>
                            <div id="uploadFileName" class="mt-2 text-muted small"></div>
                        </div>
                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-primary px-5 py-2"
                                style="border-radius: 8px; min-width: 120px;">
                                <span class="spinner-border spinner-border-sm d-none me-2" role="status"></span>
                                Upload Policy
                            </button>
                        </div>
                    </form>
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
                        style="border-radius: 10px;" onclick="location.reload()">OK</button>
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 Energy Policy page loaded');

            let csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
            let currentTemplateStep = 1;
            const totalTemplateSteps = 3;

            // Modal Functions
            function showSuccessModal(title, message) {
                document.getElementById('successTitle').textContent = title;
                document.getElementById('successMessage').textContent = message;
                const modal = new bootstrap.Modal(document.getElementById('successModal'));
                modal.show();
            }

            function showErrorModal(title, message) {
                document.getElementById('errorTitle').textContent = title;
                document.getElementById('errorMessage').textContent = message;
                const modal = new bootstrap.Modal(document.getElementById('errorModal'));
                modal.show();
            }

            // Close instructions
            window.closeInstructions = function() {
                document.getElementById('instructionsCard').style.display = 'none';
            };

            // Open Use Template Modal
            window.openUseTemplateModal = function() {
                console.log('Opening use template modal');
                currentTemplateStep = 1;
                showTemplateStep(1);
                const modal = new bootstrap.Modal(document.getElementById('useTemplateModal'));
                modal.show();
            };

            // Open Upload Modal
            window.openUploadPolicyModal = function() {
                const modal = new bootstrap.Modal(document.getElementById('uploadPolicyModal'));
                modal.show();
            };

            // Template navigation functions
            function showTemplateStep(step) {
                console.log('Showing template step:', step);

                document.querySelectorAll('.template-step').forEach(stepEl => {
                    stepEl.style.display = 'none';
                });

                const targetStep = document.getElementById(`template-step-${step}`);
                if (targetStep) {
                    targetStep.style.display = 'block';
                }

                currentTemplateStep = step;
                updateTemplateButtons();
            }

            window.nextTemplateStep = function() {
                console.log('Next clicked, current step:', currentTemplateStep);

                if (currentTemplateStep < totalTemplateSteps) {
                    if (currentTemplateStep === 2) {
                        setTimeout(() => {
                            generateTemplatePreview();
                        }, 100);
                    }
                    showTemplateStep(currentTemplateStep + 1);
                }
            };

            window.prevTemplateStep = function() {
                if (currentTemplateStep > 1) {
                    showTemplateStep(currentTemplateStep - 1);
                }
            };

            function updateTemplateButtons() {
                const prevBtn = document.getElementById('templatePrevBtn');
                const saveBtn = document.getElementById('templateSaveBtn');

                if (prevBtn) prevBtn.style.display = currentTemplateStep === 1 ? 'none' : 'inline-block';
                if (saveBtn) saveBtn.style.display = currentTemplateStep === totalTemplateSteps ? 'inline-block' : 'none';
            }

            function generateTemplatePreview() {
                console.log('Generating template preview...');

                const selectedCommitments = [];
                const checkboxes = document.querySelectorAll('input[name="commitments[]"]:checked');
                checkboxes.forEach(checkbox => {
                    selectedCommitments.push(checkbox.value);
                });

                const customStatementEl = document.getElementById('customPolicyStatement');
                const customStatement = customStatementEl ? customStatementEl.value : '';

                const formData = new FormData(document.getElementById('useTemplateForm'));
                const companyName = formData.get('company_name') || '[Company Name]';
                const energyStandard = formData.get('energy_standard') || 'ISO 50001:2018';

                const previewCompanyNameEl = document.getElementById('previewCompanyName');
                if (previewCompanyNameEl) {
                    previewCompanyNameEl.textContent = companyName;
                }

                const logoFile = document.getElementById('templateLogoUpload').files[0];
                let logoHtml = '';

                if (logoFile) {
                    const logoUrl = URL.createObjectURL(logoFile);
                    logoHtml = `<div style="text-align: center; margin-bottom: 2rem;">
                        <img src="${logoUrl}" alt="Company Logo" style="max-width: 200px; max-height: 100px;">
                    </div>`;
                } else {
                    logoHtml = `<div style="text-align: center; margin-bottom: 2rem;">
                        <div style="width: 200px; height: 100px; border: 2px dashed #ddd; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px;">
                            <span style="color: #999;">Company Logo</span>
                        </div>
                    </div>`;
                }

                let previewHtml = `
                    <div style="background: white; padding: 3rem; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
                        ${logoHtml}

                        <div style="line-height: 1.6; color: #333;">
                            <p>At ${companyName}, we recognize the importance of sustainable energy management and are committed to improving our energy performance as an integral part of our operations and decision-making processes. To support this, we have established an Energy Management System (EnMS) in accordance with ${energyStandard}.</p>

                            <p style="margin-top: 2rem;"><strong>As part of our commitment to energy management, ${companyName} will:</strong></p>

                            <ul style="margin-left: 1rem; margin-top: 1rem;">`;

                selectedCommitments.forEach(commitment => {
                    previewHtml += `<li style="margin-bottom: 0.5rem;">${commitment}</li>`;
                });

                if (customStatement) {
                    previewHtml += `<li style="margin-bottom: 0.5rem;">${customStatement}</li>`;
                }

                previewHtml += `
                            </ul>

                            <div style="margin-top: 4rem; text-align: center;">
                                <p>Approved by:</p>
                                <div style="border-bottom: 2px solid #000; width: 250px; margin: 40px auto 10px;"></div>
                            </div>
                        </div>
                    </div>
                `;

                const previewContainer = document.getElementById('templatePreviewContent');
                if (previewContainer) {
                    previewContainer.innerHTML = previewHtml;
                    console.log('Preview generated successfully');
                }
            }

            window.saveTemplatePolicy = function() {
                console.log('Saving template policy...');

                const submitBtn = document.getElementById('templateSaveBtn');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';
                }

                // Simulate successful save
                setTimeout(() => {
                    bootstrap.Modal.getInstance(document.getElementById('useTemplateModal')).hide();
                    showSuccessModal('Success!', 'Energy policy created successfully!');
                }, 1000);
            };

            // Logo upload handler
            const templateLogoUpload = document.getElementById('templateLogoUpload');
            if (templateLogoUpload) {
                templateLogoUpload.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        console.log('Logo uploaded:', file.name);

                        const uploadContent = document.getElementById('uploadContent');
                        const logoPreview = document.getElementById('logoPreview');
                        const logoPreviewImg = document.getElementById('logoPreviewImg');

                        if (uploadContent && logoPreview && logoPreviewImg) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                logoPreviewImg.src = e.target.result;
                                uploadContent.style.display = 'none';
                                logoPreview.style.display = 'block';
                            };
                            reader.readAsDataURL(file);
                        }
                    }
                });
            }

            // Upload form handler
            const uploadPolicyForm = document.getElementById('uploadPolicyForm');
            if (uploadPolicyForm) {
                uploadPolicyForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const submitBtn = this.querySelector('button[type="submit"]');
                    submitBtn.disabled = true;

                    showSuccessModal('Success!', 'Policy uploaded successfully!');
                });
            }

            // File upload handler
            const uploadFile = document.getElementById('uploadFile');
            if (uploadFile) {
                uploadFile.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const uploadFileName = document.getElementById('uploadFileName');
                        if (uploadFileName) {
                            uploadFileName.textContent = `Selected: ${file.name}`;
                        }
                    }
                });
            }

            // Global functions - FIXED
            window.viewPolicy = function(id) {
                window.open(`/energy-policy/${id}`, '_blank');
            };

            // TAMBAH FUNCTION INI - YANG HILANG!
            window.editPolicy = function(id) {
                console.log('Edit policy:', id);

                fetch(`/energy-policy/${id}/edit`, {
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
                    console.log('Edit data received:', data);
                    document.getElementById('editPolicyId').value = data.id;
                    document.getElementById('editTitle').value = data.title || '';
                    document.getElementById('editCompanyName').value = data.company_name || '';
                    document.getElementById('editPolicyStatement').value = data.policy_statement || '';
                    document.getElementById('editEnergyStandard').value = data.energy_standard || '';
                    document.getElementById('editStatus').value = data.status || 'draft';

                    const modal = new bootstrap.Modal(document.getElementById('editPolicyModal'));
                    modal.show();
                })
                .catch(error => {
                    console.error('Edit load error:', error);
                    showErrorModal('Load Error!', 'Unable to load policy data');
                });
            };

            window.deletePolicy = function(id) {
                console.log('Delete policy:', id);

                const modal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
                modal.show();

                document.getElementById('confirmDeleteBtn').onclick = function() {
                    modal.hide();

                    fetch(`/energy-policy/${id}`, {
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
                        console.log('Delete response:', data);
                        if (data.success) {
                            showSuccessModal('Deleted!', 'Energy policy deleted successfully!');
                        } else {
                            showErrorModal('Delete Error!', data.message || 'Unable to delete policy');
                        }
                    })
                    .catch(error => {
                        console.error('Delete error:', error);
                        showErrorModal('Network Error!', 'Unable to delete policy');
                    });
                };
            };

            // Edit form handler
            const editPolicyForm = document.getElementById('editPolicyForm');
            if (editPolicyForm) {
                editPolicyForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    console.log('Edit form submitted');

                    const id = document.getElementById('editPolicyId').value;
                    const submitBtn = this.querySelector('button[type="submit"]');
                    const spinner = submitBtn.querySelector('.spinner-border');
                    const alertDiv = document.getElementById('editPolicyAlert');
                    const errorsList = document.getElementById('editPolicyErrors');

                    alertDiv.classList.add('d-none');
                    submitBtn.disabled = true;
                    spinner.classList.remove('d-none');

                    const formData = new FormData(this);

                    fetch(`/energy-policy/${id}`, {
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
                        console.log('Edit response:', data);
                        if (data.success) {
                            bootstrap.Modal.getInstance(document.getElementById('editPolicyModal')).hide();
                            showSuccessModal('Updated!', 'Energy policy updated successfully!');
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
                                showErrorModal('Update Error!', data.message || 'Unable to update policy');
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Edit error:', error);
                        showErrorModal('Network Error!', 'Unable to update policy');
                    })
                    .finally(() => {
                        submitBtn.disabled = false;
                        spinner.classList.add('d-none');
                    });
                });
            }
        });
        </script>


    <style>
        .template-step {
            min-height: 400px;
        }

        .upload-area {
            transition: all 0.3s ease;
        }

        .upload-area:hover {
            border-color: #4285f4 !important;
            background-color: rgba(66, 133, 244, 0.05) !important;
        }

        .preview-document {
            min-height: 600px;
            background: #f8f9fa;
            border-radius: 12px;
            padding: 1rem;
            overflow-y: auto;
            max-height: 70vh;
        }

        .table th {
            font-weight: 600;
            font-size: 0.9rem;
        }

        .table td {
            font-size: 0.85rem;
            line-height: 1.4;
        }

        .form-check-input {
            width: 1.2em;
            height: 1.2em;
        }

        .form-control,
        .form-select {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 0.75rem;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #4285f4;
            box-shadow: 0 0 0 0.2rem rgba(66, 133, 244, 0.25);
        }

        .btn-primary {
            background-color: #4285f4;
            border-color: #4285f4;
        }

        .btn-primary:hover {
            background-color: #3367d6;
            border-color: #3367d6;
        }

        /* Fix warna biru table headers */
        .table thead th {
            background: #4285f4 !important;
            color: white !important;
            border-color: #4285f4 !important;
        }

        .table-bordered> :not(caption)>*>* {
            border-width: 1px;
            border-color: #dee2e6;
        }

        /* Make sure table headers stay blue */
        .table thead tr th {
            background-color: #4285f4 !important;
            color: white !important;
            font-weight: 600;
            font-size: 0.9rem;
            padding: 1rem;
        }

        /* Table body styling */
        .table tbody td {
            padding: 0.75rem;
            vertical-align: middle;
        }

        /* Checkbox in table */
        .table tbody td input[type="checkbox"] {
            width: 1.2em;
            height: 1.2em;
            cursor: pointer;
        }
    </style>
@endsection
