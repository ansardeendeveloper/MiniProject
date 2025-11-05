<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Service Status Update - {{ $service->job_id }}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; }
        .header { background: #3498db; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
        .content { padding: 20px; border: 1px solid #ddd; border-top: none; border-radius: 0 0 5px 5px; }
        .status-update { background: #e8f4fd; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .footer { margin-top: 20px; padding-top: 15px; border-top: 1px solid #eee; font-size: 12px; color: #666; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Service Status Update</h2>
            <p>Job ID: {{ $service->job_id }}</p>
        </div>
        
        <div class="content">
            <p>Dear {{ $service->vehicle->customer->name ?? $service->vehicle->owner->name }},</p>
            
            <p>We wanted to inform you about an update to your vehicle service status.</p>
            
            <div class="status-update">
                <h3>Status Update</h3>
                <p><strong>Vehicle:</strong> {{ $service->vehicle->registration_no }}</p>
                <p><strong>Previous Status:</strong> {{ ucfirst($oldStatus ?? 'N/A') }}</p>
                <p><strong>Current Status:</strong> <span style="color: #e67e22; font-weight: bold;">{{ ucfirst($service->status) }}</span></p>
                
                @if($service->status === 'completed')
                <p><strong>Completed On:</strong> {{ \Carbon\Carbon::parse($service->service_end_datetime)->format('d/m/Y H:i') }}</p>
                <p><strong>Total Amount:</strong> ₹{{ number_format($service->amount, 2) }}</p>
                @endif
            </div>

            @if($service->status === 'in_progress')
            <p>Your vehicle is currently being serviced. We'll notify you once the service is completed.</p>
            @elseif($service->status === 'completed')
            <p>Your vehicle service has been completed successfully. Please find the detailed invoice attached.</p>
            @elseif($service->status === 'cancelled')
            <p>Your service request has been cancelled. If this was unexpected, please contact our customer service.</p>
            @endif

            <p>Thank you for your patience and for choosing <strong>Auto Service Center</strong>.</p>
        </div>
        
        <div class="footer">
            <p>Auto Service Center</p>
            <p>This is an automated notification. Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>