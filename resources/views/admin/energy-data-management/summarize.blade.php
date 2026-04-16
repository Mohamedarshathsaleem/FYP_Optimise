@extends('layouts.dashboard')

@section('title', 'Utility Consumption')

@section('content')
<div class="container-fluid p-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Utility Consumption</h4>
    </div>

    {{-- Summary Info Card --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <small class="text-muted">Category</small>
                    <p class="fw-bold mb-0">{{ $category }}</p>
                </div>
                <div class="col-md-4">
                    <small class="text-muted">Period</small>
                    <p class="fw-bold mb-0">{{ date('M Y', strtotime($startMonth)) }} - {{ date('M Y', strtotime($endMonth)) }}</p>
                </div>
                <div class="col-md-4">
                    <small class="text-muted">Total Energy Consumption</small>
                    <p class="fw-bold mb-0 text-primary">{{ number_format(array_sum(array_column($monthlyTotals, 'total_gj')), 2) }} GJ</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Data Table --}}
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="summaryTable">
                <thead style="background: linear-gradient(135deg, #3965FF 0%, #5B8CFF 100%); color: white;">
                    <tr>
                        <th class="text-center py-3">Month</th>
                        <th class="text-center py-3">Total Energy Usage (GJ)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($monthlyTotals as $data)
                    <tr>
                        <td class="text-center py-3">{{ date('M-y', strtotime($data['month'])) }}</td>
                        <td class="text-center py-3">{{ number_format($data['total_gj'], 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="d-flex justify-content-between mt-4">
        <button class="btn btn-secondary px-5 py-2" onclick="window.history.back()" style="border-radius: 10px;">
            <i class="bi bi-arrow-left me-2"></i>Back
        </button>
        <button class="btn btn-primary px-5 py-2" onclick="exportToPDF()" style="border-radius: 10px;">
            Save as PDF
        </button>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>
function exportToPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    // Title
    doc.setFontSize(18);
    doc.setFont(undefined, 'bold');
    doc.text('Utility Consumption', 14, 20);

    // Summary info
    doc.setFontSize(11);
    doc.setFont(undefined, 'normal');
    doc.text('Category: {{ $category }}', 14, 35);
    doc.text('Period: {{ date("M Y", strtotime($startMonth)) }} - {{ date("M Y", strtotime($endMonth)) }}', 14, 42);
    doc.text('Total: {{ number_format(array_sum(array_column($monthlyTotals, "total_gj")), 2) }} GJ', 14, 49);

    // Table data
    const tableData = [
        @foreach($monthlyTotals as $data)
        ['{{ date("M-y", strtotime($data["month"])) }}', '{{ number_format($data["total_gj"], 2) }}'],
        @endforeach
    ];

    doc.autoTable({
        startY: 60,
        head: [['Month', 'Total Energy Usage (GJ)']],
        body: tableData,
        theme: 'grid',
        headStyles: {
            fillColor: [57, 101, 255],
            fontSize: 12,
            fontStyle: 'bold',
            halign: 'center'
        },
        bodyStyles: {
            halign: 'center'
        }
    });

    doc.save('Energy_Consumption_{{ $category }}_{{ date("Y-m-d") }}.pdf');
}

function exportToExcel() {
    const tableData = [
        ['Utility Consumption'],
        [''],
        ['Category', '{{ $category }}'],
        ['Period', '{{ date("M Y", strtotime($startMonth)) }} - {{ date("M Y", strtotime($endMonth)) }}'],
        ['Total', '{{ number_format(array_sum(array_column($monthlyTotals, "total_gj")), 2) }} GJ'],
        [''],
        ['Month', 'Total Energy Usage (GJ)'],
        @foreach($monthlyTotals as $data)
        ['{{ date("M-y", strtotime($data["month"])) }}', {{ $data['total_gj'] }}],
        @endforeach
    ];

    const ws = XLSX.utils.aoa_to_sheet(tableData);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'Energy Consumption');
    
    XLSX.writeFile(wb, 'Energy_Consumption_{{ $category }}_{{ date("Y-m-d") }}.xlsx');
}
</script>
@endpush
@endsection