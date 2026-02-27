<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    public function dashboard()
    {
        $teacher = auth()->user();
        $deadlines = \App\Models\Deadline::with('gradingPeriod')->where('deadline_at', '>', now())->get();
        $recentGrades = \App\Models\Grade::where('teacher_id', $teacher->id)->orderBy('created_at', 'desc')->take(10)->get();

        $totalUploaded = \App\Models\Grade::where('teacher_id', $teacher->id)->count();
        $lateCount = \App\Models\Grade::where('teacher_id', $teacher->id)->where('submission_status', 'Late')->count();
        $compliance = $totalUploaded > 0 ? round((($totalUploaded - $lateCount) / $totalUploaded) * 100, 2) : 100;

        return view('teacher.dashboard', compact('deadlines', 'recentGrades', 'totalUploaded', 'lateCount', 'compliance'));
    }

    public function index()
    {
        $grades = \App\Models\Grade::where('teacher_id', auth()->id())->paginate(20);
        return view('teacher.grades.index', compact('grades'));
    }

    public function create()
    {
        $periods = \App\Models\GradingPeriod::where('is_active', true)->get();
        return view('teacher.grades.create', compact('periods'));
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'grading_period_id' => 'required|exists:grading_periods,id',
            'student_id_number' => 'required|string',
            'student_name' => 'required|string',
            'subject_code' => 'required|string',
            'grade' => 'required|numeric|min:0|max:100',
        ]);

        $deadline = \App\Models\Deadline::where('grading_period_id', $request->grading_period_id)->first();
        $status = 'On-time';

        if ($deadline && now()->gt($deadline->deadline_at)) {
            $status = 'Late';
        }

        $grade = \App\Models\Grade::create([
            'teacher_id' => auth()->id(),
            'grading_period_id' => $request->grading_period_id,
            'student_id_number' => $request->student_id_number,
            'student_name' => $request->student_name,
            'subject_code' => $request->subject_code,
            'grade' => $request->grade,
            'submission_status' => $status,
            'submitted_at' => now(),
        ]);

        \App\Services\AuditLogger::log('Grade Uploaded', $grade->id, ['student' => $request->student_name, 'status' => $status]);

        return redirect()->route('teacher.grades.index')->with('success', 'Grade submitted successfully as ' . $status);
    }
}
