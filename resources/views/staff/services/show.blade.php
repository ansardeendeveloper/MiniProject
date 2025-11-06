@extends('layouts.stafftemplates')

@section('title', 'Service Details')

@section('head')
<style>
    .material-icons {
        vertical-align: middle;
        font-size: 18px;
        margin-right: 4px;
    }

    .service-card { border-left: 4px solid #007bff; }
    .info-row {
        margin-bottom: 0.5rem;
        padding: 0.5rem 0;
        border-bottom: 1px solid #f0f0f0;
    }
    .info-row:last-child { border-bottom: none; }

    .invoice-modal .modal-dialog {
        max-width: 210mm;
    }
    
    .invoice-container {
        width: 210mm;
        min-height: 297mm;
        padding: 15mm;
        box-sizing: border-box;
        background: white;
        margin: 0 auto;
        font-family: 'Arial', sans-serif;
        font-size: 12px;
        line-height: 1.4;
        color: #333;
    }
    
    .invoice-header {
        border-bottom: 3px double #333;
        padding-bottom: 10px;
        margin-bottom: 20px;
    }
    
    .company-name {
        font-size: 28px;
        font-weight: bold;
        color: #2c3e50;
        margin: 0;
        text-transform: uppercase;
    }
    
    .invoice-title {
        font-size: 32px;
        font-weight: bold;
        text-align: center;
        margin: 20px 0;
        color: #2c3e50;
        text-transform: uppercase;
    }
    
    .invoice-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 15px;
    }
    
    .invoice-col {
        flex: 1;
    }
    
    .invoice-table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
    }
    
    .invoice-table th {
        background: #34495e;
        color: white;
        padding: 10px 8px;
        text-align: left;
        font-weight: bold;
        border: 1px solid #2c3e50;
    }
    
    .invoice-table td {
        padding: 10px 8px;
        border: 1px solid #bdc3c7;
    }
    
    .text-right {
        text-align: right;
    }
    
    .text-center {
        text-align: center;
    }
    
    .total-row {
        background: #ecf0f1;
        font-weight: bold;
        border-top: 2px solid #34495e !important;
    }
    
    .amount-section {
        margin-top: 20px;
        padding: 15px;
        background: #f8f9fa;
        border: 1px solid #bdc3c7;
    }
    
    .invoice-footer {
        margin-top: 40px;
        border-top: 2px solid #34495e;
        padding-top: 20px;
    }
    
    .signature-area {
        display: flex;
        justify-content: space-between;
        margin-top: 60px;
    }
    
    .signature-box {
        text-align: center;
        width: 200px;
    }
    
    .signature-line {
        border-top: 1px solid #333;
        margin-top: 40px;
        padding-top: 5px;
    }
    
    .status-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 10px;
        font-weight: bold;
        text-transform: uppercase;
    }
    
    .status-completed {
        background: #27ae60;
        color: white;
    }
    
    .tax-inclusive {
        background: #3498db;
        color: white;
        padding: 2px 8px;
        border-radius: 3px;
        font-size: 10px;
        margin-left: 5px;
    }

    /* Print Styles for Invoice */
    @media print {
        body * {
            visibility: hidden;
        }
        .invoice-modal .modal-content,
        .invoice-modal .modal-content * {
            visibility: visible;
        }
        .invoice-modal .modal {
            position: absolute;
            left: 0;
            top: 0;
            margin: 0;
            padding: 0;
            visibility: visible;
        }
        .invoice-modal .modal-dialog {
            max-width: none;
            width: 210mm;
            margin: 0;
        }
        .no-print {
            display: none !important;
        }
    }
</style>
@endsection

@section('content')
<div class="container my-4">
    <!-- Service Details Card -->
    <div class="card shadow service-card">
        <div class="card-header bg-light">
            <h4 class="mb-0">Service Details</h4>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="text-primary mb-3">Basic Information</h5>
                    <div class="info-row">
                        <strong>Job ID:</strong>
                        <span class="float-end">{{ strtoupper($service->job_id ?? 'N/A') }}</span>
                    </div>
                    <div class="info-row">
                        <strong>Start Time:</strong>
                        <span class="float-end">
                            {{ $service->service_start_datetime ? \Carbon\Carbon::parse($service->service_start_datetime)->format('d/m/Y H:i') : 'N/A' }}
                        </span>
                    </div>
                    <div class="info-row">
                        <strong>End Time:</strong>
                        <span class="float-end">
                            {{ $service->service_end_datetime ? \Carbon\Carbon::parse($service->service_end_datetime)->format('d/m/Y H:i') : 'N/A' }}
                        </span>
                    </div>
                    <div class="info-row">
                        <strong>Total Amount:</strong>
                        <span class="float-end text-success fw-bold">₹{{ number_format($service->amount ?? 0, 2) }}</span>
                    </div>
                    <div class="info-row">
                        <strong>Status:</strong>
                        @php
                            $badge = match($service->status) {
                                'pending' => 'bg-warning',
                                'in_progress' => 'bg-info',
                                'completed' => 'bg-success',
                                'cancelled' => 'bg-danger',
                                default => 'bg-secondary'
                            };
                        @endphp
                        <span class="badge {{ $badge }} float-end">{{ strtoupper($service->status) }}</span>
                    </div>
                </div>

                <div class="col-md-6">
                    <h5 class="text-primary mb-3">Customer & Vehicle Details</h5>
                    <div class="info-row">
                        <strong>Vehicle:</strong>
                        <span class="float-end">
                            {{ strtoupper($service->vehicle->registration_no ?? 'N/A') }}
                            @if($service->vehicle && $service->vehicle->model)
                                <small class="text-muted">({{ strtoupper($service->vehicle->model) }})</small>
                            @endif
                        </span>
                    </div>
                    <div class="info-row">
                        <strong>Customer:</strong>
                        <span class="float-end">{{ strtoupper(optional(optional($service->vehicle)->customer)->name ?? 'N/A') }}</span>
                    </div>
                    <div class="info-row">
                        <strong>Mobile:</strong>
                        <span class="float-end">{{ optional(optional($service->vehicle)->customer)->mobile_number ?? 'N/A' }}</span>
                    </div>
                    <div class="info-row">
                        <strong>Service Types:</strong>
                        <span class="float-end">
                            @php $types = json_decode($service->service_types ?? '[]', true); @endphp
                            {{ !empty($types) ? strtoupper(implode(', ', $types)) : 'NO TYPES SPECIFIED' }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-12 text-center">
                    @if($service->status === 'completed')
                        <!-- Button to view invoice in modal -->
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#invoiceModal">
                            View Invoice
                        </button>
                        <a href="{{ route('staff.services.index') }}" class="btn btn-secondary">Back</a>
                    @elseif($service->status === 'in_progress')
                        <a href="{{ route('staff.services.edit', $service->id) }}" class="btn btn-primary">Edit Service</a>
                        <a href="{{ route('staff.services.index') }}" class="btn btn-secondary">Back</a>
                    @else
                        <a href="{{ route('staff.services.index') }}" class="btn btn-secondary">Back</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Invoice Modal -->
<div class="modal fade invoice-modal" id="invoiceModal" tabindex="-1" aria-labelledby="invoiceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header no-print">
                <h5 class="modal-title" id="invoiceModalLabel">Invoice - {{ strtoupper($service->job_id ?? 'N/A') }}</h5>
                <button type="button" class="btn-print btn btn-success btn-sm me-2" onclick="printInvoice()">
                    Print Invoice
                </button>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <!-- A4 Invoice Container -->
                <div class="invoice-container">
                    <!-- Invoice Header -->
                    <div class="invoice-header">
                        <div class="invoice-row">
                            <div class="invoice-col">
                                <h1 class="company-name">AUTOCARE WORKSHOP</h1>
                                <p>
                                    NO 7 Villupuram Main Rd, Moolakulam<br>
                                    Puducherry - 605010<br>
                                    📞 +91 9842896939 | ✉️ info@autocare.com<br>
                                    GSTIN: 34BBLPS260A1ZO
                                </p>
                            </div>
                            <div class="invoice-col text-right">
                                <h2 class="invoice-title">TAX INVOICE</h2>
                                <p>
                                    <strong>Invoice No:</strong> {{ strtoupper($service->job_id ?? 'SR-' . $service->id) }}<br>
                                    <strong>Date:</strong> {{ now('Asia/Kolkata')->format('d/m/Y') }}<br>
                                    <strong>Time:</strong> {{ now('Asia/Kolkata')->format('H:i') }}<br>
                                    <strong>Status:</strong> 
                                    <span class="status-badge status-completed">
                                        COMPLETED
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Customer & Vehicle Information -->
                    <div class="invoice-row">
                        <div class="invoice-col">
                            <h3 style="color: #2c3e50; border-bottom: 1px solid #bdc3c7; padding-bottom: 5px;">BILLED TO</h3>
                            <p>
                                <strong>Name:</strong> {{ strtoupper(optional(optional($service->vehicle)->customer)->name ?? 'N/A') }}<br>
                                <strong>Mobile:</strong> {{ optional(optional($service->vehicle)->customer)->mobile_number ?? 'N/A' }}<br>
                            </p>
                        </div>
                        <div class="invoice-col">
                            <h3 style="color: #2c3e50; border-bottom: 1px solid #bdc3c7; padding-bottom: 5px;">VEHICLE DETAILS</h3>
                            <p>
                                <strong>Registration:</strong> {{ strtoupper($service->vehicle->registration_no ?? 'N/A') }}<br>
                                <strong>Model:</strong> {{ strtoupper($service->vehicle->model ?? 'N/A') }}<br>
                                <strong>Manufacturer:</strong> {{ strtoupper($service->vehicle->manufacturer ?? 'N/A') }}<br>
                                <strong>Year:</strong> {{ $service->vehicle->year ?? 'N/A' }}
                            </p>
                        </div>
                    </div>

                    <!-- Service Information -->
                    <div class="invoice-row">
                        <div class="invoice-col">
                            <h3 style="color: #2c3e50; border-bottom: 1px solid #bdc3c7; padding-bottom: 5px;">SERVICE INFORMATION</h3>
                            <p>
                                <strong>Service Date:</strong> 
                                {{ $service->service_start_datetime ? \Carbon\Carbon::parse($service->service_start_datetime)->format('d/m/Y') : 'N/A' }}<br>
                                <strong>Service Time:</strong> 
                                {{ $service->service_start_datetime ? \Carbon\Carbon::parse($service->service_start_datetime)->format('H:i') : 'N/A' }}<br>
                            </p>
                        </div>
                    </div>

                    <!-- Services Table -->
                    <h3 style="color: #2c3e50; border-bottom: 1px solid #bdc3c7; padding-bottom: 5px; margin-top: 20px;">SERVICES PROVIDED</h3>
                    <table class="invoice-table">
                        <thead>
                            <tr>
                                <th width="5%">SR NO</th>
                                <th width="55%">SERVICE DESCRIPTION</th>
                                <th width="20%" class="text-right">UNIT PRICE (₹)</th>
                                <th width="20%" class="text-right">AMOUNT (₹)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $serviceTypes = json_decode($service->service_types ?? '[]', true) ?? [];
                                $totalAmount = $service->amount ?? 1800.00;
                                
                                // Calculate tax breakdown (for information only)
                                $baseAmount = round($totalAmount / 1.18, 2);
                                $gstAmount = $totalAmount - $baseAmount;
                                $cgst = $gstAmount / 2;
                                $sgst = $gstAmount / 2;
                            @endphp
                            
                            @if(!empty($serviceTypes))
                                @foreach($serviceTypes as $index => $serviceType)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            {{ ucwords($serviceType) }}
                                            {{-- <span class="tax-inclusive"></span> --}}
                                        </td>
                                        <td class="text-right">
                                            @if($index === 0)
                                                {{ number_format($baseAmount, 2) }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="text-right">
                                            @if($index === 0)
                                                {{ number_format($baseAmount, 2) }}
                                            @else
                                                Included
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td>1</td>
                                    <td>
                                        General Automotive Service
                                        <span class="tax-inclusive">Tax Inclusive</span>
                                    </td>
                                    <td class="text-right">{{ number_format($baseAmount, 2) }}</td>
                                    <td class="text-right">{{ number_format($baseAmount, 2) }}</td>
                                </tr>
                            @endif
                            
                            <!-- Tax Breakdown -->
                            <tr>
                                <td colspan="2" rowspan="3" style="border: none;"></td>
                                <td class="text-right"><strong>Subtotal:</strong></td>
                                <td class="text-right"><strong>{{ number_format($baseAmount, 2) }}</strong></td>
                            </tr>
                            <tr>
                                <td class="text-right">CGST (9%):</td>
                                <td class="text-right">{{ number_format($cgst, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="text-right">SGST (9%):</td>
                                <td class="text-right">{{ number_format($sgst, 2) }}</td>
                            </tr>
                            <tr class="total-row">
                                <td colspan="2"></td>
                                <td class="text-right"><strong>GRAND TOTAL:</strong></td>
                                <td class="text-right"><strong>₹{{ number_format($totalAmount, 2) }}</strong></td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Amount in Words -->
                    <div class="amount-section">
                        <strong>AMOUNT IN WORDS:</strong><br>
                        <em>{{ \App\Helpers\NumberToWords::convert($totalAmount) ?? 'One Thousand Eight Hundred' }} RUPEES ONLY.</em>
                    </div>

                    <!-- Footer & Signatures -->
                    <div class="invoice-footer">
                        <div class="signature-area">
                            <div class="signature-box">
                                <div class="signature-line"></div>
                                <strong>CUSTOMER SIGNATURE</strong>
                            </div>
                            <div class="signature-box">
                                <div class="signature-line"></div>
                                <strong>AUTHORIZED SIGNATURE</strong><br>
                                <small>For AUTOCARE WORKSHOP</small>
                            </div>
                        </div>
                        
                        <div style="text-align: center; margin-top: 30px;">
                            <strong style="color: #2c3e50;">THANK YOU FOR YOUR BUSINESS!</strong><br>
                            <em style="color: #7f8c8d;">We appreciate your trust in our services</em>
                        </div>
                        
                        <div style="text-align: center; margin-top: 15px; font-size: 10px; color: #95a5a6;">
                            <strong>TERMS & CONDITIONS:</strong><br>
                            • This is computer generated invoice | • Warranty as per service terms
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer no-print">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" onclick="printInvoice()">Print Invoice</button>
            </div>
        </div>
    </div>
</div>

<script>
function printInvoice() {
    window.print();
}

// Add keyboard shortcut for printing
document.addEventListener('keydown', function(e) {
    if (e.ctrlKey && e.key === 'p') {
        e.preventDefault();
        printInvoice();
    }
});

// Auto-focus print button when modal opens
document.getElementById('invoiceModal').addEventListener('shown.bs.modal', function () {
    document.querySelector('.btn-print').focus();
});
</script>
@endsection