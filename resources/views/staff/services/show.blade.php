@extends('layouts.stafftemplates')

@section('title', 'Service Details')

@section('head')
<style>
    .material-icons {
        vertical-align: middle;
        font-size: 18px;
        margin-right: 4px;
    }

    /* Hide invoice in normal screen */
    #invoice-section { display: none; }

    /* Print view - show only invoice */
    @media print {
        body * {
            visibility: hidden !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        #invoice-section, #invoice-section * {
            visibility: visible !important;
        }
        #invoice-section {
            display: block !important;
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            margin: 0;
            padding: 15px;
            font-family: Arial, sans-serif;
            font-size: 12px;
            background: white;
        }
        .no-print { display: none !important; }
        
        /* Professional Invoice Styles */
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            border: 2px solid #333;
            padding: 20px;
            background: white;
        }
        .invoice-header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .tax-invoice-title {
            font-size: 28px;
            font-weight: bold;
            margin: 10px 0;
            color: #333;
        }
        .company-details {
            margin-bottom: 20px;
        }
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        .invoice-table th, .invoice-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        .invoice-table th {
            background: #f0f0f0 !important;
            -webkit-print-color-adjust: exact;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-row {
            font-weight: bold;
            background: #f8f9fa !important;
            -webkit-print-color-adjust: exact;
        }
        .invoice-footer {
            margin-top: 30px;
            border-top: 1px solid #333;
            padding-top: 15px;
        }
        .signature-area {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
    }

    .service-card { border-left: 4px solid #007bff; }
    .info-row {
        margin-bottom: 0.5rem;
        padding: 0.5rem 0;
        border-bottom: 1px solid #f0f0f0;
    }
    .info-row:last-child { border-bottom: none; }
</style>
@endsection

@section('content')
<div class="container my-4">
    <div class="card shadow service-card no-print">
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
                        <button onclick="printInvoice()" class="btn btn-success">Print Invoice</button>
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
{{-- invoice --}}
    <div id="invoice-section">
        <div class="invoice-container">
            <div class="invoice-header">
                <h2 style="margin:0; font-size: 24px;">AUTOCARE WORKSHOP</h2>
                <p style="margin:5px 0;">NO 7 Villupuram Main Rd, Moolakulam, Puducherry - 605010</p>
                <p style="margin:5px 0;">Phone: +91 9842896939 | Email: info@autocare.com</p>
                <p style="margin:5px 0;">GSTIN: 34BBLPS260A1ZO</p>
                <div class="tax-invoice-title">TAX INVOICE</div>
            </div>

            <div class="row company-details">
                <div class="col-6">
                    <strong>Billed From:</strong><br>
                    AUTOCARE WORKSHOP<br>
                    NO 7 Villupuram Main Rd<br>
                    Moolakulam, Puducherry - 605010<br>
                    GSTIN: 34BBLPS260A1ZO<br>
                    Phone: +91 9842896939
                </div>
                <div class="col-6 text-end">
                    <strong>Billed To:</strong><br>
                    <strong>INVOICE NO:</strong> {{ strtoupper($service->job_id) }}<br>
                    NAME: {{ strtoupper(optional(optional($service->vehicle)->customer)->name ?? 'N/A') }}<br>
                    MOBILE: {{ optional(optional($service->vehicle)->customer)->mobile_number ?? 'N/A' }}<br>
                    VEHICLE: {{ strtoupper($service->vehicle->registration_no ?? 'N/A') }}<br>
                    @if($service->vehicle && $service->vehicle->model)
                        MODEL: {{ strtoupper($service->vehicle->model) }}<br>
                    @endif
                    <strong>DATE:</strong> {{ now('Asia/Kolkata')->format('d-m-Y H:i') }}
                </div>
            </div>

            <table class="invoice-table">
                <thead>
                    <tr>
                        <th width="5%">S.NO</th>
                        <th width="45%">DESCRIPTION OF SERVICES</th>
                        <th width="10%" class="text-right">RATE (₹)</th>
                        <th width="10%" class="text-right">CGST 9%</th>
                        <th width="10%" class="text-right">SGST 9%</th>
                        <th width="10%" class="text-right">TOTAL (₹)</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $prices = [
                            'General Service' => 1800, 'Light Change' => 400, 'Spark plug replacement' => 550,
                            'Suspension check' => 880, 'Cooling system check' => 650, 'Oil Change' => 500,
                            'Tire Replacement' => 3000, 'Brake Repair' => 1500, 'Engine Tune-up' => 4000,
                            'Battery Replacement' => 4500
                        ];
                        $subtotal = 0;
                    @endphp
                    
                    @foreach($types as $i => $t)
                        @php
                            $rate = $prices[$t] ?? 0;
                            $cg = round($rate * 0.09, 2);
                            $sg = round($rate * 0.09, 2);
                            $tot = $rate + $cg + $sg;
                            $subtotal += $tot;
                        @endphp
                        <tr>
                            <td>{{ $i+1 }}</td>
                            <td>{{ strtoupper($t) }}</td>
                            <td class="text-right">{{ number_format($rate,2) }}</td>
                            <td class="text-right">{{ number_format($cg,2) }}</td>
                            <td class="text-right">{{ number_format($sg,2) }}</td>
                            <td class="text-right">{{ number_format($tot,2) }}</td>
                        </tr>
                    @endforeach
                    
                    <tr class="total-row">
                        <td colspan="5" class="text-right"><strong>GRAND TOTAL</strong></td>
                        <td class="text-right"><strong>₹{{ number_format($service->amount ?? 0, 2) }}</strong></td>
                    </tr>
                </tbody>
            </table>

            <div style="margin-top: 15px;">
                <strong>AMOUNT IN WORDS:</strong> 
                {{ strtoupper(\App\Helpers\NumberToWords::convert($service->amount ?? 0) ?? 'Zero') }} RUPEES ONLY.
            </div>

            <div class="invoice-footer">
                <div class="signature-area">
                    <div>
                        <br><br><strong><br><br>CUSTOMER SIGNATURE</strong><br>
                        <div style="margin-top: 40px; border-top: 1px solid #000; width: 200px;"></div>
                    </div>
                    <div>
                        <br><br><strong><br><br>AUTHORIZED SIGNATURE</strong><br>
                        <div style="margin-top: 40px; border-top: 1px solid #000; width: 200px;"></div>
                    </div>
                </div>
                
                <div class="text-center" style="margin-top: 30px;">
                    <strong>THANK YOU FOR YOUR BUSINESS!</strong><br>
                    <em>AUTOCARE WORKSHOP - QUALITY SERVICE GUARANTEED</em>
                </div>
                
                <div class="text-center" style="margin-top: 10px; font-size: 10px;">
                    THIS IS A COMPUTER GENERATED INVOICE AND DOES NOT REQUIRE A SIGNATURE.
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function printInvoice() { 
        window.print(); 
    }
</script>
@endsection
