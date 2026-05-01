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
