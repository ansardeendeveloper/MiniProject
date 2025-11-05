<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Invoice - {{ $service->job_id }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { font-size: 12px; }
            .container { max-width: 100% !important; }
        }
        .invoice-header { border-bottom: 2px solid #333; padding-bottom: 20px; margin-bottom: 20px; }
        .invoice-table th { background-color: #f8f9fa; }
    </style>
</head>
<body>
    <div class="container mt-4 mb-4">
        <!-- Print Button (Hidden when printing) -->
        <div class="text-end mb-3 no-print">
            <button class="btn btn-primary" onclick="window.print()">
                <i class="bi bi-printer"></i> Print
            </button>
            <button class="btn btn-secondary" onclick="window.close()">
                <i class="bi bi-x-circle"></i> Close
            </button>
        </div>

        <!-- Invoice Header -->
        <div class="row invoice-header">
            <div class="col-6">
                <h1 class="h3 fw-bold">SERVICE INVOICE</h1>
                <p class="mb-1"><strong>Job ID:</strong> {{ $service->job_id }}</p>
                <p class="mb-1"><strong>Invoice Date:</strong> {{ \Carbon\Carbon::now()->format('d-m-Y') }}</p>
            </div>
            <div class="col-6 text-end">
                <h2 class="h4">Auto Service Center</h2>
                <p class="mb-1">123 Service Road</p>
                <p class="mb-1">Chennai, Tamil Nadu - 600001</p>
                <p class="mb-0">Phone: +91 44 1234 5678</p>
            </div>
        </div>

        <!-- Vehicle & Customer Details -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">Vehicle Details</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-1"><strong>Registration No:</strong> {{ $service->vehicle->registration_no ?? 'N/A' }}</p>
                        <p class="mb-1"><strong>Model:</strong> {{ $service->vehicle->model ?? 'N/A' }}</p>
                        <p class="mb-1"><strong>Make:</strong> {{ $service->vehicle->make ?? 'N/A' }}</p>
                        <p class="mb-0"><strong>Year:</strong> {{ $service->vehicle->year ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">Service Details</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-1"><strong>Service Date:</strong> 
                            @if($service->service_start_datetime)
                                {{ \Carbon\Carbon::parse($service->service_start_datetime)->format('d-m-Y g:i A') }}
                            @else
                                -
                            @endif
                        </p>
                        <p class="mb-1"><strong>Status:</strong> 
                            <span class="badge bg-{{ $service->status == 'completed' ? 'success' : ($service->status == 'pending' ? 'warning' : 'danger') }}">
                                {{ ucfirst($service->status) }}
                            </span>
                        </p>
                        <p class="mb-0"><strong>Service Type:</strong> {{ $service->service_name ?? 'General Service' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Service Description -->
        @if($service->description)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">Service Description</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{{ $service->description }}</p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Amount Section -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">Payment Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <p class="mb-2"><strong>Subtotal:</strong></p>
                                <p class="mb-2"><strong>Tax (18%):</strong></p>
                                <p class="mb-0"><strong>Total Amount:</strong></p>
                            </div>
                            <div class="col-6 text-end">
                                @php
                                    $subtotal = $service->amount;
                                    $tax = $service->amount * 0.18;
                                    $total = $subtotal + $tax;
                                @endphp
                                <p class="mb-2">₹{{ number_format($subtotal, 2) }}</p>
                                <p class="mb-2">₹{{ number_format($tax, 2) }}</p>
                                <p class="mb-0 fs-5 fw-bold text-primary">₹{{ number_format($total, 2) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="row mt-5">
            <div class="col-12 text-center">
                <p class="text-muted mb-2">Thank you for choosing our service!</p>
                <p class="text-muted small">For any queries, contact: support@autoservice.com | Phone: +91 44 1234 5678</p>
            </div>
        </div>
    </div>

    <script>
        // Auto-print when page loads (optional)
        // window.onload = function() {
        //     window.print();
        // }
    </script>
</body>
</html>