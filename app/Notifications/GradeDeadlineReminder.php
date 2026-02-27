<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GradeDeadlineReminder extends Notification implements ShouldQueue
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
            ->subject('Deadline Reminder: Grade Submission')
            ->line('Your grade submission deadline is approaching.')
            ->line('Deadline: ' . $this->deadline->deadline_at->format('M d, Y h:i A'))
            ->action('Login to Upload Grades', url('/login'))
            ->line('Please ensure all grades are uploaded before the deadline.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Your grade submission deadline is approaching: ' . $this->deadline->deadline_at->format('M d, Y h:i A'),
            'deadline_id' => $this->deadline->id,
        ];
    }

    /**
     * Placeholder for SMS notification.
     */
    public function toSms($notifiable)
    {
        $message = "SMGS: Reminder! Your grade submission deadline is " . $this->deadline->deadline_at->format('M d, Y h:i A');
        \Illuminate\Support\Facades\Log::info("SMS SENT TO " . $notifiable->phone_number . ": " . $message);

        // In production, integrate with Twilio/Nexmo here
        \App\Services\AuditLogger::log('SMS Dispatch', $this->deadline->id, ['phone' => $notifiable->phone_number, 'type' => 'Reminder']);
    }
}
