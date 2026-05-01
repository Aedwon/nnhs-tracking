<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'id_number',
        'first_name',
        'last_name',
        'middle_name',
        'suffix',
        'gender',
        'birth_date',
        'enrollment_year',
    ];

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }
}
