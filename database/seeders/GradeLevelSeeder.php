<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;

class GradeLevelSeeder extends Seeder
{
    public function run(): void
    {
        $gradeLevels = [
            'MATH7' => 7,
            'SCI7' => 7,
            'ENG7' => 7,
            'FIL7' => 7,
            'AP7' => 7,
            'ESP7' => 7,
            'TLE7' => 7,
            'MAPEH7' => 7,
            
            'MATH8' => 8,
            'SCI8' => 8,
            'ENG8' => 8,
            'FIL8' => 8,
            'AP8' => 8,
            'ESP8' => 8,
            'TLE8' => 8,
            'MAPEH8' => 8,
            
            'MATH9' => 9,
            'SCI9' => 9,
            'ENG9' => 9,
            'FIL9' => 9,
            'AP9' => 9,
            'ESP9' => 9,
            'TLE9' => 9,
            'MAPEH9' => 9,
            
            'MATH10' => 10,
            'SCI10' => 10,
            'ENG10' => 10,
            'FIL10' => 10,
            'AP10' => 10,
            'ESP10' => 10,
            'TLE10' => 10,
            'MAPEH10' => 10,
        ];

        foreach ($gradeLevels as $code => $level) {
            Course::where('code', $code)->update(['grade_level' => $level]);
        }
    }
}