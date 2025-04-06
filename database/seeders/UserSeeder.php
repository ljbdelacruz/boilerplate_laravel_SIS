<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\SchoolYear;
use App\Models\Section;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Lainel John',
                'email' => 'ljbdelacruz123@gmail.com',
                'password' => 'Password@1',
                'role' => 'admin'
            ],
            [
                'name' => 'Teacher User',
                'email' => 'teacher@example.com',
                'password' => 'Password@1',
                'role' => 'teacher',
                'teacher_data' => [
                    'specialization' => 'Mathematics',
                    'bio' => 'Experienced mathematics teacher',
                    'contact_number' => '09123456789'
                ]
            ],
            [
                'name' => 'Student User',
                'email' => 'student@example.com',
                'password' => 'Password@1',
                'role' => 'student',
                'student_data' => [
                    'lrn' => '123456789012',
                    'grade_level' => 7
                ]
            ],
            [
                'name' => 'Regular User',
                'email' => 'user@example.com',
                'password' => 'Password@1',
                'role' => 'user'
            ]
        ];

        foreach ($users as $userData) {
            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make($userData['password']),
                    'role' => $userData['role']
                ]
            );

            if ($userData['role'] === 'teacher' && isset($userData['teacher_data'])) {
                Teacher::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'specialization' => $userData['teacher_data']['specialization'],
                        'bio' => $userData['teacher_data']['bio'],
                        'contact_number' => $userData['teacher_data']['contact_number']
                    ]
                );
            }

            if ($userData['role'] === 'student') {
                $schoolYear = SchoolYear::first();
                $section = Section::where('grade_level', $userData['student_data']['grade_level'])->first();
                
                if ($schoolYear) {
                    Student::updateOrCreate(
                        ['user_id' => $user->id],
                        [
                            'student_id' => $userData['student_data']['lrn'], // Changed from lrn to student_id
                            'grade_level' => $userData['student_data']['grade_level'],
                            'section_id' => $section ? $section->id : null,
                            'school_year_id' => $schoolYear->id,
                            'first_name' => explode(' ', $userData['name'])[0],
                            'last_name' => explode(' ', $userData['name'])[1] ?? '',
                            'middle_name' => null,
                            'birth_date' => '2000-01-01',
                            'gender' => 'other',
                            'address' => 'Default Address',
                            'contact_number' => '09123456789',
                            'guardian_name' => 'Default Guardian',
                            'guardian_contact' => '1234567890'
                        ]
                    );
                }
            }
        }
    }
}
