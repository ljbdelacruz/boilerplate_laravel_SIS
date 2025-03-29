<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Student;
use App\Models\SchoolYear;
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
                'role' => 'teacher'
            ],
            [
                'name' => 'Student User',
                'email' => 'student@example.com',
                'password' => 'Password@1',
                'role' => 'student'
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

            // Create student record for users with student role
            if ($userData['role'] === 'student') {
                $schoolYear = SchoolYear::first();
                
                if ($schoolYear) {
                    Student::updateOrCreate(
                        ['user_id' => $user->id],
                        [
                            'student_id' => 'STU' . str_pad($user->id, 6, '0', STR_PAD_LEFT),
                            'first_name' => explode(' ', $userData['name'])[0],
                            'last_name' => explode(' ', $userData['name'])[1] ?? '',
                            'birth_date' => '2000-01-01',
                            'gender' => 'other',
                            'address' => 'Default Address',
                            'guardian_name' => 'Default Guardian',
                            'guardian_contact' => '1234567890',
                            'school_year_id' => $schoolYear->id,
                        ]
                    );
                }
            }
        }
    }
}
