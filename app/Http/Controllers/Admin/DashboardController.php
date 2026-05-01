<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $heatmapData = cache()->remember('admin_heatmap', 30, function () {
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

        return view('admin.dashboard', compact('heatmapData'));
    }
}
