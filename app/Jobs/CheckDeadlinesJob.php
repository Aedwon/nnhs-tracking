<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CheckDeadlinesJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $now = now();

        // 1. Process Reminders (Approaching deadlines)
        $approachingDeadlines = \App\Models\Deadline::where('deadline_at', '>', $now)
            ->where('deadline_at', '<=', $now->copy()->addHours(24)) // Hardcoded 24 for now, or use column
            ->get();

        foreach ($approachingDeadlines as $deadline) {
            $teachers = \App\Models\User::role('Teacher')->get();
            foreach ($teachers as $teacher) {
                // Check if grades already uploaded for this period
                $hasUploaded = \App\Models\Grade::where('teacher_id', $teacher->id)
                    ->where('grading_period_id', $deadline->grading_period_id)
                    ->exists();

                if (!$hasUploaded) {
                    $teacher->notify(new \App\Notifications\GradeDeadlineReminder($deadline));
                }
            }
        }

        // 2. Process Late Alerts (Just passed deadlines)
        $justPassedDeadlines = \App\Models\Deadline::where('deadline_at', '<=', $now)
            ->where('deadline_at', '>=', $now->copy()->subMinutes(60)) // Run every hour
            ->get();

        foreach ($justPassedDeadlines as $deadline) {
            $teachers = \App\Models\User::role('Teacher')->get();
            foreach ($teachers as $teacher) {
                $hasUploaded = \App\Models\Grade::where('teacher_id', $teacher->id)
                    ->where('grading_period_id', $deadline->grading_period_id)
                    ->exists();

                if (!$hasUploaded) {
                    $teacher->notify(new \App\Notifications\LateSubmissionAlert($deadline));

                    // Increment late counter or log for escalation
                    \App\Services\AuditLogger::log('Late Submission Flagged', $teacher->id, ['period' => $deadline->grading_period_id]);

                    // Escalation check
                    $lateCount = \App\Models\AuditLog::where('user_id', $teacher->id)
                        ->where('action', 'Late Submission Flagged')
                        ->count();

                    if ($lateCount >= 3) {
                        $admins = \App\Models\User::role('Admin')->get();
                        foreach ($admins as $admin) {
                            $admin->notify(new \App\Notifications\AdminEscalationAlert($teacher, $lateCount));
                        }
                    }
                }
            }
        }
    }
}
