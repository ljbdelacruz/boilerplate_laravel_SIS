<?php

namespace Database\Seeders;

use App\Models\User;
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
            User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make($userData['password']),
                    'role' => $userData['role']
                ]
            );
        }
    }
}
