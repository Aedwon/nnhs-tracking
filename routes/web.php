<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\SectionController;
use App\Http\Controllers\Admin\GradingPeriodController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Teacher\GradeController as TeacherGradeController;
use App\Http\Controllers\Teacher\SubjectAssignmentController;
use App\Http\Controllers\AdviserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        if (auth()->user()->hasRole('Admin')) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('teacher.dashboard');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin Routes
    Route::middleware(['role:Admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::resource('teachers', TeacherController::class);
        Route::resource('sections', SectionController::class);
        Route::resource('grading-periods', GradingPeriodController::class);
        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
        Route::get('/unlock-requests', [AdminDashboardController::class, 'unlockRequests'])->name('unlock-requests');
        Route::post('/unlock-requests/{unlockRequest}', [AdminDashboardController::class, 'processUnlockRequest'])->name('unlock-requests.process');
    });

    // Adviser Routes
    Route::middleware(['role:Teacher|Adviser'])->prefix('adviser')->name('adviser.')->group(function () {
        Route::get('/dashboard', [AdviserController::class, 'index'])->name('dashboard');
        Route::get('/sections/{section}/subjects', [AdviserController::class, 'manageSubjects'])->name('subjects');
        Route::post('/sections/{section}/subjects', [AdviserController::class, 'updateSubjects'])->name('subjects.update');
    });

    // Teacher Routes
    Route::middleware(['role:Teacher|Adviser'])->prefix('teacher')->name('teacher.')->group(function () {
        Route::get('/dashboard', [TeacherGradeController::class, 'dashboard'])->name('dashboard');
        
        // Subject Assignments (Claiming slots)
        Route::get('/assignments', [SubjectAssignmentController::class, 'index'])->name('assignments.index');
        Route::post('/assignments/{subject}/claim', [SubjectAssignmentController::class, 'claim'])->name('assignments.claim');
        Route::post('/assignments/{subject}/unclaim', [SubjectAssignmentController::class, 'unclaim'])->name('assignments.unclaim');

        // Grading
        Route::get('/grades/sheet/{subject}/{section}/{period?}', [TeacherGradeController::class, 'sheet'])->name('grades.sheet');
        Route::post('/grades/update', [TeacherGradeController::class, 'updateSheet'])->name('grades.update');
        Route::post('/grades/finalize', [TeacherGradeController::class, 'finalizeSheet'])->name('grades.finalize');
        Route::post('/grades/unlock-request', [TeacherGradeController::class, 'requestUnlock'])->name('grades.unlock-request');
    });
});

require __DIR__.'/auth.php';
