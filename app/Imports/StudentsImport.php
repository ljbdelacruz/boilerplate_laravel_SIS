<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Student;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StudentsImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        try {
            return DB::transaction(function () use ($row) {
                // Format the date properly
                $birthDate = is_string($row['birth_date']) 
                    ? Carbon::createFromFormat('Y-m-d', $row['birth_date'])->format('Y-m-d')
                    : Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['birth_date']))->format('Y-m-d');

                $user = User::create([
                    'name' => $row['name'],
                    'email' => $row['email'],
                    'password' => Hash::make($row['student_id']),
                    'role' => 'student'
                ]);

                $student = Student::create([
                    'user_id' => $user->id,
                    'student_id' => $row['student_id'],
                    'first_name' => $row['first_name'],
                    'last_name' => $row['last_name'],
                    'middle_name' => $row['middle_name'] ?? null,
                    'birth_date' => $birthDate,
                    'gender' => strtolower($row['gender']),
                    'address' => $row['address'],
                    'contact_number' => $row['contact_number'],
                    'guardian_name' => $row['guardian_name'],
                    'guardian_contact' => $row['guardian_contact'],
                    'grade_level' => $row['grade_level'],
                    'section_id' => (int)$row['section_id'],
                    'school_year_id' => (int)$row['school_year_id']
                ]);

                return $student;
            });
        } catch (\Exception $e) {
            throw new \Exception("Error in row: " . json_encode($row) . " Error: " . $e->getMessage());
        }
    }

    public function rules(): array
    {
        return [
            'student_id' => 'required|unique:students,student_id',
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'first_name' => 'required',
            'last_name' => 'required',
            'birth_date' => 'required',
            'gender' => 'required|in:male,female,Male,Female',
            'grade_level' => 'required|numeric',
            'section_id' => 'required|integer|exists:sections,id',
            'school_year_id' => 'required|integer|exists:school_years,id'
        ];
    }
}