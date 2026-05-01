<?php

namespace App\Services;

use App\Models\Grade;
use Illuminate\Support\Facades\Auth;

class SubmissionService
{
    /**
     * Finalize the grades for a specific teacher, subject, and section.
     */
    public function finalizeGrades($teacherId, $subjectId, $sectionId)
    {
        $grades = Grade::where([
            'teacher_id' => $teacherId,
            'subject_id' => $subjectId,
            'section_id' => $sectionId,
        ])->get();

        foreach ($grades as $grade) {
            $grade->update([
                'is_finalized' => true,
                'submitted_at' => now(),
            ]);
        }

        AuditLogger::log('Finalized Grades', null, [
            'subject_id' => $subjectId,
            'section_id' => $sectionId,
            'count' => $grades->count()
        ]);
    }

    /**
     * Request an un-submission for corrections.
     * This action is immediately logged with the justification.
     */
    public function requestUnsubmit(Grade $grade, string $justification)
    {
        $grade->update([
            'is_finalized' => false,
            'justification' => $justification
        ]);

        AuditLogger::log('Un-submitted Grade', $grade->id, [
            'justification' => $justification,
            'student_id' => $grade->student_id,
            'subject_id' => $grade->subject_id
        ]);
    }
}
