<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = [
        'subject_code',
        'name',
        'written_weight',
        'performance_weight',
        'exam_weight',
    ];

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    public function subjectTeacherSections()
    {
        return $this->hasMany(SubjectTeacherSection::class);
    }
}
