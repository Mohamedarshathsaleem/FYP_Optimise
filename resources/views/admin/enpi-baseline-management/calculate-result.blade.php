@extends('layouts.dashboard')

@section('title', 'EnPI & Baseline Management - Calculation Result')

@section('content')
{{-- Header --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
        <button type="button" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1 flex-shrink-0"
                onclick="goBack()" style="border-radius:8px;">
            <i class="bi bi-arrow-left"></i> Back
        </button>
        <div>
            <p class="text-secondary small mb-1">Pages / EnPI & Baseline Management</p>
            <h3 class="fw-bold mb-0">EnPI & Baseline Management <i class="bi bi-info-circle text-primary"></i></h3>
        </div>
    </div>
    <div class="d-flex align-items-center gap-2">
        <div class="input-group search-box me-1">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="text" class="form-control" placeholder="Search">
        </div>
        <img src="{{ asset('images/user.png') }}" class="rounded-circle" alt="User" style="width:40px;height:40px;">
    </div>
</div>

{{-- Category filter (read-only) --}}
<div class="row mb-4">
    <div class="col-md-3">
        <label class="form-label fw-bold">Category <span class="text-danger">*</span></label>
        <select class="form-select" disabled>
            <option selected>{{ $model->year }}</option>
        </select>
    </div>
</div>

{{-- ── Exportable content wrapper ───────────────────────────────────────────── --}}
<div id="exportContent">

{{-- ── Calculation Result Card ─────────────────────────────────────────────── --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white p-4 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0">Calculation Result — {{ $model->model_name }}</h5>
        {{-- Export dropdown --}}
        <div class="dropdown">
            <button class="btn btn-outline-secondary btn-sm dropdown-toggle d-flex align-items-center gap-2"
                    type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-download"></i> Export
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                <li>
                    <a class="dropdown-item d-flex align-items-center gap-2"
                       href="{{ route('enpi-baseline-management.export-excel', $model->id) }}">
                        <i class="bi bi-file-earmark-spreadsheet text-success"></i> Export to Excel
                    </a>
                </li>
                <li>
                    <button class="dropdown-item d-flex align-items-center gap-2"
                            id="btnExportPdf" onclick="exportPdf()">
                        <i class="bi bi-file-earmark-pdf text-danger"></i> Export to PDF
                    </button>
                </li>
            </ul>
        </div>
    </div>
    <div class="card-body p-0">

        {{-- Data Table --}}
        <div class="table-responsive">
            <table class="table table-bordered mb-0">
                <thead style="background:linear-gradient(135deg,#4472C4 0%,#2E5AA5 100%);color:white;">
                    <tr>
                        <th class="text-center py-3">Month / Year</th>
                        <th class="text-center py-3">
                            @if($model->dependent_variable_type === 'energy_data' && $model->energyData)
                                {{ $model->energyData->energy_type }} GJ ({{ $model->energyData->provider }})
                            @elseif($model->dependent_variable_type === 'energy_resource' && $model->energyResource)
                                {{ $model->energyResource->resource_type }} GJ ({{ $model->energyResource->provider }})
                            @else
                                {{ $model->dependent_variable }}
                            @endif
                        </th>
                        @for($i = 1; $i <= $model->number_of_independent_variables; $i++)
                            @php $xVar = 'independent_variable_x' . $i; @endphp
                            <th class="text-center py-3">{{ $model->$xVar }} (X{{ $i }})</th>
                        @endfor
                    </tr>
                </thead>
                <tbody>
                    @foreach($monthlyData as $row)
                    <tr>
                        <td class="text-center py-2">{{ $row['month'] }}</td>
                        <td class="text-center py-2">{{ number_format($row['dependent'], 2) }}</td>
                        @for($i = 1; $i <= $model->number_of_independent_variables; $i++)
                            <td class="text-center py-2">
                                {{ number_format($row['independent_x' . $i] ?? 0, 2) }}
                            </td>
                        @endfor
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Result Summary --}}
        <div class="px-4 pb-2 pt-3">
            {{-- Equation banner --}}
            <div class="rounded-3 px-4 py-3 mb-3 d-flex align-items-start gap-3"
                 style="background:#eef2ff;border:1px solid #c7d5ff;">
                <i class="bi bi-function fs-4 text-primary mt-1 flex-shrink-0"></i>
                <div>
                    <div class="text-primary fw-semibold mb-1"
                         style="font-size:11px;letter-spacing:.6px;text-transform:uppercase;">
                        Regression Equation
                    </div>
                    <div class="fw-bold text-dark" style="font-size:1rem;font-family:monospace;word-break:break-word;">
                        {{ $model->equation }}
                    </div>
                </div>
            </div>

            {{-- Stats row --}}
            <div class="row g-3 mb-3">
                <div class="col-sm-6">
                    <div class="rounded-3 px-4 py-3 text-center h-100"
                         style="background:#f8f9fa;border:1px solid #dee2e6;">
                        <div class="text-muted fw-semibold mb-2"
                             style="font-size:11px;letter-spacing:.6px;text-transform:uppercase;">
                            R² Value
                        </div>
                        <div class="fw-bold text-primary" style="font-size:2rem;line-height:1;">
                            {{ number_format($model->r_squared, 3) }}
                        </div>
                        <div class="text-muted mt-1" style="font-size:12px;">
                            {{ round($model->r_squared * 100, 1) }}% variance explained
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="rounded-3 px-4 py-3 text-center h-100"
                         style="background:#f8f9fa;border:1px solid #dee2e6;">
                        <div class="text-muted fw-semibold mb-2"
                             style="font-size:11px;letter-spacing:.6px;text-transform:uppercase;">
                            Correlation Strength
                        </div>
                        <div class="mt-1">
                            <span class="badge bg-{{ $model->correlationBadgeColor }} px-4 py-2"
                                  style="font-size:1.1rem;">
                                {{ $model->correlation_strength }}
                            </span>
                        </div>
                        <div class="text-muted mt-2" style="font-size:12px;">
                            {{ $model->number_of_independent_variables }}
                            independent variable{{ $model->number_of_independent_variables > 1 ? 's' : '' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- ── Regression Charts (one per independent variable) ─────────────────── --}}
<div class="card border-0 shadow-sm mt-4">
    <div class="card-header bg-white p-4 d-flex align-items-center gap-3">
        <h5 class="fw-bold mb-0">
            Regression Analysis
            {{ count($regressionData['charts']) > 1 ? 'Charts' : 'Chart' }}
        </h5>
        @if(count($regressionData['charts']) > 1)
            <span class="badge bg-light border text-muted" style="font-size:12px;">
                Y plotted against each independent variable
            </span>
        @endif
    </div>
    <div class="card-body p-4">
        {{-- Shared legend (same colours for every chart) --}}
        <div class="d-flex justify-content-end align-items-center gap-4 mb-4">
            <div class="d-flex align-items-center gap-2">
                <svg width="18" height="18" viewBox="0 0 18 18">
                    <circle cx="9" cy="9" r="7" fill="#3965FF" stroke="#fff" stroke-width="2"/>
                </svg>
                <span class="small text-muted fw-semibold">Data Points</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <svg width="36" height="10" viewBox="0 0 36 10">
                    <line x1="0" y1="5" x2="36" y2="5"
                          stroke="#e74c3c" stroke-width="2.5"
                          stroke-dasharray="8 5" stroke-linecap="round"/>
                </svg>
                <span class="small text-muted fw-semibold">Regression Line</span>
            </div>
        </div>

        <div class="row g-4">
            @foreach($regressionData['charts'] as $chartIdx => $chart)
            @php
                $colClass = count($regressionData['charts']) === 1 ? 'col-12' : 'col-xl-6';
                $canvasH  = count($regressionData['charts']) === 1 ? 560 : 480;
            @endphp
            <div class="{{ $colClass }}">
                <div class="rounded-3 p-3" style="background:#f8f9fc;border:1px solid #e9ecef;">
                    <p class="text-center fw-semibold text-muted mb-2" style="font-size:13px;">
                        Y &nbsp;vs&nbsp; X{{ $chart['xi'] }}: {{ $chart['xLabel'] }}
                    </p>
                    <canvas id="chartCanvas{{ $chartIdx }}"
                            width="900" height="{{ $canvasH }}"
                            style="width:100%;border-radius:6px;"></canvas>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

</div>{{-- /#exportContent --}}
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
<script>
    const allCharts          = @json($regressionData['charts']);
    const modelName          = @json($model->model_name);
    const yLabel             = @json($model->dependent_label);
    const equation           = @json($model->equation);
    const rSquared           = @json($model->r_squared);
    const correlationStrength = @json($model->correlation_strength);
    const modelYear          = @json($model->year);
    const numVars            = @json($model->number_of_independent_variables);
    const monthlyData        = @json($monthlyData);
    @php
        $xLabelsArr = [];
        for ($i = 1; $i <= $model->number_of_independent_variables; $i++) {
            $key = 'independent_variable_x' . $i;
            $xLabelsArr[] = $model->$key ?? ('X' . $i);
        }
    @endphp
    const xLabels = @json($xLabelsArr);

    document.addEventListener('DOMContentLoaded', function () {
        allCharts.forEach((chart, idx) => {
            const canvas = document.getElementById('chartCanvas' + idx);
            if (canvas && chart.dataPoints.length) {
                drawChart(canvas, chart);
            }
        });
    });

    function goBack() {
        window.location.href = '{{ route("enpi-baseline-management.index", ["category" => $model->year]) }}';
    }

    // ── Single chart renderer ─────────────────────────────────────────────────
    function drawChart(canvas, chart) {
        const ctx     = canvas.getContext('2d');
        const W       = canvas.width;
        const H       = canvas.height;
        const mL = 110, mR = 40, mT = 60, mB = 72;
        const plotW   = W - mL - mR;
        const plotH   = H - mT - mB;

        ctx.clearRect(0, 0, W, H);

        // ── Axis extents ──────────────────────────────────────────────────────
        const xVals  = chart.dataPoints.map(p => p.x);
        const yVals  = chart.dataPoints.map(p => p.y);
        const lineYs = chart.regressionLine.map(p => p.y);

        const rawXMin = Math.min(...xVals);
        const rawXMax = Math.max(...xVals);
        const rawYMin = Math.min(...yVals, ...lineYs);
        const rawYMax = Math.max(...yVals, ...lineYs);

        const xPad = (rawXMax - rawXMin) * 0.15 || Math.abs(rawXMin) * 0.15 || 10;
        const yPad = (rawYMax - rawYMin) * 0.15 || Math.abs(rawYMin) * 0.15 || 10;

        const xMin = rawXMin - xPad,  xMax = rawXMax + xPad;
        const yMin = rawYMin - yPad,  yMax = rawYMax + yPad;

        const mapX = x => mL + ((x - xMin) / (xMax - xMin)) * plotW;
        const mapY = y => mT + plotH - ((y - yMin) / (yMax - yMin)) * plotH;

        // ── Background ────────────────────────────────────────────────────────
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, W, H);
        ctx.fillStyle = '#f8f9fc';
        ctx.fillRect(mL, mT, plotW, plotH);

        // ── Grid lines ────────────────────────────────────────────────────────
        const xTicks = niceTickValues(xMin, xMax, 7);
        const yTicks = niceTickValues(yMin, yMax, 6);

        ctx.strokeStyle = '#e2e6ea';
        ctx.lineWidth   = 1;
        ctx.setLineDash([4, 4]);

        xTicks.forEach(x => {
            const cx = mapX(x);
            if (cx < mL || cx > mL + plotW) return;
            ctx.beginPath(); ctx.moveTo(cx, mT); ctx.lineTo(cx, mT + plotH); ctx.stroke();
        });
        yTicks.forEach(y => {
            const cy = mapY(y);
            if (cy < mT || cy > mT + plotH) return;
            ctx.beginPath(); ctx.moveTo(mL, cy); ctx.lineTo(mL + plotW, cy); ctx.stroke();
        });
        ctx.setLineDash([]);

        // ── Axes ──────────────────────────────────────────────────────────────
        ctx.strokeStyle = '#495057'; ctx.lineWidth = 2;
        ctx.beginPath(); ctx.moveTo(mL, mT + plotH); ctx.lineTo(mL + plotW, mT + plotH); ctx.stroke();
        ctx.beginPath(); ctx.moveTo(mL, mT);          ctx.lineTo(mL, mT + plotH);          ctx.stroke();

        // ── Tick labels ───────────────────────────────────────────────────────
        ctx.font      = '12px Arial';
        ctx.fillStyle = '#495057';

        ctx.textAlign    = 'center';
        ctx.textBaseline = 'top';
        xTicks.forEach(x => {
            const cx = mapX(x);
            if (cx < mL - 5 || cx > mL + plotW + 5) return;
            ctx.fillStyle = '#495057';
            ctx.fillText(formatNum(x), cx, mT + plotH + 8);
            ctx.strokeStyle = '#495057'; ctx.lineWidth = 1;
            ctx.beginPath(); ctx.moveTo(cx, mT + plotH); ctx.lineTo(cx, mT + plotH + 4); ctx.stroke();
        });

        ctx.textAlign    = 'right';
        ctx.textBaseline = 'middle';
        yTicks.forEach(y => {
            const cy = mapY(y);
            if (cy < mT - 5 || cy > mT + plotH + 5) return;
            ctx.fillStyle = '#495057';
            ctx.fillText(formatNum(y), mL - 8, cy);
            ctx.strokeStyle = '#495057'; ctx.lineWidth = 1;
            ctx.beginPath(); ctx.moveTo(mL - 4, cy); ctx.lineTo(mL, cy); ctx.stroke();
        });

        // ── Axis titles ───────────────────────────────────────────────────────
        ctx.fillStyle    = '#212529';
        ctx.font         = 'bold 13px Arial';
        ctx.textAlign    = 'center';
        ctx.textBaseline = 'bottom';
        ctx.fillText(chart.xLabel, mL + plotW / 2, H - 6);

        ctx.save();
        ctx.translate(16, mT + plotH / 2);
        ctx.rotate(-Math.PI / 2);
        ctx.textBaseline = 'top';
        ctx.fillText(yLabel, 0, 0);
        ctx.restore();

        // ── Regression line (2 anchor points → perfectly straight) ───────────
        if (chart.regressionLine.length >= 2) {
            const p0 = chart.regressionLine[0];
            const p1 = chart.regressionLine[chart.regressionLine.length - 1];
            ctx.strokeStyle = '#e74c3c';
            ctx.lineWidth   = 2.5;
            ctx.setLineDash([8, 5]);
            ctx.beginPath();
            ctx.moveTo(mapX(p0.x), mapY(p0.y));
            ctx.lineTo(mapX(p1.x), mapY(p1.y));
            ctx.stroke();
            ctx.setLineDash([]);
        }

        // ── Data points ───────────────────────────────────────────────────────
        const R = 6;
        chart.dataPoints.forEach((p, i) => {
            const cx = mapX(p.x);
            const cy = mapY(p.y);

            ctx.shadowColor = 'rgba(57,101,255,0.2)';
            ctx.shadowBlur  = 6;

            ctx.fillStyle = '#3965FF';
            ctx.beginPath(); ctx.arc(cx, cy, R, 0, 2 * Math.PI); ctx.fill();

            ctx.shadowBlur  = 0;
            ctx.strokeStyle = '#ffffff'; ctx.lineWidth = 2;
            ctx.stroke();

            // Month label: alternate above/below
            ctx.fillStyle    = '#343a40';
            ctx.font         = '10px Arial';
            ctx.textAlign    = 'center';
            const above      = i % 2 === 0;
            ctx.textBaseline = above ? 'bottom' : 'top';
            ctx.fillText(p.month, cx, above ? cy - R - 4 : cy + R + 4);
        });

        ctx.shadowBlur = 0;

        // ── Tooltip overlay ───────────────────────────────────────────────────
        const tooltipId = 'chartTooltip_' + chart.xi;
        let tip = document.getElementById(tooltipId);
        if (!tip) {
            tip = document.createElement('div');
            tip.id = tooltipId;
            tip.style.cssText = 'position:absolute;display:none;background:#212529;color:#fff;' +
                'padding:6px 10px;border-radius:6px;font-size:12px;pointer-events:none;' +
                'white-space:nowrap;z-index:10;box-shadow:0 2px 8px rgba(0,0,0,.25);';
            canvas.parentElement.style.position = 'relative';
            canvas.parentElement.appendChild(tip);
        }

        const scaleX = W / canvas.getBoundingClientRect().width;
        const scaleY = H / canvas.getBoundingClientRect().height;

        canvas.addEventListener('mousemove', function (e) {
            const rect = canvas.getBoundingClientRect();
            const mx   = (e.clientX - rect.left) * scaleX;
            const my   = (e.clientY - rect.top)  * scaleY;
            const hit  = chart.dataPoints.find(p => Math.hypot(mapX(p.x) - mx, mapY(p.y) - my) < R * 2.5);
            if (hit) {
                canvas.style.cursor = 'pointer';
                tip.innerHTML = `<strong>${hit.month}</strong><br>` +
                    `${chart.xLabel}: <strong>${formatNum(hit.x)}</strong><br>` +
                    `${yLabel}: <strong>${formatNum(hit.y)}</strong>`;
                tip.style.display = 'block';
                tip.style.left    = (e.clientX - rect.left + 14) + 'px';
                tip.style.top     = (e.clientY - rect.top  - 10) + 'px';
            } else {
                canvas.style.cursor = 'default';
                tip.style.display   = 'none';
            }
        });
        canvas.addEventListener('mouseleave', () => { tip.style.display = 'none'; });
    }

    // ── Helpers ───────────────────────────────────────────────────────────────
    function niceTickValues(min, max, count) {
        const range = max - min;
        const raw   = range / count;
        const mag   = Math.pow(10, Math.floor(Math.log10(raw)));
        let step    = mag;
        for (const s of [1, 2, 2.5, 5, 10]) {
            if (s * mag >= raw) { step = s * mag; break; }
        }
        const start = Math.ceil(min / step) * step;
        const ticks = [];
        for (let v = start; v <= max + step * 0.01; v += step) {
            ticks.push(parseFloat(v.toPrecision(10)));
        }
        return ticks;
    }

    function formatNum(n) {
        if (Math.abs(n) >= 1000) return n.toLocaleString(undefined, { maximumFractionDigits: 0 });
        if (Number.isInteger(n)) return n.toString();
        return parseFloat(n.toFixed(2)).toString();
    }

    // ── PDF export ────────────────────────────────────────────────────────────
    async function exportPdf() {
        if (typeof window.jspdf === 'undefined') {
            alert('PDF library is still loading — please wait a moment and try again.');
            return;
        }

        const btn      = document.getElementById('btnExportPdf');
        const origHtml = btn.innerHTML;
        btn.disabled   = true;
        btn.innerHTML  = '<span class="spinner-border spinner-border-sm me-1"></span> Generating…';

        try {
            const { jsPDF } = window.jspdf;
            const pdf  = new jsPDF({ orientation: 'portrait', unit: 'mm', format: 'a4' });
            const pageW = pdf.internal.pageSize.getWidth();
            const pageH = pdf.internal.pageSize.getHeight();
            const mg    = 15;
            const cW    = pageW - 2 * mg;

            // ── Page 1: Model summary ─────────────────────────────────────────
            let y = mg;

            // Title banner
            pdf.setFillColor(57, 101, 255);
            pdf.rect(mg, y, cW, 16, 'F');
            pdf.setFontSize(13); pdf.setFont(undefined, 'bold');
            pdf.setTextColor(255, 255, 255);
            pdf.text(modelName, mg + 5, y + 10);
            pdf.setFontSize(9); pdf.setFont(undefined, 'normal');
            pdf.text('Year: ' + modelYear, pageW - mg - 5, y + 10, { align: 'right' });
            y += 22;

            // Equation box
            pdf.setFillColor(238, 242, 255);
            pdf.setDrawColor(199, 213, 255);
            pdf.roundedRect(mg, y, cW, 16, 2, 2, 'FD');
            pdf.setFontSize(7); pdf.setFont(undefined, 'bold');
            pdf.setTextColor(57, 101, 255);
            pdf.text('REGRESSION EQUATION', mg + 5, y + 6);
            pdf.setFont(undefined, 'normal');
            pdf.setTextColor(33, 37, 41);
            const eqFitted = pdf.splitTextToSize(equation, cW - 10);
            pdf.setFontSize(8.5);
            pdf.text(eqFitted[0], mg + 5, y + 12);
            y += 22;

            // Stat cards
            const cardW = (cW - 5) / 2;

            pdf.setFillColor(248, 249, 250); pdf.setDrawColor(222, 226, 230);
            pdf.roundedRect(mg, y, cardW, 24, 2, 2, 'FD');
            pdf.setFontSize(7); pdf.setFont(undefined, 'bold');
            pdf.setTextColor(108, 117, 125);
            pdf.text('R² VALUE', mg + cardW / 2, y + 7, { align: 'center' });
            pdf.setFontSize(18); pdf.setTextColor(57, 101, 255);
            pdf.text(String(rSquared), mg + cardW / 2, y + 17, { align: 'center' });
            pdf.setFontSize(7); pdf.setFont(undefined, 'normal'); pdf.setTextColor(108, 117, 125);
            pdf.text((rSquared * 100).toFixed(1) + '% variance explained', mg + cardW / 2, y + 22, { align: 'center' });

            const cx2 = mg + cardW + 5;
            pdf.setFillColor(248, 249, 250); pdf.setDrawColor(222, 226, 230);
            pdf.roundedRect(cx2, y, cardW, 24, 2, 2, 'FD');
            pdf.setFontSize(7); pdf.setFont(undefined, 'bold'); pdf.setTextColor(108, 117, 125);
            pdf.text('CORRELATION STRENGTH', cx2 + cardW / 2, y + 7, { align: 'center' });
            pdf.setFontSize(14); pdf.setTextColor(33, 37, 41); pdf.setFont(undefined, 'bold');
            pdf.text(correlationStrength, cx2 + cardW / 2, y + 17, { align: 'center' });
            pdf.setFontSize(7); pdf.setFont(undefined, 'normal'); pdf.setTextColor(108, 117, 125);
            pdf.text(numVars + ' independent variable' + (numVars > 1 ? 's' : ''), cx2 + cardW / 2, y + 22, { align: 'center' });
            y += 30;

            // Data table
            const tableHead = [['Month / Year', yLabel + ' (Y)', ...xLabels.map((l, i) => l + ' (X' + (i + 1) + ')')]];
            const tableBody = monthlyData.map(row => {
                const r = [row.month, parseFloat(row.dependent).toFixed(2)];
                for (let i = 1; i <= numVars; i++) {
                    r.push(parseFloat(row['independent_x' + i] || 0).toFixed(2));
                }
                return r;
            });

            pdf.autoTable({
                startY: y,
                head: tableHead,
                body: tableBody,
                margin: { left: mg, right: mg },
                headStyles: {
                    fillColor: [68, 114, 196], textColor: [255, 255, 255],
                    fontStyle: 'bold', fontSize: 8, halign: 'center', cellPadding: 3,
                },
                bodyStyles: { fontSize: 8, halign: 'center', cellPadding: 2.5 },
                alternateRowStyles: { fillColor: [248, 249, 252] },
            });

            // ── One page per chart ────────────────────────────────────────────
            for (let i = 0; i < allCharts.length; i++) {
                const canvas = document.getElementById('chartCanvas' + i);
                if (!canvas) continue;

                pdf.addPage();
                let cy = mg;

                // Chart title banner
                pdf.setFillColor(57, 101, 255);
                pdf.rect(mg, cy, cW, 12, 'F');
                pdf.setFontSize(10); pdf.setFont(undefined, 'bold');
                pdf.setTextColor(255, 255, 255);
                pdf.text('Y  vs  X' + allCharts[i].xi + ': ' + allCharts[i].xLabel,
                         mg + cW / 2, cy + 8, { align: 'center' });
                cy += 16;

                // Chart image — fill remaining page
                const imgData = canvas.toDataURL('image/jpeg', 0.95);
                const imgH    = Math.min(canvas.height * (cW / canvas.width), pageH - cy - mg);
                pdf.addImage(imgData, 'JPEG', mg, cy, cW, imgH);
            }

            pdf.save(modelName + '-' + modelYear + '.pdf');

        } catch (err) {
            console.error('PDF export failed:', err);
            alert('PDF generation failed: ' + err.message);
        } finally {
            btn.disabled  = false;
            btn.innerHTML = origHtml;
        }
    }
</script>
@endpush
