<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'student_id',
        'first_name',
        'last_name',
        'birth_date',
        'gender',
        'address',
        'guardian_name',
        'guardian_contact',
        'school_year_id',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function schoolYear()
    {
        return $this->belongsTo(SchoolYear::class);
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'student_courses')
                    ->withPivot('school_year_id', 'amount_paid', 'status')
                    ->withTimestamps();
    }
}
