<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Service Invoice - {{ $service->job_id }}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; }
        .header { background: #2c3e50; color: white; padding: 30px; text-align: center; border-radius: 5px 5px 0 0; }
        .content { padding: 30px; border: 1px solid #ddd; border-top: none; border-radius: 0 0 5px 5px; }
        .footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; font-size: 12px; color: #666; text-align: center; }
        .service-details { background: #f8f9fa; padding: 20px; border-radius: 5px; margin: 20px 0; }
        .detail-row { display: flex; justify-content: space-between; margin-bottom: 10px; }
        .detail-label { font-weight: bold; color: #2c3e50; }
        .service-types { margin: 15px 0; }
        .service-type-item { padding: 8px; background: #e8f4fd; margin: 5px 0; border-radius: 3px; }
        .amount { font-size: 18px; font-weight: bold; color: #27ae60; text-align: center; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Auto Service Center</h1>
            <h2>Service Invoice</h2>
            <p>Job ID: <strong>{{ $service->job_id }}</strong></p>
        </div>
        
        <div class="content">
            <p>Dear {{ $service->vehicle->customer->name ?? $service->vehicle->owner->name }},</p>
            
            <p>Your vehicle service has been completed successfully. Here are the details:</p>
            
            <div class="service-details">
                <h3>Vehicle Information</h3>
                <div class="detail-row">
                    <span class="detail-label">Vehicle Number:</span>
                    <span>{{ $service->vehicle->registration_no }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Model:</span>
                    <span>{{ $service->vehicle->model }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Manufacturer:</span>
                    <span>{{ $service->vehicle->manufacturer }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Year:</span>
                    <span>{{ $service->vehicle->year }}</span>
                </div>
                
                <h3>Service Information</h3>
                <div class="detail-row">
                    <span class="detail-label">Service Start:</span>
                    <span>{{ \Carbon\Carbon::parse($service->service_start_datetime)->format('d/m/Y H:i') }}</span>
                </div>
                @if($service->service_end_datetime)
                <div class="detail-row">
                    <span class="detail-label">Service End:</span>
                    <span>{{ \Carbon\Carbon::parse($service->service_end_datetime)->format('d/m/Y H:i') }}</span>
                </div>
                @endif
                <div class="detail-row">
                    <span class="detail-label">Status:</span>
                    <span style="color: #27ae60; font-weight: bold;">{{ ucfirst($service->status) }}</span>
                </div>
                
                <div class="service-types">
                    <h4>Service Types:</h4>
                    @php
                        $serviceTypes = json_decode($service->service_types, true) ?: [];
                    @endphp
                    @foreach($serviceTypes as $serviceType)
                        <div class="service-type-item">{{ $serviceType }}</div>
                    @endforeach
                </div>
                
                <div class="amount">
                    Total Amount: ₹{{ number_format($service->amount, 2) }}
                </div>
            </div>
            
            <p>A detailed PDF invoice is attached to this email for your records.</p>
            
            <p>Thank you for choosing <strong>Auto Service Center</strong> for your vehicle maintenance needs!</p>
            
            <p>Best regards,<br>Auto Service Center Team</p>
        </div>
        
        <div class="footer">
            <p>This is an automated email. Please do not reply to this message.</p>
            <p>If you have any questions, please contact our customer service.</p>
            <p>&copy; {{ date('Y') }} Auto Service Center. All rights reserved.</p>
        </div>
    </div>
</body>
</html>