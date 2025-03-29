<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'price',
        'is_active'
    ];

    public function students()
    {
        return $this->belongsToMany(Student::class, 'student_courses')
                    ->withPivot('school_year_id', 'amount_paid', 'status')
                    ->withTimestamps();
    }
}