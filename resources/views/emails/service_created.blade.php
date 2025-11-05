<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Service Created - {{ $service->job_id }}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; }
        .header { background: #27ae60; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
        .content { padding: 20px; border: 1px solid #ddd; border-top: none; border-radius: 0 0 5px 5px; }
        .service-info { background: #e8f6f3; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .footer { margin-top: 20px; padding-top: 15px; border-top: 1px solid #eee; font-size: 12px; color: #666; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>New Service Created</h2>
            <p>Job ID: {{ $service->job_id }}</p>
        </div>
        
        <div class="content">
            <p>Dear {{ $service->vehicle->customer->name ?? $service->vehicle->owner->name }},</p>
            
            <p>Thank you for choosing Auto Service Center! We have successfully created a new service for your vehicle.</p>
            
            <div class="service-info">
                <h3>Service Details</h3>
                <p><strong>Vehicle:</strong> {{ $service->vehicle->registration_no }}</p>
                <p><strong>Model:</strong> {{ $service->vehicle->model }}</p>
                <p><strong>Scheduled Date:</strong> {{ \Carbon\Carbon::parse($service->service_start_datetime)->format('d/m/Y H:i') }}</p>
                <p><strong>Initial Status:</strong> <span style="color: #e67e22;">{{ ucfirst($service->status) }}</span></p>
                <p><strong>Estimated Amount:</strong> ₹{{ number_format($service->amount, 2) }}</p>
            </div>

            <p>We will keep you updated on the progress of your service. You will receive notifications when there are status updates.</p>
            
            <p>If you have any questions, please don't hesitate to contact us.</p>
            
            <p>Best regards,<br>Auto Service Center Team</p>
        </div>
        
        <div class="footer">
            <p>This is an automated confirmation email. Please do not reply to this message.</p>
        </div>
    </div>
</body>
</html>