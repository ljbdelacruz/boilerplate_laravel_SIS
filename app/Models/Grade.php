<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    protected $fillable = [
        'student_id',
        'subject_id',
        'school_year_id',
        'prelim',
        'midterm',
        'prefinal',
        'final'
    ];

    protected $casts = [
        'prelim' => 'decimal:2',
        'midterm' => 'decimal:2',
        'prefinal' => 'decimal:2',
        'final' => 'decimal:2'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function subject()
    {
        return $this->belongsTo(Course::class, 'subject_id');
    }

    public function schoolYear()
    {
        return $this->belongsTo(SchoolYear::class);
    }
}
