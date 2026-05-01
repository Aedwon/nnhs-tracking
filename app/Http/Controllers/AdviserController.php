<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\Student;
use App\Models\Subject;
use App\Models\SubjectTeacherSection;
use App\Models\User;
use Illuminate\Http\Request;

class AdviserController extends Controller
{
    public function students(Section $section)
    {
        if ($section->adviser_id !== auth()->id()) {
            abort(403);
        }

        $students = $section->students()->orderBy('last_name')->get();
        return view('adviser.students', compact('section', 'students'));
    }

    public function storeStudent(Request $request, Section $section)
    {
        if ($section->adviser_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'id_number' => 'required|string|unique:students,id_number',
        ]);

        $section->students()->create($validated);

        return back()->with('success', 'Student added successfully.');
    }

    public function subjects(Section $section)
    {
        if ($section->adviser_id !== auth()->id()) {
            abort(403);
        }

        $assignedSubjects = $section->subjectTeacherSections()->with(['subject', 'teacher'])->get();
        $allSubjects = Subject::orderBy('name')->get();
        $allTeachers = User::whereHas('roles', function($q) {
            $q->where('name', 'Teacher');
        })->orderBy('name')->get();

        return view('adviser.subjects', compact('section', 'assignedSubjects', 'allSubjects', 'allTeachers'));
    }

    public function storeSubject(Request $request, Section $section)
    {
        if ($section->adviser_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:users,id',
        ]);

        // Prevent duplicate assignment
        $exists = SubjectTeacherSection::where('section_id', $section->id)
            ->where('subject_id', $validated['subject_id'])
            ->exists();

        if ($exists) {
            return back()->with('error', 'Subject already assigned to this section.');
        }

        SubjectTeacherSection::create([
            'section_id' => $section->id,
            'subject_id' => $validated['subject_id'],
            'teacher_id' => $validated['teacher_id'],
            'ww_max_scores' => [],
            'pt_max_scores' => [],
            'qa_max_score' => 0,
        ]);

        return back()->with('success', 'Subject assigned successfully.');
    }
}
