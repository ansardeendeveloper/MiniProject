<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\ServiceRecord;

class MailNotification extends Notification
{
    use Queueable;

    public $service;
    public $type;
    public $oldStatus;

    /**
     * Create a new notification instance.
     */
    public function __construct(ServiceRecord $service, $type = 'created', $oldStatus = null)
    {
        $this->service = $service;
        $this->type = $type;
        $this->oldStatus = $oldStatus;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $vehicle = $this->service->vehicle;
        $customerName = 'Valued Customer';
        
        if ($vehicle->customer) {
            $customerName = $vehicle->customer->name ?? 'Valued Customer';
        }

        $mailMessage = (new MailMessage)
            ->subject($this->getSubject())
            ->greeting("Hello {$customerName}!");

        return $this->buildMessage($mailMessage);
    }

    private function getSubject(): string
    {
        switch ($this->type) {
            case 'created':
                return 'Service Created - ' . ($this->service->job_id ?? 'New Service');
            case 'status_updated':
                return 'Service Status Updated - ' . ($this->service->job_id ?? 'Service Update');
            case 'completed':
                return 'Service Completed - Invoice Available';
            default:
                return 'Service Notification';
        }
    }

    private function buildMessage(MailMessage $mailMessage): MailMessage
    {
        switch ($this->type) {
            case 'created':
                return $this->buildCreatedMessage($mailMessage);
            case 'status_updated':
                return $this->buildStatusUpdateMessage($mailMessage);
            case 'completed':
                return $this->buildCompletedMessage($mailMessage);
            default:
                return $this->buildDefaultMessage($mailMessage);
        }
    }

    private function buildCreatedMessage(MailMessage $mailMessage): MailMessage
    {
        return $mailMessage
            ->line('Your vehicle service has been successfully created.')
            ->line('**Service Details:**')
            ->line('- Job ID: ' . ($this->service->job_id ?? 'N/A'))
            ->line('- Vehicle: ' . ($this->service->vehicle->registration_no ?? 'N/A'))
            ->line('- Service Types: ' . $this->getServiceTypes())
            ->line('- Scheduled Date: ' . $this->formatDate($this->service->service_start_datetime))
            ->line('We will keep you updated on the progress of your service.')
            ->line('Thank you for choosing our service!');
    }

    private function buildStatusUpdateMessage(MailMessage $mailMessage): MailMessage
    {
        return $mailMessage
            ->line('Your vehicle service status has been updated.')
            ->line('**Service Details:**')
            ->line('- Job ID: ' . ($this->service->job_id ?? 'N/A'))
            ->line('- Vehicle: ' . ($this->service->vehicle->registration_no ?? 'N/A'))
            ->line('- Previous Status: ' . ucfirst($this->oldStatus ?? 'N/A'))
            ->line('- Current Status: ' . ucfirst($this->service->status))
            ->line('- Service Types: ' . $this->getServiceTypes())
            ->line('We will notify you when there are further updates.');
    }

    private function buildCompletedMessage(MailMessage $mailMessage): MailMessage
    {
        return $mailMessage
            ->line('Your vehicle service has been completed!')
            ->line('**Service Details:**')
            ->line('- Job ID: ' . ($this->service->job_id ?? 'N/A'))
            ->line('- Vehicle: ' . ($this->service->vehicle->registration_no ?? 'N/A'))
            ->line('- Total Amount: ₹' . number_format($this->service->amount, 2))
            ->line('- Completed Date: ' . $this->formatDate($this->service->service_end_datetime))
            ->line('Thank you for your business! We look forward to serving you again.');
    }

    private function buildDefaultMessage(MailMessage $mailMessage): MailMessage
    {
        return $mailMessage
            ->line('There is an update regarding your vehicle service.')
            ->line('- Job ID: ' . ($this->service->job_id ?? 'N/A'))
            ->line('- Status: ' . ucfirst($this->service->status))
            ->line('Thank you for choosing our service!');
    }

    private function getServiceTypes(): string
    {
        $types = json_decode($this->service->service_types, true) ?: [];
        return !empty($types) ? implode(', ', $types) : 'Not specified';
    }

    private function formatDate($date): string
    {
        return $date ? \Carbon\Carbon::parse($date)->format('M j, Y g:i A') : 'Not scheduled';
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'service_id' => $this->service->id,
            'type' => $this->type,
            'job_id' => $this->service->job_id,
            'status' => $this->service->status,
        ];
    }
}