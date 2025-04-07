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
                'start_year' => 2023,
                'end_year' => 2024,
                'is_active' => true
            ],
            [
                'start_year' => 2024,
                'end_year' => 2025,
                'is_active' => false
            ],
            [
                'start_year' => 2025,
                'end_year' => 2026,
                'is_active' => false
            ]
        ];

        foreach ($schoolYears as $schoolYear) {
            SchoolYear::updateOrCreate(
                [
                    'start_year' => $schoolYear['start_year'],
                    'end_year' => $schoolYear['end_year']
                ],
                $schoolYear
            );
        }
    }
}