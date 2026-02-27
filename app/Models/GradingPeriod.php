<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GradingPeriod extends Model
{
    protected $fillable = ['name', 'start_date', 'end_date', 'is_active'];

    public function deadlines()
    {
        return $this->hasMany(Deadline::class);
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }
}
