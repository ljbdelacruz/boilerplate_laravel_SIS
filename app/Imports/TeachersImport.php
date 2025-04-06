<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Teacher;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class TeachersImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return DB::transaction(function () use ($row) {
            $user = User::create([
                'name' => $row['name'],
                'email' => $row['email'],
                'password' => Hash::make($row['contact_number']), // Using contact number as initial password
                'role' => 'teacher'
            ]);

            return Teacher::create([
                'user_id' => $user->id,
                'specialization' => $row['specialization'],
                'bio' => $row['bio'] ?? null,
                'contact_number' => $row['contact_number']
            ]);
        });
    }
}