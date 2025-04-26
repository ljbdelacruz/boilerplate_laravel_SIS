<?php

namespace Database\Seeders;

use App\Models\GradeLevel;
use Illuminate\Database\Seeder;

class GradeLevelSeeder extends Seeder
{
    public function run(): void
    {
        $gradeLevels = [
            'Kindergarten 1',
            'Kindergarten 2',
            'Grade 1',
            'Grade 2',
            'Grade 3',
            'Grade 4',
            'Grade 5',
            'Grade 6'
        ];

        foreach ($gradeLevels as $level) {
            GradeLevel::create([
                'grade_level' => $level
            ]);
        }
    }
}