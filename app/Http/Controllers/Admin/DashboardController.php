<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalGrades = \App\Models\Grade::count();
        $lateGrades = \App\Models\Grade::where('submission_status', 'Late')->count();
        $onTimeGrades = $totalGrades - $lateGrades;

        $complianceRate = $totalGrades > 0 ? round(($onTimeGrades / $totalGrades) * 100, 2) : 100;

        $frequentLateUploaders = \App\Models\Grade::where('submission_status', 'Late')
            ->select('teacher_id', \Illuminate\Support\Facades\DB::raw('count(*) as late_count'))
            ->groupBy('teacher_id')
            ->orderBy('late_count', 'desc')
            ->with('teacher')
            ->take(5)
            ->get();

        $gradingPeriods = \App\Models\GradingPeriod::withCount('grades')->get();

        return view('admin.dashboard', compact(
            'totalGrades',
            'lateGrades',
            'onTimeGrades',
            'complianceRate',
            'frequentLateUploaders',
            'gradingPeriods'
        ));
    }
}
