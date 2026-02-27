<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    protected $fillable = [
        'teacher_id',
        'grading_period_id',
        'student_id_number',
        'student_name',
        'subject_code',
        'grade',
        'submission_status',
        'submitted_at'
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'grade' => 'decimal:2',
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function gradingPeriod()
    {
        return $this->belongsTo(GradingPeriod::class);
    }
}
