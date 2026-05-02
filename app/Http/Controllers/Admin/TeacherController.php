<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Services\AuditLogger;

class TeacherController extends Controller
{
    public function index()
    {
        $teachers = User::role(['Teacher', 'Adviser'])
            ->withCount('grades')
            ->orderBy('name')
            ->paginate(15);
        return view('admin.teachers.index', compact('teachers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'school_level' => 'required|in:JHS,SHS,BOTH',
            'is_adviser' => 'nullable|boolean',
        ]);

        $teacher = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'school_level' => $validated['school_level'],
            'password' => Hash::make($validated['password']),
        ]);

        $teacher->assignRole('Teacher');
        if ($request->boolean('is_adviser')) {
            $teacher->assignRole('Adviser');
        }

        AuditLogger::log('Teacher Created', $teacher->id, [
            'email' => $teacher->email,
            'level' => $teacher->school_level,
            'is_adviser' => $request->boolean('is_adviser')
        ]);

        return redirect()->route('admin.teachers.index')->with('success', 'Teacher created successfully.');
    }

    public function update(Request $request, User $teacher)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $teacher->id,
            'password' => 'nullable|string|min:8|confirmed',
            'school_level' => 'required|in:JHS,SHS,BOTH',
            'is_adviser' => 'nullable|boolean',
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'school_level' => $validated['school_level'],
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $teacher->update($data);

        // Sync Roles
        if ($request->boolean('is_adviser')) {
            $teacher->assignRole('Adviser');
        } else {
            $teacher->removeRole('Adviser');
        }

        AuditLogger::log('Teacher Updated', $teacher->id, [
            'email' => $teacher->email,
            'level' => $teacher->school_level,
            'is_adviser' => $request->boolean('is_adviser')
        ]);

        return redirect()->route('admin.teachers.index')->with('success', 'Teacher updated successfully.');
    }

    public function destroy(User $teacher)
    {
        AuditLogger::log('Teacher Deleted', $teacher->id, ['name' => $teacher->name]);
        $teacher->delete();
        return redirect()->route('admin.teachers.index')->with('success', 'Teacher deleted successfully.');
    }
}
