<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LateSubmissionAlert extends Notification implements ShouldQueue
{
    use Queueable;

    public $deadline;

    /**
     * Create a new notification instance.
     */
    public function __construct($deadline)
    {
        $this->deadline = $deadline;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->error()
            ->subject('URGENT: Late Grade Submission')
            ->line('The deadline for grade submission has passed, and your grades have not been uploaded.')
            ->line('Deadline was: ' . $this->deadline->deadline_at->format('M d, Y h:i A'))
            ->action('Upload Now', url('/login'))
            ->line('Late submissions are logged and may be escalated to the administration.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'LATE SUBMISSION: Deadline was ' . $this->deadline->deadline_at->format('M d, Y h:i A'),
            'deadline_id' => $this->deadline->id,
            'is_late' => true,
        ];
    }

    /**
     * Placeholder for SMS notification.
     */
    public function toSms($notifiable)
    {
        $message = "SMGS: URGENT! Your grade submission is LATE. Deadline was " . $this->deadline->deadline_at->format('M d, Y h:i A');
        \Illuminate\Support\Facades\Log::info("SMS SENT TO " . $notifiable->phone_number . ": " . $message);

        \App\Services\AuditLogger::log('SMS Dispatch (Late)', $this->deadline->id, ['phone' => $notifiable->phone_number, 'type' => 'Late Alert']);
    }
}
