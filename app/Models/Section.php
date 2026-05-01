<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $fillable = ['name', 'grade_level', 'adviser_id'];

    public function adviser()
    {
        return $this->belongsTo(User::class, 'adviser_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    public function subjectTeacherSections()
    {
        return $this->hasMany(SubjectTeacherSection::class);
    }
}
