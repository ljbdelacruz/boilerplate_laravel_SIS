<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Section extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'grade_level',
        'adviser_id',
        'school_year_id',
        'is_active'
    ];

    public function adviser()
    {
        return $this->belongsTo(User::class, 'adviser_id');
    }
    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function schoolYear()
    {
        return $this->belongsTo(SchoolYear::class);
    }
    public function curriculums()
    {
        return $this->hasMany(Curriculum::class);
    }
}