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
                'code' => 'MT1',
                'name' => 'Mother Tongue',
                'description' => 'Mother Tongue for Grade 1',
                'is_active' => true,
                'grade_level' => 'Grade 1'
            ],
            [
                'code' => 'MT2',
                'name' => 'Mother Tongue',
                'description' => 'Mother Tongue for Grade 2',
                'is_active' => true,
                'grade_level' => 'Grade 2'
            ],
            [
                'code' => 'MT3',
                'name' => 'Mother Tongue',
                'description' => 'Mother Tongue for Grade 3',
                'is_active' => true,
                'grade_level' => 'Grade 3'
            ],
            [
                'code' => 'MT4',
                'name' => 'Mother Tongue',
                'description' => 'Mother Tongue for Grade 4',
                'is_active' => true,
                'grade_level' => 'Grade 4'
            ],
            [
                'code' => 'MT5',
                'name' => 'Mother Tongue',
                'description' => 'Mother Tongue for Grade 5',
                'is_active' => true,
                'grade_level' => 'Grade 5'
            ],
            [
                'code' => 'MT6',
                'name' => 'Mother Tongue',
                'description' => 'Mother Tongue for Grade 6',
                'is_active' => true,
                'grade_level' => 'Grade 6'
            ],
            [
                'code' => 'FIL1',
                'name' => 'Filipino',
                'description' => 'Filipino for Grade 1',
                'is_active' => true,
                'grade_level' => 'Grade 1'
            ],
            [
                'code' => 'FIL2',
                'name' => 'Filipino',
                'description' => 'Filipino for Grade 2',
                'is_active' => true,
                'grade_level' => 'Grade 2'
            ],
            [
                'code' => 'FIL3',
                'name' => 'Filipino',
                'description' => 'Filipino for Grade 3',
                'is_active' => true,
                'grade_level' => 'Grade 3'
            ],  
            [
                'code' => 'FIL4',
                'name' => 'Filipino',
                'description' => 'Filipino for Grade 4',
                'is_active' => true,
                'grade_level' => 'Grade 4'
            ],
            [
                'code' => 'FIL5',
                'name' => 'Filipino',
                'description' => 'Filipino for Grade 5',
                'is_active' => true,
                'grade_level' => 'Grade 5'
            ],
            [
                'code' => 'FIL6',
                'name' => 'Filipino',
                'description' => 'Filipino for Grade 6',
                'is_active' => true,
                'grade_level' => 'Grade 6'
            ],
            [
                'code' => 'ENG1',
                'name' => 'English',
                'description' => 'English for Grade 1',
                'is_active' => true,
                'grade_level' => 'Grade 1'
            ],
            [
                'code' => 'ENG2',
                'name' => 'English',
                'description' => 'English for Grade 2',
                'is_active' => true,
                'grade_level' => 'Grade 2'
            ],
            [
                'code' => 'ENG3',
                'name' => 'English',
                'description' => 'English for Grade 3',
                'is_active' => true,
                'grade_level' => 'Grade 3'
            ],
            [
                'code' => 'ENG4',
                'name' => 'English',
                'description' => 'English for Grade 4',
                'is_active' => true,
                'grade_level' => 'Grade 4'
            ],
            [
                'code' => 'ENG5',
                'name' => 'English',
                'description' => 'English for Grade 5',
                'is_active' => true,
                'grade_level' => 'Grade 5'
            ],
            [
                'code' => 'ENG6',
                'name' => 'English',
                'description' => 'English for Grade 6',
                'is_active' => true,
                'grade_level' => 'Grade 6'
            ],
            [
                'code' => 'MATH1',
                'name' => 'Mathematics',
                'description' => 'Mathematics for Grade 1',
                'is_active' => true,
                'grade_level' => 'Grade 1'
            ],
            [
                'code' => 'MATH2',
                'name' => 'Mathematics',
                'description' => 'Mathematics for Grade 2',
                'is_active' => true,
                'grade_level' => 'Grade 2'
            ],
            [
                'code' => 'MATH3',
                'name' => 'Mathematics',
                'description' => 'Mathematics for Grade 3',
                'is_active' => true,
                'grade_level' => 'Grade 3'
            ],
            [
                'code' => 'MATH4',
                'name' => 'Mathematics',
                'description' => 'Mathematics for Grade 4',
                'is_active' => true,
                'grade_level' => 'Grade 4'
            ],
            [
                'code' => 'MATH5',
                'name' => 'Mathematics',
                'description' => 'Mathematics for Grade 5',
                'is_active' => true,
                'grade_level' => 'Grade 5'
            ],
            [
                'code' => 'MATH6',
                'name' => 'Mathematics',
                'description' => 'Mathematics for Grade 6',
                'is_active' => true,
                'grade_level' => 'Grade 6'
            ],
            [
                'code' => 'SCI1',
                'name' => 'Science',
                'description' => 'Science for Grade 1',
                'is_active' => true,
                'grade_level' => 'Grade 1'
            ],
            [
                'code' => 'SCI2',
                'name' => 'Science',
                'description' => 'Science for Grade 2',
                'is_active' => true,
                'grade_level' => 'Grade 2'
            ],
            [
                'code' => 'SCI3',
                'name' => 'Science',
                'description' => 'Science for Grade 3',
                'is_active' => true,
                'grade_level' => 'Grade 3'
            ],
            [
                'code' => 'SCI4',
                'name' => 'Science',
                'description' => 'Science for Grade 4',
                'is_active' => true,
                'grade_level' => 'Grade 4'
            ],
            [
                'code' => 'SCI5',
                'name' => 'Science',
                'description' => 'Science for Grade 5',
                'is_active' => true,
                'grade_level' => 'Grade 5'
            ],
            [
                'code' => 'SCI6',
                'name' => 'Science',
                'description' => 'Science for Grade 6',
                'is_active' => true,
                'grade_level' => 'Grade 6'
            ],
            [
                'code' => 'AP1',
                'name' => 'Araling Panlipunan',
                'description' => 'Araling Panlipunan for Grade 1',
                'is_active' => true,
                'grade_level' => 'Grade 1'
            ],
            [
                'code' => 'AP2',
                'name' => 'Araling Panlipunan',
                'description' => 'Araling Panlipunan for Grade 2',
                'is_active' => true,
                'grade_level' => 'Grade 2'
            ],
            [
                'code' => 'AP3',
                'name' => 'Araling Panlipunan',
                'description' => 'Araling Panlipunan for Grade 3',
                'is_active' => true,
                'grade_level' => 'Grade 3'
            ],
            [
                'code' => 'AP4',
                'name' => 'Araling Panlipunan',
                'description' => 'Araling Panlipunan for Grade 4',
                'is_active' => true,
                'grade_level' => 'Grade 4'
            ],
            [
                'code' => 'AP5',
                'name' => 'Araling Panlipunan',
                'description' => 'Araling Panlipunan for Grade 5',
                'is_active' => true,
                'grade_level' => 'Grade 5'
            ],
            [
                'code' => 'AP6',
                'name' => 'Araling Panlipunan',
                'description' => 'Araling Panlipunan for Grade 6',
                'is_active' => true,
                'grade_level' => 'Grade 6'
            ],
            [
                'code' => 'TLE1',
                'name' => 'EPP / TLE',
                'description' => 'Edukasyong Pantahanan at Pangkabuhayan for Grade 1',
                'is_active' => true,
                'grade_level' => 'Grade 1'
            ],
            [
                'code' => 'TLE2',
                'name' => 'EPP / TLE',
                'description' => 'Edukasyong Pantahanan at Pangkabuhayan for Grade 2',
                'is_active' => true,
                'grade_level' => 'Grade 2'
            ],
            [
                'code' => 'TLE3',
                'name' => 'EPP / TLE',
                'description' => 'Edukasyong Pantahanan at Pangkabuhayan for Grade 3',
                'is_active' => true,
                'grade_level' => 'Grade 3'
            ],
            [
                'code' => 'TLE4',
                'name' => 'EPP / TLE',
                'description' => 'Edukasyong Pantahanan at Pangkabuhayan for Grade 4',
                'is_active' => true,
                'grade_level' => 'Grade 4'
            ],
            [
                'code' => 'TLE5',
                'name' => 'EPP / TLE',
                'description' => 'Edukasyong Pantahanan at Pangkabuhayan for Grade 5',
                'is_active' => true,
                'grade_level' => 'Grade 5'
            ],
            [
                'code' => 'TLE6',
                'name' => 'EPP / TLE',
                'description' => 'Edukasyong Pantahanan at Pangkabuhayan for Grade 6',
                'is_active' => true,
                'grade_level' => 'Grade 6'
            ],
        ];

        foreach ($courses as $courseData){
            Course::updateOrCreate(
                [
                    'code' => $courseData['code'],
                    'grade_level' => $courseData['grade_level']
                ],
                $courseData
            );
        }

        $gradeLevels = [
            'Grade 1',
            'Grade 2',
            'Grade 3',
            'Grade 4',
            'Grade 5',
            'Grade 6'
        ];

        $mapehChildren = [
            ['name' => 'Music', 'code_suffix' => 'M'],
            ['name' => 'Arts', 'code_suffix' => 'A'],
            ['name' => 'Physical Education', 'code_suffix' => 'PE'],
            ['name' => 'Health', 'code_suffix' => 'H']
        ];

        foreach ($gradeLevels as $gradeLevel){
            $gradeSuffix = str_replace('Grade ', '', $gradeLevel);

            // Create parent MAPEH subject
            $parentMapeh = Course::updateOrCreate(
                [
                    'code' => 'MAPEH' . $gradeSuffix,
                    'grade_level' => $gradeLevel,
                ],
                [
                    'name' => 'MAPEH',
                    'description' => 'Music, Arts, PE, and Health for ' . $gradeLevel,
                    'is_active' => true,
                    'parent_id' => null, 
                ]
            );

            // Create sub-subjects for MAPEH
            foreach ($mapehChildren as $child) {
                Course::updateOrCreate(
                    [
                        'code' => 'MAPEH' . $gradeSuffix . '_' . $child['code_suffix'],
                        'grade_level' => $gradeLevel,
                        'parent_id' => $parentMapeh->id,
                    ],
                    [
                        'name' => $child['name'],
                        'description' => $child['name'] . ' for ' . $gradeLevel . ' (Component of MAPEH)',
                        'is_active' => true,
                    ]
                );
            }
        }

        $ESPChildren = [
            ['name' => 'Arabic Language', 'code_suffix' => 'AL'],
            ['name' => 'Isalmic Values Education', 'code_suffix' => 'IVE'],
        ];
        // Seed Edukasyon sa Pagpapakatao (ESP) as parent subjects
        foreach ($gradeLevels as $gradeLevel){
            $gradeSuffix = str_replace('Grade ', '', $gradeLevel);

            // Create parent ESP subject
            $parentESP =Course::updateOrCreate(
                [
                    'code' => 'ESP' . $gradeSuffix, 
                    'grade_level' => $gradeLevel,
                ],
                [
                    'name' => 'Edukasyon sa Pagpapakatao', 
                    'description' => 'Edukasyon sa Pagpapakatao for ' . $gradeLevel, 
                    'is_active' => true,
                    'parent_id' => null, 
                ]
            );

            // Create sub-subjects for ESP
            foreach ($ESPChildren as $child) {
                Course::updateOrCreate(
                    [
                        'code' => 'ESP' . $gradeSuffix . '_' . $child['code_suffix'],
                        'grade_level' => $gradeLevel,
                        'parent_id' => $parentESP->id,
                    ],
                    [
                        'name' => $child['name'],
                        'description' => $child['name'] . ' for ' . $gradeLevel . ' (Component of ESP)',
                        'is_active' => true,
                    ]
                );
            }
        }
        
        $courses = array_filter($courses, function ($course) {
            return !str_starts_with($course['code'], 'MAPEH') || str_contains($course['code'], '_');
        });
    }
}