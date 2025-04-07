<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolYear extends Model
{
    protected $fillable = [
        'start_year',
        'end_year',
        'is_active'
    ];

    protected $casts = [
        'start_year' => 'integer',
        'end_year' => 'integer',
        'is_active' => 'boolean'
    ];

    public function getSchoolYearDisplayAttribute()
    {
        return "{$this->start_year}-{$this->end_year}";
    }

    public function sections()
    {
        return $this->hasMany(Section::class);
    }
}