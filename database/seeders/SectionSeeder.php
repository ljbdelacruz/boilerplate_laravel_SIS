<?php

namespace Database\Seeders;

use App\Models\Section;
use App\Models\SchoolYear;
use Illuminate\Database\Seeder;

class SectionSeeder extends Seeder
{
    public function run(): void
    {

        // Get or create a school year
        $schoolYear = SchoolYear::first() ?? SchoolYear::create([
            'year_start' => 2024,
            'year_end' => 2025,
            'is_active' => true,
        ]);

        $sections = [
            [
                'name' => 'Section A',
                'grade_level' => 'Grade 1',
                'is_active' => true,
                'school_year_id' => $schoolYear->id,
            ],
            [
                'name' => 'Section B',
                'grade_level' => 'Grade 2',
                'is_active' => true,
                'school_year_id' => $schoolYear->id,
            ],
            [
                'name' => 'Section C',
                'grade_level' => 'Grade 3',
                'is_active' => true,
                'school_year_id' => $schoolYear->id,
            ],
            [
                'name' => 'Section D',
                'grade_level' => 'Grade 4',
                'is_active' => true,
                'school_year_id' => $schoolYear->id,
            ]
        ];

        foreach ($sections as $sectionData) {
            Section::updateOrCreate(
                [
                    'name' => $sectionData['name'], 
                    'grade_level' => $sectionData['grade_level'],
                    'school_year_id' => $sectionData['school_year_id']
                ],
                $sectionData
            );
        }
    }
}