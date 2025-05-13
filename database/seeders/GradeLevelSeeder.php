<?php

namespace Database\Seeders;

use App\Models\GradeLevel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GradeLevelSeeder extends Seeder
{
    public function run(): void
    {
        $gradeLevels = [
            ['grade_level' => 'Grade 1'],
            ['grade_level' => 'Grade 2'],
            ['grade_level' => 'Grade 3'],
            ['grade_level' => 'Grade 4'],
            ['grade_level' => 'Grade 5'],
            ['grade_level' => 'Grade 6']
        ];
        // Define the grade levels and their order sequence
        $gradeLevelsData = [
            ['grade_level' => 'Grade 1', 'order_sequence' => 0],
            ['grade_level' => 'Grade 2', 'order_sequence' => 1],
            ['grade_level' => 'Grade 3', 'order_sequence' => 2],
            ['grade_level' => 'Grade 4', 'order_sequence' => 3],
            ['grade_level' => 'Grade 5', 'order_sequence' => 4],
            ['grade_level' => 'Grade 6', 'order_sequence' => 5]
        ];

        foreach ($gradeLevels as $gradeLevel) {
            GradeLevel::updateOrCreate(
                ['grade_level' => $gradeLevel['grade_level']]
            );
        }

        foreach ($gradeLevelsData as $data) {
            $gradeLevel = GradeLevel::where('grade_level', $data['grade_level'])->first();
            if ($gradeLevel) {
                $gradeLevel->order_sequence = $data['order_sequence'];
                $gradeLevel->save();
                $this->command->info("Updated order_sequence for {$data['grade_level']}.");
            } else {
                // If the grade level does not exist return a warning
                $this->command->warn("Grade level '{$data['grade_level']}' not found. Skipping update.");
            }
    }
}
}