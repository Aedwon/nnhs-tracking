<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminEscalationAlert extends Notification implements ShouldQueue
{
    use Queueable;

    public $teacher;
    public $lateCount;

    /**
     * Create a new notification instance.
     */
    public function __construct($teacher, $lateCount)
    {
        $this->teacher = $teacher;
        $this->lateCount = $lateCount;
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
            ->subject('Escalation: Repeated Late Submissions')
            ->line('Teacher ' . $this->teacher->name . ' has reached ' . $this->lateCount . ' late submissions.')
            ->line('Please review their status in the analytics dashboard.')
            ->action('View Analytics', url('/admin/dashboard'));
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'ESCALATION: ' . $this->teacher->name . ' has ' . $this->lateCount . ' late submissions.',
            'teacher_id' => $this->teacher->id,
            'late_count' => $this->lateCount,
        ];
    }
}
