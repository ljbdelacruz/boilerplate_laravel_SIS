<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role'  // Make sure this is included
    ];

    public function student()
    {
        return $this->hasOne(Student::class);
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'student_courses', 'student_id', 'course_id')
                    ->withPivot('amount_paid', 'status', 'school_year_id')
                    ->withTimestamps();
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'teacher_id');
    }

    // Add this method to the User model
    public function teachingCourses()
    {
        return $this->belongsToMany(Course::class, 'teacher_courses', 'teacher_id', 'course_id')
                    ->withPivot('school_year_id')
                    ->withTimestamps();
    }

    public function scopeTeachers($query)
    {
        return $query->where('role', 'teacher');
    }

    public function isTeacher()
    {
        return $this->role === 'teacher';
    }
}
