<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Student extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'lrn',
        'user_id',
        'student_id',
        'first_name',
        'last_name',
        'middle_name',
        'suffix',
        'birth_date',
        'gender',
        'address',
        'contact_number',
        'guardian_name',
        'guardian_contact',
        'grade_level',
        'section_id',
        'school_year_id'
    ];

    protected $casts = [
        'birth_date' => 'date:d-m-Y',
        'grade_level' => 'string',
        'section_id' => 'integer',
        'school_year_id' => 'integer'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function schoolYear(): BelongsTo
    {
        return $this->belongsTo(SchoolYear::class);
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    public function enrollmentHistories()
    {
        return $this->hasMany(StudentEnrollmentHistory::class);
    }
}
