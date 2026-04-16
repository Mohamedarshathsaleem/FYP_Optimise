@extends('layouts.dashboard')

@section('title', 'EnPI & Baseline Management - Calculation Result')

@section('content')
<!-- Header Section -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <p class="text-secondary small mb-1">Pages / EnPI & Baseline Management</p>
        <h3 class="fw-bold">EnPI & Baseline Management <i class="bi bi-info-circle text-primary"></i></h3>
    </div>
    <div class="d-flex align-items-center">
        <div class="input-group search-box me-3">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="text" class="form-control" placeholder="Search">
        </div>
        <img src="{{ asset('images/user.png') }}" class="rounded-circle" alt="User" style="width: 40px; height: 40px;">
    </div>
</div>

<!-- Category Filter Section -->
<div class="row mb-4">
    <div class="col-md-3">
        <label class="form-label fw-bold">Category <span class="text-danger">*</span></label>
        <select class="form-select" id="categorySelect">
            <option value="2025" selected>2025</option>
            <option value="2024">2024</option>
            <option value="2023">2023</option>
        </select>
    </div>
</div>

<!-- Calculation Result Section -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white p-4">
        <h5 class="fw-bold mb-0">Calculation Result</h5>
    </div>
    <div class="card-body p-0">

        <!-- Data Table Section -->
        <div class="table-responsive">
            <table class="table table-bordered mb-0">
                <thead style="background: linear-gradient(135deg, #4472C4 0%, #2E5AA5 100%); color: white;">
                    <tr>
                        <th class="text-center py-3">Month / Year</th>
                        <th class="text-center py-3">BBU Electrical Consumption, (GJ/month)</th>
                        <th class="text-center py-3">BBU Process (tonne)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center py-2">Jan-25</td>
                        <td class="text-center py-2">49.38</td>
                        <td class="text-center py-2">1454</td>
                    </tr>
                    <tr>
                        <td class="text-center py-2">Feb-25</td>
                        <td class="text-center py-2">24.5</td>
                        <td class="text-center py-2">355</td>
                    </tr>
                    <tr>
                        <td class="text-center py-2">Mar-25</td>
                        <td class="text-center py-2">35.76</td>
                        <td class="text-center py-2">162</td>
                    </tr>
                    <tr>
                        <td class="text-center py-2">Apr-25</td>
                        <td class="text-center py-2">48.77</td>
                        <td class="text-center py-2">1444</td>
                    </tr>
                    <tr>
                        <td class="text-center py-2">May-25</td>
                        <td class="text-center py-2">47.37</td>
                        <td class="text-center py-2">832</td>
                    </tr>
                    <tr>
                        <td class="text-center py-2">Jun-25</td>
                        <td class="text-center py-2">36.34</td>
                        <td class="text-center py-2">272</td>
                    </tr>
                    <tr>
                        <td class="text-center py-2">Jul-25</td>
                        <td class="text-center py-2">30.52</td>
                        <td class="text-center py-2">0</td>
                    </tr>
                    <tr>
                        <td class="text-center py-2">Aug-25</td>
                        <td class="text-center py-2">49.38</td>
                        <td class="text-center py-2">1454</td>
                    </tr>
                    <tr>
                        <td class="text-center py-2">Sep-25</td>
                        <td class="text-center py-2">24.5</td>
                        <td class="text-center py-2">355</td>
                    </tr>
                    <tr>
                        <td class="text-center py-2">Oct-25</td>
                        <td class="text-center py-2">35.76</td>
                        <td class="text-center py-2">162</td>
                    </tr>
                    <tr>
                        <td class="text-center py-2">Nov-25</td>
                        <td class="text-center py-2">48.77</td>
                        <td class="text-center py-2">1444</td>
                    </tr>
                    <tr>
                        <td class="text-center py-2">Dec-25</td>
                        <td class="text-center py-2">47.37</td>
                        <td class="text-center py-2">832</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Result Summary Section -->
        <div class="mt-0">
            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <thead style="background: linear-gradient(135deg, #3965FF 0%, #3965FF 100%); color: white;">
                        <tr>
                            <th class="text-center py-3">Equation</th>
                            <th class="text-center py-3">BBU ELECTRICAL CONSUMPTION = 52.44 BBU PROCESS + 1385.1</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center py-3 fw-bold" style="background: linear-gradient(135deg, #4472C4 0%, #2E5AA5 100%); color: white;">R² Value</td>
                            <td class="text-center py-3">0.729</td>
                        </tr>
                        <tr>
                            <td class="text-center py-3 fw-bold" style="background: linear-gradient(135deg, #4472C4 0%, #2E5AA5 100%); color: white;">Regression Analysis</td>
                            <td class="text-center py-3">Moderate</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="p-4 d-flex justify-content-between align-items-center">
            <button class="btn btn-secondary" onclick="goBack()">Back</button>
            <div>
                <button class="btn btn-info me-2" onclick="showChart()">Show Chart</button>
                <button class="btn btn-primary px-4" onclick="goBack()" style="border-radius: 8px;">OK</button>
            </div>
        </div>

    </div>
</div>

<!-- Chart Modal -->
<div class="modal fade" id="chartModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Regression Analysis Chart</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="position-relative">
                    <!-- Chart Container -->
                    <div id="regressionChart" style="height: 500px; background: #f8f9fa; border-radius: 10px;">
                        <canvas id="chartCanvas" width="900" height="500" style="border-radius: 10px;"></canvas>
                    </div>

                    <!-- Legend -->
                    <div class="mt-3 d-flex justify-content-end">
                        <div class="d-flex align-items-center">
                            <div style="width: 12px; height: 12px; background: #8b5cf6; border-radius: 50%; margin-right: 8px;"></div>
                            <span class="small text-muted">BBU Process (tonne)</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function goBack() {
        window.location.href = '/enpi-baseline-management';
    }

    function showChart() {
        // Show chart in modal
        $('#chartModal').modal('show');

        // Draw chart when modal is shown
        setTimeout(function() {
            drawRegressionChart();
        }, 300);
    }

    function drawRegressionChart() {
        const canvas = document.getElementById('chartCanvas');
        if (!canvas) return;

        const ctx = canvas.getContext('2d');

        // Clear canvas
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        // Chart dimensions
        const margin = 80;
        const chartWidth = canvas.width - 2 * margin;
        const chartHeight = canvas.height - 2 * margin;

        // Draw axes
        ctx.strokeStyle = '#666';
        ctx.lineWidth = 1;

        // X-axis
        ctx.beginPath();
        ctx.moveTo(margin, canvas.height - margin);
        ctx.lineTo(canvas.width - margin, canvas.height - margin);
        ctx.stroke();

        // Y-axis
        ctx.beginPath();
        ctx.moveTo(margin, margin);
        ctx.lineTo(margin, canvas.height - margin);
        ctx.stroke();

        // Draw axis labels
        ctx.fillStyle = '#666';
        ctx.font = '14px Arial';
        ctx.textAlign = 'center';

        // X-axis labels
        for (let i = 0; i <= 50; i += 10) {
            const x = margin + (i / 50) * chartWidth;
            ctx.fillText(i.toString(), x, canvas.height - margin + 25);

            // Grid lines
            if (i > 0) {
                ctx.strokeStyle = '3965FF';
                ctx.lineWidth = 0.5;
                ctx.beginPath();
                ctx.moveTo(x, margin);
                ctx.lineTo(x, canvas.height - margin);
                ctx.stroke();
            }
        }

        // Y-axis labels
        ctx.textAlign = 'right';
        for (let i = -400; i <= 2000; i += 400) {
            const y = canvas.height - margin - ((i + 400) / 2400) * chartHeight;
            ctx.fillText(i.toString(), margin - 15, y + 4);

            // Grid lines
            if (i !== -400) {
                ctx.strokeStyle = '#e5e5e5';
                ctx.lineWidth = 0.5;
                ctx.beginPath();
                ctx.moveTo(margin, y);
                ctx.lineTo(canvas.width - margin, y);
                ctx.stroke();
            }
        }

        // Sample data points (based on table data)
        const dataPoints = [
            {x: 14.54, y: 49.38}, // Jan-25
            {x: 3.55, y: 24.5},   // Feb-25
            {x: 1.62, y: 35.76},  // Mar-25
            {x: 14.44, y: 48.77}, // Apr-25
            {x: 8.32, y: 47.37},  // May-25
            {x: 2.72, y: 36.34}   // Jun-25 (sample)
        ];

        // Normalize data points for chart
        const normalizedPoints = dataPoints.map(point => ({
            x: margin + (point.x / 20) * chartWidth,
            y: canvas.height - margin - ((point.y - 20) / 40) * chartHeight
        }));

        // Draw regression line (dashed)
        ctx.strokeStyle = '#8b5cf6';
        ctx.lineWidth = 3;
        ctx.setLineDash([8, 8]);
        ctx.beginPath();
        const startX = margin;
        const startY = canvas.height - margin - ((25 - 20) / 40) * chartHeight;
        const endX = canvas.width - margin;
        const endY = margin + ((50 - 20) / 40) * chartHeight;
        ctx.moveTo(startX, startY);
        ctx.lineTo(endX, endY);
        ctx.stroke();
        ctx.setLineDash([]);

        // Draw data points
        ctx.fillStyle = '#8b5cf6';
        normalizedPoints.forEach(point => {
            ctx.beginPath();
            ctx.arc(point.x, point.y, 8, 0, 2 * Math.PI);
            ctx.fill();
        });
    }
</script>
@endpush
