<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>INVOICE - {{ $service->job_id ?? 'N/A' }}</title>
    <style>
        /* A4 Page Settings */
        @page {
            size: A4;
            margin: 0;
        }
        
        body {
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            background: #fff;
        }
        
        .a4-container {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 15mm;
            box-sizing: border-box;
            background: white;
            position: relative;
        }
        
        /* Header Styles */
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
        
        .company-tagline {
            font-size: 14px;
            color: #7f8c8d;
            margin: 5px 0;
        }
        
        .invoice-title {
            font-size: 32px;
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
            color: #2c3e50;
            text-transform: uppercase;
        }
        
        /* Two Column Layout */
        .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }
        
        .col {
            flex: 1;
        }
        
        .col-2 {
            flex: 2;
        }
        
        /* Table Styles */
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
        
        /* Footer Styles */
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
        
        /* Status Badges */
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
        
        .status-pending {
            background: #f39c12;
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
        
        /* Print Styles */
        @media print {
            body {
                margin: 0;
                padding: 0;
                background: white;
            }
            
            .a4-container {
                width: 210mm;
                min-height: 297mm;
                margin: 0;
                padding: 15mm;
                box-shadow: none;
                border: none;
            }
            
            .no-print {
                display: none !important;
            }
            
            /* Ensure backgrounds print */
            .invoice-table th {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .total-row {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
        
        /* Screen Styles */
        @media screen {
            body {
                background: #ecf0f1;
                padding: 20px;
            }
            
            .a4-container {
                background: white;
                box-shadow: 0 0 20px rgba(0,0,0,0.1);
                margin: 0 auto;
            }
            
            .no-print {
                text-align: center;
                margin-bottom: 20px;
            }
            
            .print-btn {
                background: #3498db;
                color: white;
                border: none;
                padding: 10px 20px;
                border-radius: 5px;
                cursor: pointer;
                font-size: 16px;
                margin: 5px;
            }
            
            .print-btn:hover {
                background: #2980b9;
            }
        }
    </style>
</head>
<body>

    <!-- A4 Invoice Container -->
    <div class="a4-container">
        <!-- Invoice Header -->
        <div class="invoice-header">
            <div class="row">
                <div class="col">
                    <h1 class="company-name">AUTOCARE WORKSHOP</h1>
                    <p>
                        NO 7 Villupuram Main Rd, Moolakulam<br>
                        Puducherry - 605010<br>
                        📞 +91 9842896939 | ✉️ info@autocare.com<br>
                        GSTIN: 34BBLPS260A1ZO
                    </p>
                </div>
                <div class="col text-right">
                    <h2 class="invoice-title">TAX INVOICE</h2>
                    <p>
                        <strong>Invoice No:</strong> {{ strtoupper($service->job_id ?? 'SR-' . $service->id) }}<br>
                        <strong>Date:</strong> {{ now('Asia/Kolkata')->format('d/m/Y') }}<br>
                        <strong>Time:</strong> {{ now('Asia/Kolkata')->format('H:i') }}<br>
                        <strong>Status:</strong> 
                        <span class="status-badge {{ $service->status === 'completed' ? 'status-completed' : 'status-pending' }}">
                            {{ strtoupper($service->status) }}
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Customer & Vehicle Information -->
        <div class="row">
            <div class="col">
                <h3 style="color: #2c3e50; border-bottom: 1px solid #bdc3c7; padding-bottom: 5px;">BILLED TO</h3>
                <p>
                    <strong>Name:</strong> {{ strtoupper(optional(optional($service->vehicle)->customer)->name ?? 'N/A') }}<br>
                    <strong>Mobile:</strong> {{ optional(optional($service->vehicle)->customer)->mobile_number ?? 'N/A' }}<br>
                </p>
            </div>
            <div class="col">
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
        <div class="row">
            <div class="col">
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
                    $totalAmount = 1800.00; // Fixed amount including all taxes
                    
                    // Calculate tax breakdown (for information only)
                    $baseAmount = round($totalAmount / 1.18, 2); // Assuming 18% GST
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
                • This is computer generated invoice | • Warranty as per service terms | • Subject to Puducherry jurisdiction
            </div>
        </div>
    </div>

    <script>
        // Auto-print when page loads (optional - uncomment if needed)
        // window.onload = function() {
        //     window.print();
        // };
        
        // Add keyboard shortcut for printing
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'p') {
                e.preventDefault();
                window.print();
            }
        });
    </script>
</body>
</html>