@extends('layouts.dashboard')

@section('title', 'Legal Detail - ' . $legal->title)

@section('content')
<!-- CSRF Token Meta Tag -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Header Section -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <p class="text-secondary small mb-1">Pages / Energy Data Entry</p>
        <h3 class="fw-bold">Legislation & Regulation</h3>
    </div>
    <div class="d-flex align-items-center">
        <div class="input-group search-box me-3">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="text" class="form-control" id="searchInput" placeholder="Search">
        </div>
        <img src="{{ asset('images/user.png') }}" class="rounded-circle" alt="User" style="width: 40px; height: 40px;">
    </div>
</div>

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
                    <p class="mb-0 text-dark small lh-base">
                        This section shows detailed breakdown of the legislation and regulation components
                        with expandable sections for better organization.
                    </p>
                </div>
            </div>
            <button class="btn-close" onclick="closeInstructions()"></button>
        </div>
    </div>
</div>

<!-- Action Buttons -->
<div class="d-flex justify-content-end mb-4">
    <div class="d-flex gap-2">
        <a href="{{ route('legals.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to List
        </a>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLegalModal">Add Legal</button>
    </div>
</div>

<!-- Main Legal Document Card -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white p-4">
        <h5 class="fw-bold mb-0">Legislation & Regulation</h5>
    </div>
    <div class="card-body p-4">

        <!-- Legal Document Header -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header text-white text-center py-3 bg-primary">
                <h5 class="mb-1 fw-bold">{{ $legal->title }}</h5>
                <h6 class="mb-0">{{ $legal->authority }}</h6>
            </div>
        </div>

        <!-- Legal Items Accordion -->
        <div id="legalItemsContainer">
            <!-- Loading state -->
            <div id="loadingState" class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 text-muted">Loading legal items...</p>
            </div>
        </div>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('🚀 Legal Detail page loaded');

        // Get CSRF token
        let csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

        // Close instructions
        window.closeInstructions = function() {
            document.getElementById('instructionsCard').style.display = 'none';
        };

        // Load legal items
        loadLegalItems();

        function loadLegalItems() {
            fetch('{{ route("legals.items", $legal->id) }}', {
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
                console.log('📄 Legal items loaded:', data);
                if (data.success) {
                    renderLegalItems(data.data);
                } else {
                    showError('Failed to load legal items');
                }
            })
            .catch(error => {
                console.error('❌ Error loading items:', error);
                showError('Error loading legal items');
            });
        }

        function renderLegalItems(items) {
            const container = document.getElementById('legalItemsContainer');

            if (items.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-5">
                        <i class="bi bi-inbox" style="font-size: 3rem; color: #6c757d;"></i>
                        <h5 class="mt-2 text-muted">No legal items found</h5>
                        <p class="text-muted">No items are associated with this legal document.</p>
                    </div>
                `;
                return;
            }

            let html = '<div class="accordion" id="legalItemsAccordion">';

            items.forEach((item, index) => {
                const isFirst = index === 0;
                const collapseId = `collapse-${item.id}`;
                const headingId = `heading-${item.id}`;

                html += `
                    <div class="accordion-item border-0 shadow-sm mb-3">
                        <h2 class="accordion-header" id="${headingId}">
                            <div class="d-flex justify-content-between align-items-center bg-light p-3">
                                <div class="d-flex align-items-center">
                                    <button class="btn btn-link text-decoration-none text-dark fw-semibold p-0 me-3"
                                            type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#${collapseId}"
                                            aria-expanded="${isFirst ? 'true' : 'false'}"
                                            aria-controls="${collapseId}">
                                        <i class="bi bi-chevron-down transition-transform"></i>
                                    </button>
                                    <span class="fw-bold">${item.item_id}</span>
                                </div>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-primary" onclick="editItem(${item.id})" title="Edit">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-primary" onclick="viewItem(${item.id})" title="View">
                                        <i class="bi bi-file-text-fill"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteItem(${item.id})" title="Delete">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                    <button class="btn btn-sm btn-secondary" title="More Options">
                                        <i class="bi bi-chevron-left"></i>
                                    </button>
                                </div>
                            </div>
                        </h2>
                        <div id="${collapseId}"
                             class="accordion-collapse collapse ${isFirst ? 'show' : ''}"
                             aria-labelledby="${headingId}"
                             data-bs-parent="#legalItemsAccordion">
                            <div class="accordion-body p-4">
                                ${renderItemContent(item)}
                            </div>
                        </div>
                    </div>
                `;
            });

            html += '</div>';
            container.innerHTML = html;

            // Add rotation animation for chevron icons
            addChevronAnimation();
        }

        function renderItemContent(item) {
            return `
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <td class="fw-semibold bg-light" style="width: 25%;">Relevant Clause / Section</td>
                                <td style="width: 25%;">4</td>
                                <td class="fw-semibold bg-light" style="width: 25%;">Reference to Others (Pre-requisite)</td>
                                <td style="width: 25%;">${item.item_id}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold bg-light">Category</td>
                                <td>Legal</td>
                                <td class="fw-semibold bg-light">Effective Date</td>
                                <td>2025-01-01</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold bg-light">Relevant (Y/N)</td>
                                <td colspan="3">Y</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold bg-light">Description of requirements</td>
                                <td colspan="3">${item.description || 'Energy management compliance requirements'}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold bg-light">What is affected by this requirement?</td>
                                <td colspan="3">Company wide operations</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold bg-light">What action is required</td>
                                <td colspan="3">Implement and monitor energy management practices</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold bg-light">Responsible Person</td>
                                <td colspan="3">Energy Manager</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold bg-light">Last Review Date</td>
                                <td colspan="3">2024-12-01</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold bg-light">How often will this be reviewed</td>
                                <td colspan="3">Annually</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold bg-light">Does it require further action?</td>
                                <td colspan="3">Yes - Continuous monitoring required</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold bg-light">Compliance Status</td>
                                <td colspan="3">In Progress</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold bg-light">Evidence of Compliance</td>
                                <td colspan="3">Monitoring reports and audit documentation</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold bg-light">Remarks / Notes</td>
                                <td colspan="3">Regular compliance monitoring and reporting required</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold bg-light">Last Updated</td>
                                <td colspan="3">2 days ago</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            `;
        }

        function addChevronAnimation() {
            // Add rotation animation for chevron icons
            document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(button => {
                button.addEventListener('click', function() {
                    const icon = this.querySelector('.bi-chevron-down');
                    if (icon) {
                        icon.style.transform = icon.style.transform === 'rotate(180deg)' ? 'rotate(0deg)' : 'rotate(180deg)';
                        icon.style.transition = 'transform 0.3s ease';
                    }
                });
            });
        }

        function showError(message) {
            const container = document.getElementById('legalItemsContainer');
            container.innerHTML = `
                <div class="text-center py-5">
                    <i class="bi bi-exclamation-triangle text-danger" style="font-size: 3rem;"></i>
                    <h5 class="mt-2 text-danger">Error</h5>
                    <p class="text-muted">${message}</p>
                    <button class="btn btn-primary" onclick="location.reload()">Retry</button>
                </div>
            `;
        }

        // Global functions for item actions
        window.editItem = function(id) {
            console.log('✏️ Edit item:', id);
            // Implementation for edit functionality
        };

        window.viewItem = function(id) {
            console.log('👁️ View item:', id);
            // Implementation for view functionality
        };

        window.deleteItem = function(id) {
            console.log('🗑️ Delete item:', id);
            // Implementation for delete functionality
        };

        // Search functionality
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('keyup', function() {
                const searchTerm = this.value.toLowerCase();
                const accordionItems = document.querySelectorAll('.accordion-item');

                accordionItems.forEach(item => {
                    const text = item.textContent.toLowerCase();
                    item.style.display = text.includes(searchTerm) ? '' : 'none';
                });
            });
        }
    });
</script>

<style>
    .transition-transform {
        transition: transform 0.3s ease;
    }

    .accordion-button:not(.collapsed) .bi-chevron-down {
        transform: rotate(180deg);
    }

    .accordion-item {
        border-radius: 10px !important;
        overflow: hidden;
    }

    .accordion-header button:focus {
        box-shadow: none;
        border-color: transparent;
    }
</style>

@endsection
