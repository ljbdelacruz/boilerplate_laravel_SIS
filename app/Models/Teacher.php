<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $fillable = [
        'user_id',
        'specialization',
        'bio',
        'contact_number'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'teacher_courses')
                    ->withPivot('school_year_id')
                    ->withTimestamps();
    }

    public function advisedSections()
    {
        return $this->hasMany(Section::class, 'adviser_id');
    }
}