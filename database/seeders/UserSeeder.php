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
                'name' => 'John Smith',
                'email' => 'john.smith@example.com',
                'password' => 'Password@1',
                'role' => 'student',
                'student_data' => [
                    'lrn' => '202400001',
                    'grade_level' => 7,
                    'first_name' => 'John',
                    'last_name' => 'Smith',
                    'birth_date' => '2010-05-15',
                    'gender' => 'Male',
                    'contact_number' => '09123456781'
                ]
            ],
            [
                'name' => 'Maria Garcia',
                'email' => 'maria.garcia@example.com',
                'password' => 'Password@1',
                'role' => 'student',
                'student_data' => [
                    'lrn' => '202400002',
                    'grade_level' => 7,
                    'first_name' => 'Maria',
                    'last_name' => 'Garcia',
                    'birth_date' => '2010-06-20',
                    'gender' => 'Female',
                    'contact_number' => '09123456782'
                ]
            ],
            [
                'name' => 'James Wilson',
                'email' => 'james.wilson@example.com',
                'password' => 'Password@1',
                'role' => 'student',
                'student_data' => [
                    'lrn' => '202400003',
                    'grade_level' => 8,
                    'first_name' => 'James',
                    'last_name' => 'Wilson',
                    'birth_date' => '2009-03-10',
                    'gender' => 'Male',
                    'contact_number' => '09123456783'
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
                $section = Section::where('grade_level', $userData['student_data']['grade_level'])
                                ->where('name', 'Section A')
                                ->first();
                
                if ($schoolYear && $section) {
                    Student::updateOrCreate(
                        ['user_id' => $user->id],
                        [
                            'student_id' => $userData['student_data']['lrn'],
                            'grade_level' => $userData['student_data']['grade_level'],
                            'section_id' => $section->id,
                            'school_year_id' => $schoolYear->id,
                            'first_name' => $userData['student_data']['first_name'],
                            'last_name' => $userData['student_data']['last_name'],
                            'middle_name' => null,
                            'birth_date' => $userData['student_data']['birth_date'],
                            'gender' => $userData['student_data']['gender'],
                            'address' => 'Default Address',
                            'contact_number' => $userData['student_data']['contact_number'],
                            'guardian_name' => 'Parent/Guardian',
                            'guardian_contact' => '09xxxxxxxxx'
                        ]
                    );
                }
            }
        }
    }
}
