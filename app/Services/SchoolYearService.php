<?php

namespace App\Services;

use App\Models\SchoolYear;
use App\Models\GradeLevel;
use App\Models\Section;
use Illuminate\Support\Facades\DB;

class SchoolYearService
{
    public function createSchoolYear(array $data)
    {
        return DB::transaction(function () use ($data) {
            $schoolYear = SchoolYear::create([
                'school_year' => $data['school_year']
            ]);

            $gradeLevel = $schoolYear->gradeLevels()->create([
                'grade_level' => $data['grade_level']
            ]);

            $section = $gradeLevel->sections()->create([
                'section_name' => $data['section_name']
            ]);

            return [
                'school_year' => $schoolYear,
                'grade_level' => $gradeLevel,
                'section' => $section
            ];
        });
    }

    public function getAllSchoolYears()
    {
        return SchoolYear::with(['gradeLevels.sections'])->get();
    }
}