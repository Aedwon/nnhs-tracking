<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\User;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function index()
    {
        $sections = Section::with(['adviser', 'subjects'])
            ->orderBy('grade_level')
            ->orderBy('name')
            ->paginate(10);
            
        $advisers = User::role(['Teacher', 'Adviser'])->orderBy('name')->get();
        
        return view('admin.sections.index', compact('sections', 'advisers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'grade_level' => 'required|integer|min:1|max:12',
            'level' => 'required|in:JHS,SHS',
            'adviser_id' => 'required|exists:users,id',
        ]);

        Section::create($validated);

        return redirect()->route('admin.sections.index')->with('success', 'Section created successfully.');
    }

    public function update(Request $request, Section $section)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'grade_level' => 'required|integer|min:1|max:12',
            'level' => 'required|in:JHS,SHS',
            'adviser_id' => 'required|exists:users,id',
        ]);

        $section->update($validated);

        return redirect()->route('admin.sections.index')->with('success', 'Section updated successfully.');
    }

    public function destroy(Section $section)
    {
        $section->delete();
        return redirect()->route('admin.sections.index')->with('success', 'Section deleted.');
    }
}
