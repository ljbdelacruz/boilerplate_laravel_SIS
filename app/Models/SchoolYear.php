<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolYear extends Model
{
    protected $table = 'school_years';  // Changed to match migration table name
    protected $fillable = [
        'school_year',
        'grade_level',
        'section_name',
        'is_archived'
    ];

    public function gradeLevels()
    {
        return $this->hasMany(GradeLevel::class);
    }
}