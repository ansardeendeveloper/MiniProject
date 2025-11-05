<?php

namespace App\Services;

use App\Models\ServiceRecord;
use App\Models\Vehicle;
use App\Models\Customer;
use App\Models\Owner;
use App\Notifications\MailNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;

class EmailService
{
    /**
     * Send service created notification to vehicle owner
     */
    public function sendServiceCreatedNotification(ServiceRecord $service): void
    {
        try {
            $vehicle = $service->vehicle;
            $recipient = $this->getRecipient($vehicle);
            
            if ($this->isValidEmail($recipient)) {
                Notification::route('mail', $recipient)
                    ->notify(new MailNotification($service, 'created'));
                
                Log::info("Service created notification sent to: {$recipient} for job: {$service->job_id}");
            } else {
                Log::warning("No valid email found for service creation - Job ID: {$service->job_id}");
            }
        } catch (\Exception $e) {
            Log::error('Failed to send service created notification: ' . $e->getMessage());
        }
    }

    /**
     * Send service status update notification
     */
    public function sendServiceStatusUpdate(ServiceRecord $service, string $oldStatus): void
    {
        try {
            $vehicle = $service->vehicle;
            $recipient = $this->getRecipient($vehicle);
            
            if ($this->isValidEmail($recipient)) {
                Notification::route('mail', $recipient)
                    ->notify(new MailNotification($service, 'status_updated', $oldStatus));
                
                Log::info("Service status update sent to: {$recipient} for job: {$service->job_id}");
            } else {
                Log::warning("No valid email found for status update - Job ID: {$service->job_id}");
            }
        } catch (\Exception $e) {
            Log::error('Failed to send status update notification: ' . $e->getMessage());
        }
    }

    /**
     * Send service completion notification with invoice
     */
    public function sendServiceInvoice(ServiceRecord $service): void
    {
        try {
            $vehicle = $service->vehicle;
            $recipient = $this->getRecipient($vehicle);
            
            if ($this->isValidEmail($recipient)) {
                Notification::route('mail', $recipient)
                    ->notify(new MailNotification($service, 'completed'));
                
                Log::info("Service completion notification sent to: {$recipient} for job: {$service->job_id}");
            } else {
                Log::warning("No valid email found for completion notification - Job ID: {$service->job_id}");
            }
        } catch (\Exception $e) {
            Log::error('Failed to send service completion notification: ' . $e->getMessage());
        }
    }

    /**
     * Get the appropriate recipient email for a vehicle
     */
    private function getRecipient(Vehicle $vehicle): ?string
    {
        try {
            // Load customer relationship if not loaded
            if (!$vehicle->relationLoaded('customer')) {
                $vehicle->load('customer');
            }

            // Priority 1: Customer email from vehicles table
            if ($vehicle->customer && !empty($vehicle->customer->email)) {
                return $vehicle->customer->email;
            }

            // Priority 2: Owner email from owners table (by vehicle registration)
            $owner = Owner::where('vehicle_number', $vehicle->registration_no)->first();
            if ($owner && !empty($owner->email)) {
                return $owner->email;
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Error getting recipient email: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Validate email address
     */
    private function isValidEmail(?string $email): bool
    {
        return $email && filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}