<?php

namespace Database\Seeders;

use App\Models\SchoolYear;
use Illuminate\Database\Seeder;

class SchoolYearSeeder extends Seeder
{
    public function run(): void
    {
        $schoolYears = [
            [
                'school_year' => '2023-2024-G7',
                'grade_level' => 'Grade 7',
                'section_name' => 'Section A'
            ],
            [
                'school_year' => '2023-2024-G8',
                'grade_level' => 'Grade 8',
                'section_name' => 'Section B'
            ],
            [
                'school_year' => '2023-2024-G9',
                'grade_level' => 'Grade 9',
                'section_name' => 'Section C'
            ],
            [
                'school_year' => '2023-2024-G10',
                'grade_level' => 'Grade 10',
                'section_name' => 'Section D'
            ]
        ];

        foreach ($schoolYears as $schoolYear) {
            SchoolYear::updateOrCreate(
                [
                    'school_year' => $schoolYear['school_year'],
                    'grade_level' => $schoolYear['grade_level'],
                    'section_name' => $schoolYear['section_name']
                ],
                $schoolYear
            );
        }
    }
}