<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Student;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class StudentsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return DB::transaction(function () use ($row) {
            $user = User::create([
                'name' => $row['name'],
                'email' => $row['email'],
                'password' => Hash::make($row['student_id']), // Using student_id as initial password
                'role' => 'student'
            ]);

            return Student::create([
                'user_id' => $user->id,
                'student_id' => $row['student_id'],
                'first_name' => $row['first_name'],
                'last_name' => $row['last_name'],
                'middle_name' => $row['middle_name'] ?? null,
                'birth_date' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['birth_date'])->format('Y-m-d'),
                'gender' => strtolower($row['gender']),
                'address' => $row['address'],
                'contact_number' => $row['contact_number'] ?? null,
                'guardian_name' => $row['guardian_name'],
                'guardian_contact' => $row['guardian_contact'],
                'grade_level' => $row['grade_level'],
                'section_id' => $row['section_id'] ?? null,
                'school_year_id' => $row['school_year_id']
            ]);
        });
    }
}