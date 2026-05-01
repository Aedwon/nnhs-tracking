<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $pendingRequestsCount = \App\Models\GradeUnlockRequest::where('status', 'pending')->count();
        $heatmapData = cache()->remember('admin_heatmap', 30, function () {
            // ... existing heatmap logic ...
            $sections = \App\Models\Section::with(['subjectTeacherSections.subject', 'subjectTeacherSections.teacher'])->get();
            
            $submissionStatus = \App\Models\Grade::select('subject_id', 'section_id', \Illuminate\Support\Facades\DB::raw('MAX(CASE WHEN is_finalized = true THEN 2 WHEN grade IS NOT NULL THEN 1 ELSE 0 END) as status_code'))
                ->groupBy('subject_id', 'section_id')
                ->get()
                ->keyBy(fn($item) => $item->subject_id . '-' . $item->section_id);

            return $sections->map(function($section) use ($submissionStatus) {
                $subjects = $section->subjectTeacherSections->map(function($sts) use ($section, $submissionStatus) {
                    $key = $sts->subject_id . '-' . $section->id;
                    $statusCode = $submissionStatus->get($key)->status_code ?? 0;

                    return [
                        'subject' => $sts->subject->name,
                        'teacher' => $sts->teacher->name,
                        'status' => $statusCode == 2 ? 'submitted' : ($statusCode == 1 ? 'progress' : 'pending')
                    ];
                });

                return [
                    'section' => $section->name,
                    'grade_level' => $section->grade_level,
                    'subjects' => $subjects
                ];
            });
        });

        return view('admin.dashboard', compact('heatmapData', 'pendingRequestsCount'));
    }

    public function unlockRequests()
    {
        $requests = \App\Models\GradeUnlockRequest::with(['teacher', 'subject', 'section'])
            ->orderBy('created_at', 'desc')
            ->get();
        return view('admin.unlock-requests', compact('requests'));
    }

    public function processUnlockRequest(Request $request, \App\Models\GradeUnlockRequest $unlockRequest)
    {
        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        $unlockRequest->update(['status' => $validated['status']]);

        if ($validated['status'] === 'approved') {
            // Unlock the grades
            \App\Models\Grade::where('teacher_id', $unlockRequest->teacher_id)
                ->where('subject_id', $unlockRequest->subject_id)
                ->where('section_id', $unlockRequest->section_id)
                ->update(['is_finalized' => false]);
            
            // Bust caches
            cache()->forget('teacher_dashboard_' . $unlockRequest->teacher_id);
            cache()->forget('admin_heatmap');
        }

        return back()->with('success', 'Unlock request ' . $validated['status'] . '.');
    }
}
