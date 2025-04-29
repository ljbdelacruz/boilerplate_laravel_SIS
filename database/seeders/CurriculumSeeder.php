<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Curriculum;
use App\Models\Section;
use App\Models\Course;

class CurriculumSeeder extends Seeder
{
    public function run(): void
    {
        // Example: Assign each course to each section with a sample time
        $sections = Section::all();
        $courses = Course::all();

        foreach ($sections as $section) {
            foreach ($courses as $course) {
                Curriculum::updateOrCreate(
                    [
                        'section_id' => $section->id,
                        'subject_id' => $course->id,
                    ],
                    [
                        'time' => '08:00 AM - 09:00 AM',
                    ]
                );
            }
        }
    }
}
