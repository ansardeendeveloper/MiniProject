<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice - {{ $service->job_id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; line-height: 1.6; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 20px; }
        .company-name { font-size: 24px; font-weight: bold; margin-bottom: 10px; }
        .invoice-details { margin: 20px 0; }
        .detail-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .detail-table th, .detail-table td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        .detail-table th { background-color: #f8f9fa; }
        .service-types { margin: 15px 0; }
        .total-amount { font-size: 18px; font-weight: bold; text-align: right; margin-top: 20px; }
        .footer { margin-top: 50px; padding-top: 20px; border-top: 1px solid #ddd; text-align: center; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">Auto Service Center</div>
        <div>Service Invoice</div>
        <div>Job ID: {{ $service->job_id }}</div>
        <div>Date: {{ \Carbon\Carbon::now()->format('d/m/Y') }}</div>
    </div>

    <div class="invoice-details">
        <table class="detail-table">
            <tr>
                <th colspan="2" style="background-color: #2c3e50; color: white;">Customer & Vehicle Information</th>
            </tr>
            <tr>
                <td><strong>Customer Name:</strong></td>
                <td>{{ $service->vehicle->customer->name ?? $service->vehicle->owner->name }}</td>
            </tr>
            <tr>
                <td><strong>Email:</strong></td>
                <td>{{ $service->vehicle->customer->email ?? $service->vehicle->owner->email }}</td>
            </tr>
            <tr>
                <td><strong>Phone:</strong></td>
                <td>{{ $service->vehicle->customer->mobile_number ?? $service->vehicle->owner->phone }}</td>
            </tr>
            <tr>
                <td><strong>Vehicle Number:</strong></td>
                <td>{{ $service->vehicle->registration_no }}</td>
            </tr>
            <tr>
                <td><strong>Vehicle Model:</strong></td>
                <td>{{ $service->vehicle->model }}</td>
            </tr>
            <tr>
                <td><strong>Manufacturer:</strong></td>
                <td>{{ $service->vehicle->manufacturer }}</td>
            </tr>
            <tr>
                <td><strong>Year:</strong></td>
                <td>{{ $service->vehicle->year }}</td>
            </tr>
        </table>

        <table class="detail-table">
            <tr>
                <th colspan="2" style="background-color: #2c3e50; color: white;">Service Information</th>
            </tr>
            <tr>
                <td><strong>Service Start:</strong></td>
                <td>{{ \Carbon\Carbon::parse($service->service_start_datetime)->format('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <td><strong>Service End:</strong></td>
                <td>{{ $service->service_end_datetime ? \Carbon\Carbon::parse($service->service_end_datetime)->format('d/m/Y H:i') : 'N/A' }}</td>
            </tr>
            <tr>
                <td><strong>Status:</strong></td>
                <td>{{ ucfirst($service->status) }}</td>
            </tr>
        </table>

        <div class="service-types">
            <h3>Service Types Performed:</h3>
            <ul>
                @foreach($service->service_types_array as $serviceType)
                    <li>{{ $serviceType }}</li>
                @endforeach
            </ul>
        </div>

        <div class="total-amount">
            Total Amount: ₹{{ number_format($service->amount, 2) }}
        </div>
    </div>

    <div class="footer">
        <p>Thank you for choosing Auto Service Center</p>
        <p>This is a computer-generated invoice. No signature required.</p>
        <p>For any queries, please contact our customer service.</p>
    </div>
</body>
</html>