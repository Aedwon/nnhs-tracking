<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    protected $fillable = [
        'teacher_id',
        'grading_period_id',
        'student_id',
        'subject_id',
        'section_id',
        'written_work_scores',
        'performance_task_scores',
        'exam_score',
        'grade',
        'is_finalized',
        'justification',
        'submission_status',
        'submitted_at'
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'grade' => 'decimal:2',
        'exam_score' => 'decimal:2',
        'written_work_scores' => 'array',
        'performance_task_scores' => 'array',
        'is_finalized' => 'boolean',
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function gradingPeriod()
    {
        return $this->belongsTo(GradingPeriod::class);
    }
}
