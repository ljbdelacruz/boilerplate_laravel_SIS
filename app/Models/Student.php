<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'student_id',
        'first_name',
        'last_name',
        'middle_name',
        'birth_date',
        'gender',
        'address',
        'contact_number',
        'email',
        'guardian_name',
        'guardian_contact',
        'school_year_id'
    ];

    public function schoolYear()
    {
        return $this->belongsTo(SchoolYear::class);
    }
}
