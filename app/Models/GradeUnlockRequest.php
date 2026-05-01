<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GradeUnlockRequest extends Model
{
    protected $fillable = [
        'teacher_id',
        'subject_id',
        'section_id',
        'reason',
        'status',
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }
}
