<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GradingPeriodController extends Controller
{
    public function index()
    {
        $periods = \App\Models\GradingPeriod::with('deadlines')->get();
        return view('admin.grading-periods.index', compact('periods'));
    }

    public function create()
    {
        return view('admin.grading-periods.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'deadline_at' => 'required|date|after:start_date',
        ]);

        $period = \App\Models\GradingPeriod::create([
            'name' => $request->name,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_active' => true,
        ]);

        \App\Models\Deadline::create([
            'grading_period_id' => $period->id,
            'deadline_at' => $request->deadline_at,
            'reminder_x_hours_before' => 24,
        ]);

        \App\Services\AuditLogger::log('Grading Period Created', $period->id, ['name' => $period->name]);

        return redirect()->route('admin.grading-periods.index')->with('success', 'Grading period and deadline created.');
    }
}
