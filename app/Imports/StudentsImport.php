<?php

namespace App\Imports;

use App\Models\User;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\Section;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StudentsImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows, WithColumnFormatting
{
    
    public function prepareForValidation(array $data, int $index): array
    {
        // Explicitly cast LRN to string before validation rules are applied.
        $data['lrn'] = isset($data['lrn']) ? (string) $data['lrn'] : null;
        return $data;
    }

    public function model(array $row)
    {
        try {
            return DB::transaction(function () use ($row) {
                $schoolYearString = $row['school_year']; 
                $years = explode('-', $schoolYearString);
                if (count($years) !== 2 || !ctype_digit($years[0]) || !ctype_digit($years[1])) {
                    throw new \Exception("Invalid school_year format '{$schoolYearString}' in row. Expected YYYY-YYYY.");
                }
                $startYear = (int)$years[0];
                $endYear = (int)$years[1];
                $schoolYear = SchoolYear::where('start_year', $startYear)->where('end_year', $endYear)->first();

                if (!$schoolYear) {
                    throw new \Exception("School year '{$schoolYearString}' not found in the database for row.");
                }

                // Check if the section exists for the given school year
                $sectionName = $row['section_name'];
                $section = Section::where('name', $sectionName)
                    ->where('school_year_id', $schoolYear->id)
                    ->where('grade_level', $row['grade_level'] ?? '')
                    ->first();

                if (!$section) {
                    throw new \Exception("Section '{$sectionName}' not found for school year '{$schoolYearString}' in the database for row.");
                }
                // Format the date properly
                $birthDate = is_string($row['birth_date']) 
                    ? Carbon::createFromFormat('Y-m-d', $row['birth_date'])->format('Y-m-d')
                    : Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['birth_date']))->format('Y-m-d');

                $student = Student::where('lrn', $row['lrn'])
                    ->orWhere('student_id', $row['student_id'])
                    ->first();

                    if ($student) {
                        $user = $student->user;
                         // Update Student
                    $student->update([
                        // LRN and student_id are identifiers, cannot be updated via batch
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
                        'section_id' => $section->id,
                        'school_year_id' => $schoolYear->id,
                    ]);
                    return $student; 
                } else {
                    $user = User::create([
                        'name' => $row['first_name'] . ' ' . $row['last_name'], 
                        // Generate a unique placeholder email using LRN if email column is missing or empty
                        'email' => $row['email'] ?? 
                                   (isset($row['lrn']) && !empty($row['lrn']) 
                                       ? $row['lrn'] . '@placeholder.school.edu' 
                                       : uniqid('student_') . '@example.school.edu'),
                        'password' => Hash::make($row['lrn']), 
                        'role' => 'student'
                    ]);
                    return Student::create(array_merge($row, [
                        'user_id' => $user->id,
                        'birth_date' => $birthDate,
                        'gender' => strtolower($row['gender']),
                        'section_id' => $section->id,
                        'school_year_id' => $schoolYear->id,
                    ]));
                }
            });
        } catch (\Exception $e) {
            throw new \Exception(" Error: " . $e->getMessage());
        }
    }

    public function rules(): array
    {
        return [
            'lrn' => [
                'required',
                function ($attribute, $value, $fail) {
                    // Custom validation for LRN
                    if (!is_string($value)) {
                        $fail('The LRN (' . $attribute . ') must be a string. Got type: ' . gettype($value) . ' for value: "' . $value . '". Please ensure the LRN column in Excel is formatted as Text and the header is exactly "lrn".');
                    } 
                },
            ],
            'student_id' => 'required|string',
            'first_name' => 'required',
            'last_name' => 'required',
            'birth_date' => 'required',
            'gender' => 'required|in:male,female,Male,Female',
            'grade_level' => 'required',
            // Custom validation for section_name based on school_year
            'section_name' => [
                'required',
                function ($attribute, $value, $fail) use (&$row) { 
                    $schoolYearString = $row['school_year'] ?? null;
                    if (!$schoolYearString) return; 

                    
                    $years = explode('-', $schoolYearString);
                    
                    if (count($years) !== 2 || !ctype_digit($years[0]) || !ctype_digit($years[1])) {
                        return; 
                    }
                    $startYear = (int)$years[0];
                    $endYear = (int)$years[1];

                    
                    $schoolYear = SchoolYear::where('start_year', $startYear)->where('end_year', $endYear)->first();

                    if ($schoolYear) {
                        
                        if (!Section::where('name', $value)->where('school_year_id', $schoolYear->id)->exists()) {
                        $fail("The section '{$value}' does not exist for the school year '{$schoolYearString}'.");
                    }
                }
            }
            ],
            // Custom validation for school_year
            'school_year' => [
                'required',
                'regex:/^\d{4}-\d{4}$/', 
                function ($attribute, $value, $fail) {
                    $years = explode('-', $value);
                    
                    if (count($years) !== 2 || !ctype_digit($years[0]) || !ctype_digit($years[1])) {
                        return $fail('The '.$attribute.' format is invalid. Expected YYYY-YYYY.');
                    }
                    $start_year = (int)$years[0];
                    $end_year = (int)$years[1];

                    if ($end_year <= $start_year) {
                         return $fail('The end year must be greater than the start year in '.$attribute.'.');
                    }

                    if (!SchoolYear::where('start_year', $start_year)->where('end_year', $end_year)->exists()) {
                        $fail('The specified '.$attribute.' ('.$value.') does not exist in the system.');
                    }
                }
            ],
        ];
    }
    /**
     * @return array
     */
    public function columnFormats(): array
    {
        return [
            'lrn' => NumberFormat::FORMAT_TEXT,
        ];
    }
}