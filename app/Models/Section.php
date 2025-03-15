<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $table = 'sections'; // Changed from 'section' to 'sections'
    protected $fillable = ['grade_level_id', 'section_name'];

    public function gradeLevel()
    {
        return $this->belongsTo(GradeLevel::class);
    }
}