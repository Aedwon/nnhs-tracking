<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubjectTeacherSection extends Model
{
    protected $table = 'subject_teacher_section';

    protected $fillable = [
        'subject_id',
        'teacher_id',
        'section_id',
        'school_year',
        'ww_max_scores',
        'pt_max_scores',
        'qa_max_score',
    ];

    protected $casts = [
        'ww_max_scores' => 'array',
        'pt_max_scores' => 'array',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }
}
