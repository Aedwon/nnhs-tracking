<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        $cacheKey = 'teacher_dashboard_' . $user->id;

        $data = cache()->remember($cacheKey, 30, function () use ($user) {
            // 1. Subjects to Grade (as Teacher)
            $subjectsToGrade = \App\Models\SubjectTeacherSection::with(['subject', 'section'])
                ->where('teacher_id', $user->id)
                ->get();

            $gradeCounts = \App\Models\Grade::where('teacher_id', $user->id)
                ->where('is_finalized', true)
                ->select('subject_id', 'section_id', \Illuminate\Support\Facades\DB::raw('count(*) as count'))
                ->groupBy('subject_id', 'section_id')
                ->get()
                ->keyBy(fn($item) => $item->subject_id . '-' . $item->section_id);

            $subjectsToGrade->map(function ($sts) use ($gradeCounts) {
                $key = $sts->subject_id . '-' . $sts->section_id;
                $gradedCount = $gradeCounts->get($key)->count ?? 0;
                $totalStudents = $sts->section->students()->count();
                $sts->progress = $totalStudents > 0 ? round(($gradedCount / $totalStudents) * 100) : 0;
                return $sts;
            });

            // 2. Sections to Consolidate (as Adviser)
            $sectionsToAdvise = \App\Models\Section::withCount('subjectTeacherSections')
                ->where('adviser_id', $user->id)
                ->get();

            $submittedPerSection = \App\Models\Grade::whereIn('section_id', $sectionsToAdvise->pluck('id'))
                ->where('is_finalized', true)
                ->select('section_id', \Illuminate\Support\Facades\DB::raw('count(distinct subject_id) as count'))
                ->groupBy('section_id')
                ->get()
                ->keyBy('section_id');

            $sectionsToAdvise->map(function ($section) use ($submittedPerSection) {
                $totalSubjects = $section->subject_teacher_sections_count;
                $submittedCount = $submittedPerSection->get($section->id)->count ?? 0;
                $section->consolidation_progress = $totalSubjects > 0 ? round(($submittedCount / $totalSubjects) * 100) : 0;
                return $section;
            });

            $deadlines = \App\Models\Deadline::with('gradingPeriod')
                ->where('deadline_at', '>', now())
                ->orderBy('deadline_at', 'asc')
                ->get();

            return compact('subjectsToGrade', 'sectionsToAdvise', 'deadlines');
        });

        return view('teacher.dashboard', $data);
    }

    public function index()
    {
        $grades = \App\Models\Grade::with(['student', 'subject'])
            ->where('teacher_id', auth()->id())
            ->paginate(20);
        return view('teacher.grades.index', compact('grades'));
    }

    public function create()
    {
        $periods = \App\Models\GradingPeriod::where('is_active', true)->get();
        return view('teacher.grades.create', compact('periods'));
    }

    public function sheet($subjectId, $sectionId)
    {
        $subject = \App\Models\Subject::findOrFail($subjectId);
        $section = \App\Models\Section::with('students')->findOrFail($sectionId);
        
        $sts = \App\Models\SubjectTeacherSection::where('subject_id', $subjectId)
            ->where('section_id', $sectionId)
            ->where('teacher_id', auth()->id())
            ->firstOrFail();

        $students = $section->students->map(function($student) use ($subject, $section) {
            $grade = \App\Models\Grade::firstOrCreate(
                [
                    'student_id' => $student->id,
                    'subject_id' => $subject->id,
                    'section_id' => $section->id,
                    'teacher_id' => auth()->id(),
                    'grading_period_id' => 1, // Defaulting to 1 for now
                ],
                [
                    'grade' => 0,
                    'written_work_scores' => [],
                    'performance_task_scores' => [],
                    'exam_score' => 0,
                    'submitted_at' => now(),
                ]
            );
            
            $student->grade_record = $grade;
            return $student;
        });

        return view('teacher.grades.sheet', compact('subject', 'section', 'students', 'sts'));
    }

    public function updateSheet(Request $request)
    {
        // 1. Update the max scores
        $stsId = $request->input('sts_id');
        $sts = \App\Models\SubjectTeacherSection::findOrFail($stsId);
        $sts->ww_max_scores = array_map('floatval', $request->input('max_scores.ww') ?? []);
        $sts->pt_max_scores = array_map('floatval', $request->input('max_scores.pt') ?? []);
        $sts->qa_max_score = floatval($request->input('max_scores.qa') ?? 0);
        $sts->save();

        // 2. Update student grades
        $data = $request->input('grades');
        $calcService = new \App\Services\GradeCalculationService();

        foreach ($data as $gradeId => $scores) {
            $grade = \App\Models\Grade::findOrFail($gradeId);
            
            // Check if finalized
            if ($grade->is_finalized) {
                continue; // Skip locked records
            }

            $grade->written_work_scores = array_map('floatval', $scores['ww'] ?? []);
            $grade->performance_task_scores = array_map('floatval', $scores['pt'] ?? []);
            $grade->exam_score = isset($scores['qa']) ? floatval($scores['qa']) : 0;
            
            // Recalculate final grade (Transmuted)
            $grade->grade = $calcService->calculate($grade);
            
            $grade->save();
        }

        // Bust caches so dashboards show fresh data
        cache()->forget('teacher_dashboard_' . auth()->id());
        cache()->forget('admin_heatmap');

        return back()->with('success', 'Grades updated successfully.');
    }

    public function finalizeSheet(Request $request)
    {
        // First update the sheet to save any latest changes
        $this->updateSheet($request);

        $data = $request->input('grades');
        if (empty($data)) {
            return back()->with('error', 'No grades to finalize.');
        }

        $gradeIds = array_keys($data);

        \App\Models\Grade::whereIn('id', $gradeIds)
            ->where('teacher_id', auth()->id())
            ->update([
                'is_finalized' => true,
                'submitted_at' => now()
            ]);

        // Bust caches
        cache()->forget('teacher_dashboard_' . auth()->id());
        cache()->forget('admin_heatmap');

        return back()->with('success', 'Grades finalized and submitted to adviser.');
    }

    public function requestUnlock(Request $request)
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'section_id' => 'required|exists:sections,id',
            'reason' => 'required|string|max:500',
        ]);

        \App\Models\GradeUnlockRequest::create([
            'teacher_id' => auth()->id(),
            'subject_id' => $validated['subject_id'],
            'section_id' => $validated['section_id'],
            'reason' => $validated['reason'],
            'status' => 'pending',
        ]);

        return back()->with('success', 'Unlock request sent to principal.');
    }
}
