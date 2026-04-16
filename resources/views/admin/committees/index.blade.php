@extends('layouts.dashboard')

@section('title', 'Energy Management Committee')


<meta name="csrf-token" content="{{ csrf_token() }}">

@section('page-title', 'Energy Management Committee')
@section('page-title-main', 'Energy Management Committee')

@section('content')


@include('partials._header-dashboard')



<!-- Instructions Section -->
<div class="card border-0 shadow-sm mb-4" id="instructionsCard" style="background: #e3f2fd; border-left: 4px solid #2196f3;">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-start">
            <div class="d-flex align-items-start">
                <div class="me-3">
                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                        <i class="bi bi-info-lg text-white"></i>
                    </div>
                </div>
                <div>
                    <h6 class="fw-bold text-primary mb-2">Instructions</h6>
                    <ul class="mb-0 text-dark small lh-base">
                        <li>The purpose is to identify and document internal and external stakeholders relevant to the Energy Management System, their roles, expectations, and influence in accordance with Clause 4.2 – Understanding the Needs and Expectations of Interested Parties.</li>
                        <li class="mt-2">Stakeholders are person or organization (3.1.1) that can affect, be affected by, or perceive itself to be affected by a</li>
                    </ul>
                </div>
            </div>
            <button class="btn-close" onclick="closeInstructions()"></button>
        </div>
    </div>
</div>

<!-- Action Buttons -->
<div class="d-flex justify-content-end align-items-center mb-4">
    <div class="d-flex gap-2">
        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#orgChartModal">
            Organizational Chart
        </button>
        @if (!auth()->user()->hasPermission('committees.add')) 
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCommitteeModal">Add Committee</button>
        @endif
    </div>
</div>

<div class="modal fade" id="orgChartModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 860px;">
        <div class="modal-content" style="border-radius: 16px; border: none; overflow: hidden;">
 
            <!-- Header -->
            <div class="modal-header border-0 px-4 pt-4 pb-0">
                <h5 class="modal-title fw-bold text-dark" style="font-size: 1.2rem;">Organizational Chart</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
 
            <!-- Body -->
            <div class="modal-body px-4 pt-3 pb-2">
                <div style="border: 1px solid #e0e0e0; border-radius: 12px; background: #fff; padding: 24px; overflow-x: auto;">
 
                    <!-- SVG Org Chart -->
                    <svg id="orgChartSVG" viewBox="0 0 780 380" xmlns="http://www.w3.org/2000/svg"
                         style="width: 100%; height: auto; min-width: 700px; display: block;">
 
                        <!-- ── CONNECTOR LINES ── -->
                        <!-- Chairperson → down to branch point -->
                        <line x1="390" y1="82"  x2="390" y2="140" stroke="#aaa" stroke-width="1.5"/>
 
                        <!-- Horizontal: center → Energy Manager -->
                        <line x1="390" y1="140" x2="640" y2="140" stroke="#aaa" stroke-width="1.5"/>
 
                        <!-- Energy Manager: horizontal stub up -->
                        <line x1="640" y1="140" x2="640" y2="158" stroke="#aaa" stroke-width="1.5"/>
 
                        <!-- Center → down to dept branch -->
                        <line x1="390" y1="140" x2="390" y2="200" stroke="#aaa" stroke-width="1.5"/>
 
                        <!-- Horizontal bar across all 6 depts -->
                        <!-- leftmost dept center x=65, rightmost=715, each dept width ~110, gap ~10 -->
                        <!-- dept centers: 65, 195, 325, 455, 585, 715 -->
                        <line x1="65"  y1="200" x2="715" y2="200" stroke="#aaa" stroke-width="1.5"/>
 
                        <!-- Drop lines to each dept box -->
                        <line x1="65"  y1="200" x2="65"  y2="228" stroke="#aaa" stroke-width="1.5"/>
                        <line x1="195" y1="200" x2="195" y2="228" stroke="#aaa" stroke-width="1.5"/>
                        <line x1="325" y1="200" x2="325" y2="228" stroke="#aaa" stroke-width="1.5"/>
                        <line x1="455" y1="200" x2="455" y2="228" stroke="#aaa" stroke-width="1.5"/>
                        <line x1="585" y1="200" x2="585" y2="228" stroke="#aaa" stroke-width="1.5"/>
                        <line x1="715" y1="200" x2="715" y2="228" stroke="#aaa" stroke-width="1.5"/>
 
                        <!-- ── CHAIRPERSON BOX ── -->
                        <rect x="310" y="10" width="160" height="72" rx="8" ry="8"
                              fill="white" stroke="#1a3399" stroke-width="1.5"/>
                        <text x="390" y="32" text-anchor="middle" font-family="sans-serif"
                              font-size="12" font-weight="700" fill="#1a3399">Chairperson</text>
                        <text x="390" y="50" text-anchor="middle" font-family="sans-serif"
                              font-size="10" fill="#333">Muhammad Faiz Bin Zakaria</text>
                        <text x="390" y="66" text-anchor="middle" font-family="sans-serif"
                              font-size="10" fill="#333">Terminal Manager</text>
 
                        <!-- ── ENERGY MANAGER BOX ── -->
                        <rect x="562" y="158" width="156" height="74" rx="8" ry="8"
                              fill="white" stroke="#888" stroke-width="1.5"/>
                        <text x="640" y="179" text-anchor="middle" font-family="sans-serif"
                              font-size="12" font-weight="700" fill="#1a3399">Energy Manager</text>
                        <text x="640" y="197" text-anchor="middle" font-family="sans-serif"
                              font-size="10" fill="#333">Muhammad Ali Bin Abu</text>
                        <text x="640" y="213" text-anchor="middle" font-family="sans-serif"
                              font-size="10" fill="#333">Chargemen</text>
 
                        <!-- ── DEPT BOX helper: x = center - 55, y = 228, w=110, h=110 ── -->
 
                        <!-- 1. Environmental Health Safety & Quality (center=65) -->
                        <rect x="8" y="228" width="115" height="118" rx="8" ry="8"
                              fill="white" stroke="#888" stroke-width="1.5"/>
                        <text x="65" y="252" text-anchor="middle" font-family="sans-serif"
                              font-size="9.5" font-weight="700" fill="#1a3399">Environmental,</text>
                        <text x="65" y="265" text-anchor="middle" font-family="sans-serif"
                              font-size="9.5" font-weight="700" fill="#1a3399">Health, Safety</text>
                        <text x="65" y="278" text-anchor="middle" font-family="sans-serif"
                              font-size="9.5" font-weight="700" fill="#1a3399">&amp; Quality</text>
                        <text x="65" y="298" text-anchor="middle" font-family="sans-serif"
                              font-size="9" fill="#555">John Doe</text>
                        <text x="65" y="312" text-anchor="middle" font-family="sans-serif"
                              font-size="9" fill="#555">QHSE Implementor</text>
 
                        <!-- 2. Facility Utility & Operations Management (center=195) -->
                        <rect x="138" y="228" width="115" height="118" rx="8" ry="8"
                              fill="white" stroke="#888" stroke-width="1.5"/>
                        <text x="195" y="252" text-anchor="middle" font-family="sans-serif"
                              font-size="9.5" font-weight="700" fill="#1a3399">Facility, Utility &amp;</text>
                        <text x="195" y="265" text-anchor="middle" font-family="sans-serif"
                              font-size="9.5" font-weight="700" fill="#1a3399">Operations</text>
                        <text x="195" y="278" text-anchor="middle" font-family="sans-serif"
                              font-size="9.5" font-weight="700" fill="#1a3399">Management</text>
                        <text x="195" y="298" text-anchor="middle" font-family="sans-serif"
                              font-size="9" fill="#555">John Doe</text>
                        <text x="195" y="312" text-anchor="middle" font-family="sans-serif"
                              font-size="9" fill="#555">Operations Supervisor</text>
 
                        <!-- 3. Engineering & Maintenance (center=325) -->
                        <rect x="268" y="228" width="115" height="118" rx="8" ry="8"
                              fill="white" stroke="#888" stroke-width="1.5"/>
                        <text x="325" y="252" text-anchor="middle" font-family="sans-serif"
                              font-size="9.5" font-weight="700" fill="#1a3399">Engineering &amp;</text>
                        <text x="325" y="265" text-anchor="middle" font-family="sans-serif"
                              font-size="9.5" font-weight="700" fill="#1a3399">Maintenance</text>
                        <text x="325" y="290" text-anchor="middle" font-family="sans-serif"
                              font-size="9" fill="#555">John Doe</text>
                        <text x="325" y="304" text-anchor="middle" font-family="sans-serif"
                              font-size="9" fill="#555">Mechanical</text>
                        <text x="325" y="317" text-anchor="middle" font-family="sans-serif"
                              font-size="9" fill="#555">Technician/Chargeman</text>
 
                        <!-- 4. Production Industry (center=455) -->
                        <rect x="398" y="228" width="115" height="118" rx="8" ry="8"
                              fill="white" stroke="#888" stroke-width="1.5"/>
                        <text x="455" y="259" text-anchor="middle" font-family="sans-serif"
                              font-size="9.5" font-weight="700" fill="#1a3399">Production</text>
                        <text x="455" y="272" text-anchor="middle" font-family="sans-serif"
                              font-size="9.5" font-weight="700" fill="#1a3399">(Industry)</text>
                        <text x="455" y="295" text-anchor="middle" font-family="sans-serif"
                              font-size="9" fill="#555">John Doe</text>
                        <text x="455" y="309" text-anchor="middle" font-family="sans-serif"
                              font-size="9" fill="#555">Logistics Supervisor</text>
 
                        <!-- 5. Procurement and Finance (center=585) -->
                        <rect x="528" y="228" width="115" height="118" rx="8" ry="8"
                              fill="white" stroke="#888" stroke-width="1.5"/>
                        <text x="585" y="259" text-anchor="middle" font-family="sans-serif"
                              font-size="9.5" font-weight="700" fill="#1a3399">Procurement</text>
                        <text x="585" y="272" text-anchor="middle" font-family="sans-serif"
                              font-size="9.5" font-weight="700" fill="#1a3399">and Finance</text>
                        <text x="585" y="295" text-anchor="middle" font-family="sans-serif"
                              font-size="9" fill="#555">John Doe</text>
                        <text x="585" y="309" text-anchor="middle" font-family="sans-serif"
                              font-size="9" fill="#555">Finance Lead</text>
 
                        <!-- 6. Human Resource & Administration (center=715) -->
                        <rect x="658" y="228" width="115" height="118" rx="8" ry="8"
                              fill="white" stroke="#888" stroke-width="1.5"/>
                        <text x="715" y="252" text-anchor="middle" font-family="sans-serif"
                              font-size="9.5" font-weight="700" fill="#1a3399">Human Resource</text>
                        <text x="715" y="265" text-anchor="middle" font-family="sans-serif"
                              font-size="9.5" font-weight="700" fill="#1a3399">&amp; Administration</text>
                        <text x="715" y="290" text-anchor="middle" font-family="sans-serif"
                              font-size="9" fill="#555">John Doe</text>
                        <text x="715" y="304" text-anchor="middle" font-family="sans-serif"
                              font-size="9" fill="#555">HR Advisor</text>
 
                    </svg>
                    <!-- End SVG -->
 
                </div>
            </div>
 
            <!-- Footer -->
            <div class="modal-footer border-0 px-4 pb-4 pt-2 d-flex gap-3">
                <button type="button"
                        class="btn btn-primary flex-fill py-2 fw-semibold"
                        style="border-radius: 10px; background:#3965FF; border-color:#3965FF;"
                        data-bs-dismiss="modal">
                    Done
                </button>
                <button type="button"
                        class="btn btn-primary flex-fill py-2 fw-semibold"
                        style="border-radius: 10px; background:#3965FF; border-color:#3965FF;"
                        onclick="exportOrgChartPDF()">
                    Export as PDF
                </button>
            </div>
 
        </div>
    </div>
</div>
<!-- Energy Management Committee Section -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white p-4">
        <h5 class="fw-bold mb-0">Energy Management Committee</h5>
    </div>
    <div class="card-body p-4">

        @forelse($committees ?? [] as $committee)
        <!-- Chairperson Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between align-items-center text-white" style="background: #3965FF;">
                <h5 class="mb-0 fw-bold">{{ $committee->role ?? 'Chairperson' }}</h5>
                <div class="d-flex gap-2">
                    @if (!auth()->user()->hasPermission('committees.edit')) 
                    <button class="btn btn-light btn-sm" onclick="editCommittee({{ $committee->id ?? 1 }})" title="Edit">
                        <i class="bi bi-pencil-fill"></i>
                    </button>
                    @endif
                    <button class="btn btn-light btn-sm" onclick="viewCommittee({{ $committee->id ?? 1 }})" title="View">
                        <i class="bi bi-file-text-fill"></i>
                    </button>
                    @if (!auth()->user()->hasPermission('committees.delete')) 
                    <button class="btn btn-light btn-sm" onclick="deleteCommittee({{ $committee->id ?? 1 }})" title="Delete">
                        <i class="bi bi-trash-fill"></i>
                    </button>
                    @endif
                    <button class="btn btn-warning btn-sm" title="Appointment Letter">
                        Appointment Letter
                    </button>
                </div>
            </div>
            <div class="card-body p-4">
                <h6 class="fw-bold text-primary mb-3">{{ $committee->department ?? 'Energy Manager' }}</h6>

                <div class="row mb-4">
                    <div class="col-md-3">
                        <small class="text-muted fw-semibold">Name :</small>
                        <div class="fw-medium">{{ $committee->name ?? 'John Doe' }}</div>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted fw-semibold">Position :</small>
                        <div class="fw-medium">{{ $committee->position ?? 'Manager' }}</div>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted fw-semibold">Start Date :</small>
                        <div class="fw-medium">{{ $committee->start_date ?? '17 Jun 2020' }}</div>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted fw-semibold">End Date :</small>
                        <div class="fw-medium">{{ $committee->end_date ?? '17 Jun 2025' }}</div>
                    </div>
                </div>

                <div class="responsibilities">
                    @php
                        $responsibilities = $committee->responsibilities ?? [
                            'Lead the Energy Management Team and ensure top management commitment.',
                            'Serve as the link between top management and the energy team.',
                            'Approve energy policy, objectives, and targets.',
                            'Ensure that the EnMS is integrated into the organization\'s overall management system.',
                            'Allocate resources needed for effective energy management.',
                            'Review energy performance and drive continuous improvement.',
                            'Facilitate management review meetings.'
                        ];
                        if (is_string($responsibilities)) {
                            $responsibilities = explode("\n", $responsibilities);
                        }
                    @endphp

                    @foreach($responsibilities as $responsibility)
                        <div class="mb-1 small">- {{ trim($responsibility) }}</div>
                    @endforeach
                </div>

                <div class="text-end mt-3">
                    <small class="text-muted fst-italic">Last updated {{ $committee->updated_at?->diffForHumans() ?? '6 days ago' }}</small>
                </div>
            </div>
        </div>
        @empty
        <!-- Default Chairperson Card -->

        <!-- Secretary Card -->
        
        @endforelse

    </div>
</div>

<!-- Add Committee Modal -->
<div class="modal fade" id="addCommitteeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header border-0 pb-2">
                <h5 class="modal-title fw-bold text-dark">Add Energy Management Committee</h5>
                <div class="d-flex gap-2">
                    <button class="btn btn-primary btn-sm">Select user</button>
                    <button class="btn btn-outline-secondary btn-sm">Clear</button>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body p-4">
                <div id="addCommitteeAlert" class="alert alert-danger d-none" role="alert">
                    <ul class="mb-0" id="addCommitteeErrors"></ul>
                </div>
                <form id="addCommitteeForm">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-primary">Name <span class="text-danger">*</span></label>
                            <select class="form-select" name="name" required>
                                <option value="">Choose...</option>
                                <option value="John Doe">John Doe</option>
                                <option value="Jane Smith">Jane Smith</option>
                                <option value="Michael Johnson">Michael Johnson</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-primary">Position <span class="text-danger">*</span></label>
                            <select class="form-select" name="position" required>
                                <option value="">Choose...</option>
                                <option value="Manager">Manager</option>
                                <option value="Assistant Manager">Assistant Manager</option>
                                <option value="Engineer">Engineer</option>
                                <option value="Supervisor">Supervisor</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-primary">Appointment Period <span class="text-danger">*</span></label>
                            <div class="row">
                                <div class="col-6">
                                    <input type="date" class="form-control" name="start_date" placeholder="Start Date" required>
                                </div>
                                <div class="col-6">
                                    <input type="date" class="form-control" name="end_date" placeholder="End Date" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-primary">Role <span class="text-danger">*</span></label>
                            <select class="form-select" name="role" required>
                                <option value="">Choose...</option>
                                <option value="Chairperson">Chairperson</option>
                                <option value="Secretary">Secretary</option>
                                <option value="Member">Member</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-primary">Department <span class="text-danger">*</span></label>
                            <select class="form-select" name="department" required>
                                <option value="">Choose...</option>
                                <option value="Energy Manager">Energy Manager</option>
                                <option value="Environmental, Health, Safety & Quality">Environmental, Health, Safety & Quality</option>
                                <option value="Facility, Utility & Operations Management">Facility, Utility & Operations Management</option>
                                <option value="Engineering & Maintenance">Engineering & Maintenance</option>
                                <option value="Production">Production</option>
                                <option value="Finance & Procurement">Finance & Procurement</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-primary">Communication Method <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="communication_method" placeholder="Enter communication method" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-primary">Responsibilities <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="responsibilities" rows="5" placeholder="Enter responsibilities (one per line)" required></textarea>
                    </div>
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary px-5 py-2" id="addCommitteeBtn" style="border-radius: 10px; min-width: 120px;">
                            <span class="spinner-border spinner-border-sm d-none me-2" role="status"></span>
                            Add Now
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
                <button type="button" class="btn btn-primary px-4" data-bs-dismiss="modal" style="border-radius: 10px;">OK</button>
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
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal" style="border-radius: 10px;">Close</button>
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
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal" style="border-radius: 10px;">Cancel</button>
                    <button type="button" class="btn btn-danger px-4" id="confirmButton" style="border-radius: 10px;">Delete</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('🚀 Energy Management Committee page loaded');

        // Get CSRF token
        let csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                        document.querySelector('input[name="_token"]')?.value ||
                        '{{ csrf_token() }}';

        // Modal Functions
        function showSuccessModal(title, message, callback = null) {
            document.getElementById('successTitle').textContent = title;
            document.getElementById('successMessage').textContent = message;
            const modal = new bootstrap.Modal(document.getElementById('successModal'));
            modal.show();

            if (callback) {
                document.getElementById('successModal').addEventListener('hidden.bs.modal', function() {
                    callback();
                }, { once: true });
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

        // Add committee form
        const addForm = document.getElementById('addCommitteeForm');
        if (addForm) {
            addForm.addEventListener('submit', function(e) {
                e.preventDefault();
                console.log('📝 Add form submitted');

                const btn = document.getElementById('addCommitteeBtn');
                const spinner = btn.querySelector('.spinner-border');
                const alertDiv = document.getElementById('addCommitteeAlert');
                const errorsList = document.getElementById('addCommitteeErrors');

                alertDiv.classList.add('d-none');
                btn.disabled = true;
                spinner.classList.remove('d-none');

                const formData = new FormData(this);

                fetch('{{ route("committees.store") }}', {
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
                        const addModal = bootstrap.Modal.getInstance(document.getElementById('addCommitteeModal'));
                        addModal.hide();

                        showSuccessModal(
                            'Committee Member Added!',
                            'The committee member has been successfully added.',
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
                            showErrorModal('Validation Error', data.message || 'Please check your input and try again.');
                        }
                    }
                })
                .catch(error => {
                    console.error('❌ Fetch error:', error);
                    showErrorModal('Network Error', 'Unable to connect to server. Please check your connection and try again.');
                })
                .finally(() => {
                    btn.disabled = false;
                    spinner.classList.add('d-none');
                });
            });
        }

        // Global functions
        window.editCommittee = function(id) {
            console.log('✏️ Edit committee:', id);
            // Implementation for edit functionality
        };

        window.viewCommittee = function(id) {
            console.log('👁️ View committee:', id);
            // Implementation for view functionality
        };

        window.deleteCommittee = function(id) {
            console.log('🗑️ Delete committee:', id);

            showConfirmModal(
                'Delete Committee Member',
                'Are you sure you want to delete this committee member? This action cannot be undone.',
                function() {
                    // Implementation for delete functionality
                    fetch(`{{ url('/committees') }}/${id}`, {
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
                                'Committee Member Deleted!',
                                'The committee member has been successfully deleted.',
                                () => location.reload()
                            );
                        } else {
                            showErrorModal('Delete Error', data.message || 'Unable to delete committee member.');
                        }
                    })
                    .catch(error => {
                        console.error('❌ Delete error:', error);
                        showErrorModal('Network Error', 'Unable to delete committee member. Please try again.');
                    });
                }
            );
        };
    });
    function exportOrgChartPDF() {
    const originalTitle = document.title;
    document.title = 'Organizational_Chart';
 
    const container = document.getElementById('orgChartContainer');
    const printWindow = window.open('', '_blank');
 
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Organizational Chart</title>
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
            <style>
                body { font-family: sans-serif; padding: 20px; }
                .org-node { page-break-inside: avoid; }
            </style>
        </head>
        <body>
            <h4 class="fw-bold mb-4" style="color:#1a3399">Organizational Chart</h4>
            ${container.outerHTML}
        </body>
        </html>
    `);
 
    printWindow.document.close();
    printWindow.focus();
 
    setTimeout(() => {
        printWindow.print();
        printWindow.close();
        document.title = originalTitle;
    }, 500);
}
</script>

@endsection
