<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/test-view', function () {
    return view('test');
});

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin Routes
    Route::middleware(['role:Admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
        Route::resource('teachers', \App\Http\Controllers\Admin\TeacherController::class);
        Route::resource('grading-periods', \App\Http\Controllers\Admin\GradingPeriodController::class);
        Route::get('/audit-logs', [\App\Http\Controllers\Admin\AuditLogController::class, 'index'])->name('audit-logs.index');
    });

    // Teacher Routes
    Route::middleware(['role:Teacher'])->prefix('teacher')->name('teacher.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Teacher\GradeController::class, 'dashboard'])->name('dashboard');
        Route::get('/grades', [\App\Http\Controllers\Teacher\GradeController::class, 'index'])->name('grades.index');
        Route::get('/grades/sheet/{subject}/{section}', [\App\Http\Controllers\Teacher\GradeController::class, 'sheet'])->name('grades.sheet');
        Route::post('/grades/sheet/update', [\App\Http\Controllers\Teacher\GradeController::class, 'updateSheet'])->name('grades.update-sheet');
        Route::post('/grades/sheet/finalize', [\App\Http\Controllers\Teacher\GradeController::class, 'finalizeSheet'])->name('grades.finalize-sheet');

        // Adviser Routes
        Route::get('/adviser/section/{section}/students', [\App\Http\Controllers\AdviserController::class, 'students'])->name('adviser.students');
        Route::post('/adviser/section/{section}/students', [\App\Http\Controllers\AdviserController::class, 'storeStudent'])->name('adviser.store-student');
        Route::get('/adviser/section/{section}/subjects', [\App\Http\Controllers\AdviserController::class, 'subjects'])->name('adviser.subjects');
        Route::post('/adviser/section/{section}/subjects', [\App\Http\Controllers\AdviserController::class, 'storeSubject'])->name('adviser.store-subject');
        
        // Unlock Request
        Route::post('/grades/request-unlock', [\App\Http\Controllers\Teacher\GradeController::class, 'requestUnlock'])->name('grades.request-unlock');
    });

    // Admin Unlock Requests
    Route::middleware(['role:Admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/unlock-requests', [\App\Http\Controllers\Admin\DashboardController::class, 'unlockRequests'])->name('unlock-requests');
        Route::post('/unlock-requests/{unlockRequest}/process', [\App\Http\Controllers\Admin\DashboardController::class, 'processUnlockRequest'])->name('unlock-requests.process');
    });
});

require __DIR__ . '/auth.php';
