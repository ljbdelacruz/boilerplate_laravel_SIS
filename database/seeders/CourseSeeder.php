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
                'code' => 'MATH7',
                'name' => 'Mathematics 7',
                'description' => 'Grade 7 Mathematics curriculum',
                'price' => 5000.00,
                'is_active' => true
            ],
            [
                'code' => 'SCI7',
                'name' => 'Science 7',
                'description' => 'Grade 7 Science curriculum',
                'price' => 5000.00,
                'is_active' => true
            ],
            [
                'code' => 'ENG7',
                'name' => 'English 7',
                'description' => 'Grade 7 English curriculum',
                'price' => 4500.00,
                'is_active' => true
            ],
            [
                'code' => 'MATH8',
                'name' => 'Mathematics 8',
                'description' => 'Grade 8 Mathematics curriculum',
                'price' => 5000.00,
                'is_active' => true
            ],
            [
                'code' => 'SCI8',
                'name' => 'Science 8',
                'description' => 'Grade 8 Science curriculum',
                'price' => 5000.00,
                'is_active' => true
            ],
            [
                'code' => 'ENG8',
                'name' => 'English 8',
                'description' => 'Grade 8 English curriculum',
                'price' => 4500.00,
                'is_active' => true
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