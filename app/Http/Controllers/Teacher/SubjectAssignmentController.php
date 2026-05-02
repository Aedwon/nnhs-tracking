<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectAssignmentController extends Controller
{
    public function index()
    {
        $sections = Section::with(['subjects' => function($q) {
            $q->whereNull('teacher_id');
        }])->get();

        $mySubjects = Subject::where('teacher_id', auth()->id())->with('section')->get();

        return view('teacher.subjects.index', compact('sections', 'mySubjects'));
    }

    public function claim(Request $request, Subject $subject)
    {
        if ($subject->teacher_id !== null) {
            return back()->with('error', 'This subject is already claimed by another teacher.');
        }

        $subject->update(['teacher_id' => auth()->id()]);

        return redirect()->route('teacher.dashboard')->with('success', "You have claimed {$subject->name} for {$subject->section->name}.");
    }

    public function unclaim(Subject $subject)
    {
        if ($subject->teacher_id != auth()->id()) {
            abort(403);
        }

        $subject->update(['teacher_id' => null]);

        return back()->with('success', "You have unclaimed {$subject->name}.");
    }
}
