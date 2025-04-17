<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $courses = [
            [
                'code' => 'MATH1',
                'name' => 'Mathematics 1',
                'description' => 'Grade 1 Mathematics curriculum',
                'price' => 5000.00,
                'is_active' => true,
                'grade_level' => 1
            ],
            [
                'code' => 'SCI1',
                'name' => 'Science 1',
                'description' => 'Grade 1 Science curriculum',
                'price' => 5000.00,
                'is_active' => true,
                'grade_level' => 1
            ],
            [
                'code' => 'ENG1',
                'name' => 'English 1',
                'description' => 'Grade 1 English curriculum',
                'price' => 4500.00,
                'is_active' => true,
                'grade_level' => 1
            ],
            [
                'code' => 'MATH2',
                'name' => 'Mathematics 2',
                'description' => 'Grade 2 Mathematics curriculum',
                'price' => 5000.00,
                'is_active' => true,
                'grade_level' => 2
            ],
            [
                'code' => 'SCI2',
                'name' => 'Science 2',
                'description' => 'Grade 2 Science curriculum',
                'price' => 5000.00,
                'is_active' => true,
                'grade_level' => 2
            ],
            [
                'code' => 'ENG2',
                'name' => 'English 2',
                'description' => 'Grade 2 English curriculum',
                'price' => 4500.00,
                'is_active' => true,
                'grade_level' => 2
            ]
        ];

        foreach ($courses as $course) {
            Course::updateOrCreate(
                ['code' => $course['code']],
                $course
            );
        }
    }
}