<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\Subject;
use Illuminate\Http\Request;

class AdviserController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $sections = Section::where('adviser_id', $user->id)
            ->with(['subjects.teacher'])
            ->get();
            
        return view('adviser.dashboard', compact('sections'));
    }

    public function manageSubjects(Section $section)
    {
        // Check if user is the adviser
        if ($section->adviser_id != auth()->id()) {
            abort(403);
        }

        $section->load('subjects.teacher');
        return view('adviser.subjects', compact('section'));
    }

    public function updateSubjects(Request $request, Section $section)
    {
        if ($section->adviser_id != auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'expected_subjects_count' => 'required|integer|min:0',
            'subjects' => 'nullable|array',
            'subjects.*.id' => 'nullable|exists:subjects,id',
            'subjects.*.name' => 'required|string|max:255',
        ]);

        $section->update([
            'expected_subjects_count' => $validated['expected_subjects_count']
        ]);

        // Sync subjects
        $providedIds = collect($validated['subjects'] ?? [])->pluck('id')->filter()->toArray();
        
        // Remove subjects not in the request
        $section->subjects()->whereNotIn('id', $providedIds)->delete();

        foreach ($validated['subjects'] ?? [] as $subjData) {
            if (isset($subjData['id'])) {
                Subject::where('id', $subjData['id'])->update(['name' => $subjData['name']]);
            } else {
                $section->subjects()->create(['name' => $subjData['name']]);
            }
        }

        return redirect()->route('adviser.subjects', $section)->with('success', 'Curriculum updated successfully.');
    }
}
