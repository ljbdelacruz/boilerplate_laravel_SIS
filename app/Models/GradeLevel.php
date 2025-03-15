<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GradeLevel extends Model
{
    protected $table = 'grade_levels';  // Changed from 'grade_level' to 'grade_levels'
    protected $fillable = ['school_year_id', 'grade_level'];

    public function schoolYear()
    {
        return $this->belongsTo(SchoolYear::class);
    }

    public function sections()
    {
        return $this->hasMany(Section::class);
    }
}