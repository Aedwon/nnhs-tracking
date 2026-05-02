<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\GradingPeriod;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Deadline;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GradeController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        $cacheKey = 'teacher_dashboard_' . $user->id;

        $data = cache()->remember($cacheKey, 30, function () use ($user) {
            // 1. My Subjects
            $subjectsToGrade = Subject::with(['section' => function($q) {
                $q->withCount('students');
            }])
                ->where('teacher_id', $user->id)
                ->get();

            $activePeriod = GradingPeriod::where('is_active', true)->first() ?? GradingPeriod::first();
            $activePeriodId = $activePeriod ? $activePeriod->id : 1;

            $gradeCounts = Grade::where('teacher_id', $user->id)
                ->where('grading_period_id', $activePeriodId)
                ->where('is_finalized', true)
                ->select('subject_id', 'section_id', DB::raw('count(*) as count'))
                ->groupBy('subject_id', 'section_id')
                ->get()
                ->keyBy(fn($item) => $item->subject_id . '-' . $item->section_id);

            foreach ($subjectsToGrade as $subject) {
                $key = $subject->id . '-' . $subject->section_id;
                $gradedCount = $gradeCounts->get($key)->count ?? 0;
                $totalStudents = $subject->section->students_count;
                $subject->progress = $totalStudents > 0 ? round(($gradedCount / $totalStudents) * 100) : 0;
            }

            // 2. Sections I Advise (Monitoring)
            $sectionsToAdvise = Section::with(['subjects.teacher'])
                ->where('adviser_id', $user->id)
                ->get();

            if ($sectionsToAdvise->isNotEmpty()) {
                $sectionIds = $sectionsToAdvise->pluck('id');
                $finalizedStatuses = Grade::whereIn('section_id', $sectionIds)
                    ->where('grading_period_id', $activePeriodId)
                    ->where('is_finalized', true)
                    ->select('subject_id', 'section_id')
                    ->distinct()
                    ->get()
                    ->groupBy('section_id');

                foreach ($sectionsToAdvise as $section) {
                    $sectionFinalized = $finalizedStatuses->get($section->id, collect());
                    
                    foreach ($section->subjects as $subject) {
                        $subject->is_finalized = $sectionFinalized->contains('subject_id', $subject->id);
                    }

                    $totalExpected = $section->expected_subjects_count > 0 ? $section->expected_subjects_count : $section->subjects->count();
                    $submittedCount = $section->subjects->where('is_finalized', true)->count();
                    $section->consolidation_progress = $totalExpected > 0 ? round(($submittedCount / $totalExpected) * 100) : 0;
                }
            }

            $deadlines = Deadline::with('gradingPeriod')
                ->where('deadline_at', '>', now())
                ->orderBy('deadline_at', 'asc')
                ->get();

            return compact('subjectsToGrade', 'sectionsToAdvise', 'deadlines', 'activePeriod');
        });

        return view('teacher.dashboard', $data);
    }

    public function sheet($subjectId, $sectionId, $periodId = null)
    {
        $subject = Subject::findOrFail($subjectId);
        $section = Section::with('students')->findOrFail($sectionId);
        
        if ($subject->teacher_id != auth()->id() && $section->adviser_id != auth()->id()) {
            abort(403);
        }

        $periods = GradingPeriod::where('level', $section->level)->orWhere('level', 'BOTH')->get();
        $activePeriod = $periodId ? GradingPeriod::findOrFail($periodId) : ($periods->where('is_active', true)->first() ?? $periods->first());
        $isReadOnly = $subject->teacher_id != auth()->id();

        $studentIds = $section->students->pluck('id');
        $allGrades = Grade::whereIn('student_id', $studentIds)
            ->where('subject_id', $subjectId)
            ->where('section_id', $sectionId)
            ->get()
            ->groupBy('student_id');

        $toInsert = [];
        $now = now();

        foreach ($section->students as $student) {
            $studentGrades = $allGrades->get($student->id, collect())->keyBy('grading_period_id');
            foreach ($periods as $p) {
                if (!$studentGrades->has($p->id)) {
                    $toInsert[] = [
                        'student_id' => $student->id,
                        'subject_id' => $subject->id,
                        'section_id' => $section->id,
                        'teacher_id' => $subject->teacher_id,
                        'grading_period_id' => $p->id,
                        'grade' => 0,
                        'written_work_scores' => json_encode([]),
                        'performance_task_scores' => json_encode([]),
                        'exam_score' => 0,
                        'is_finalized' => false,
                        'submitted_at' => null,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }
        }

        if (!empty($toInsert)) {
            Grade::insert($toInsert);
            $allGrades = Grade::whereIn('student_id', $studentIds)
                ->where('subject_id', $subjectId)
                ->where('section_id', $sectionId)
                ->get()
                ->groupBy('student_id');
        }

        $students = $section->students->map(function($student) use ($allGrades, $activePeriod) {
            $student->all_grades = $allGrades->get($student->id)->keyBy('grading_period_id');
            $student->grade_record = $student->all_grades->get($activePeriod->id);
            return $student;
        });

        return view('teacher.grades.sheet', compact('subject', 'section', 'students', 'periods', 'activePeriod', 'isReadOnly'));
    }

    public function updateSheet(Request $request)
    {
        $data = $request->input('grades');
        foreach ($data as $gradeId => $scores) {
            $grade = Grade::findOrFail($gradeId);
            if ($grade->teacher_id != auth()->id() || $grade->is_finalized) continue;
            $grade->grade = isset($scores['grade']) ? floatval($scores['grade']) : 0;
            $grade->save();
        }

        cache()->forget('teacher_dashboard_' . auth()->id());
        cache()->forget('admin_heatmap');
        return back()->with('success', 'Grades updated.');
    }

    public function finalizeSheet(Request $request)
    {
        $this->updateSheet($request);
        $gradeIds = array_keys($request->input('grades', []));
        Grade::whereIn('id', $gradeIds)->where('teacher_id', auth()->id())->update([
            'is_finalized' => true,
            'submitted_at' => now()
        ]);

        cache()->forget('teacher_dashboard_' . auth()->id());
        cache()->forget('admin_heatmap');
        return back()->with('success', 'Grades finalized.');
    }
}
