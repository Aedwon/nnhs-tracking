<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\GradeUnlockRequest;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $pendingRequestsCount = GradeUnlockRequest::where('status', 'pending')->count();
        $heatmapData = cache()->remember('admin_heatmap', 30, function () {
            $sections = Section::with(['subjects.teacher'])->get();
            
            $submissionStatus = Grade::select('subject_id', 'section_id', DB::raw('MAX(CASE WHEN is_finalized = true THEN 2 WHEN grade IS NOT NULL THEN 1 ELSE 0 END) as status_code'))
                ->groupBy('subject_id', 'section_id')
                ->get()
                ->keyBy(fn($item) => $item->subject_id . '-' . $item->section_id);

            return $sections->map(function($section) use ($submissionStatus) {
                $subjects = $section->subjects->map(function($subject) use ($section, $submissionStatus) {
                    $key = $subject->id . '-' . $section->id;
                    $statusCode = $submissionStatus->get($key)->status_code ?? 0;

                    return [
                        'subject' => $subject->name,
                        'teacher' => $subject->teacher->name ?? 'UNCLAIMED',
                        'status' => $statusCode == 2 ? 'submitted' : ($statusCode == 1 ? 'progress' : 'pending')
                    ];
                });

                return [
                    'section' => $section->name,
                    'grade_level' => $section->grade_level,
                    'expected' => $section->expected_subjects_count,
                    'subjects' => $subjects
                ];
            });
        });

        return view('admin.dashboard', compact('heatmapData', 'pendingRequestsCount'));
    }

    public function unlockRequests()
    {
        $requests = GradeUnlockRequest::with(['teacher', 'subject', 'section'])
            ->orderBy('created_at', 'desc')
            ->get();
        return view('admin.unlock-requests', compact('requests'));
    }

    public function processUnlockRequest(Request $request, GradeUnlockRequest $unlockRequest)
    {
        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        $unlockRequest->update(['status' => $validated['status']]);

        if ($validated['status'] === 'approved') {
            Grade::where('teacher_id', $unlockRequest->teacher_id)
                ->where('subject_id', $unlockRequest->subject_id)
                ->where('section_id', $unlockRequest->section_id)
                ->update(['is_finalized' => false]);
            
            cache()->forget('teacher_dashboard_' . $unlockRequest->teacher_id);
            cache()->forget('admin_heatmap');
        }

        return back()->with('success', 'Unlock request processed.');
    }
}
