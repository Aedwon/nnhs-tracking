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
    });
});

require __DIR__ . '/auth.php';
