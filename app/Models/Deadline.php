<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deadline extends Model
{
    protected $fillable = ['grading_period_id', 'deadline_at', 'reminder_x_hours_before'];

    protected $casts = [
        'deadline_at' => 'datetime',
    ];

    public function gradingPeriod()
    {
        return $this->belongsTo(GradingPeriod::class);
    }
}
