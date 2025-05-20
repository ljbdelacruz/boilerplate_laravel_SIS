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
use Illuminate\Validation\Rule;

class StudentsImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows, WithColumnFormatting
{
    
    public function prepareForValidation(array $data, int $index): array
    {
        $data['lrn'] = isset($data['lrn']) ? (string) $data['lrn'] : null;
        $data['student_id'] = isset($data['student_id']) ? (string) $data['student_id'] : null; 
        $data['contact_number'] = isset($data['contact_number']) ? (string) $data['contact_number'] : null;
        $data['guardian_contact'] = isset($data['guardian_contact']) ? (string) $data['guardian_contact'] : null;
        $data['grade_level'] = isset($data['grade_level']) ? (string) $data['grade_level'] : null;
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
                    throw new \Exception("School year '{$schoolYearString}' not found in the database.");
                }

                // Check if the section exists for the given school year
                $sectionName = $row['section_name'];
                $gradeLevel = $row['grade_level'];
                $section = Section::where('name', $sectionName)
                    ->where('school_year_id', $schoolYear->id)
                    ->where('grade_level', $gradeLevel) 
                    ->first();

                if (!$section) {
                    throw new \Exception("Section '{$sectionName}' not found for school year '{$schoolYearString}' in the database for row.");
                }
                $birthDate = null;
                if (isset($row['birth_date'])) {
                     if (is_numeric($row['birth_date'])) {
                        try {
                            $birthDate = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['birth_date']))->format('Y-m-d');
                        } catch (\Exception $e) {
                             throw new \Exception("Invalid Excel date format for birth_date in row.");
                        }
                    } elseif (is_string($row['birth_date'])) {
                         try {
                             $birthDate = Carbon::createFromFormat('Y-m-d', $row['birth_date'])->format('Y-m-d');
                         } catch (\Exception $e) {
                             // Try other common formats if Y-m-d fails
                             try {
                                $birthDate = Carbon::parse($row['birth_date'])->format('Y-m-d');
                             } catch (\Exception $e2) {
                                 throw new \Exception("Invalid string date format for birth_date '{$row['birth_date']}' in row. Expected YYYY-MM-DD or a valid date format.");
                             }
                         }
                    } else {
                         throw new \Exception("Invalid data type for birth_date in row. Expected number (Excel date) or string.");
                    }
                }
                $student = Student::where('lrn', $row['lrn'])
                    ->orWhere('student_id', $row['student_id'])
                    ->first();

                    if ($student) {
                        $user = $student->user;
                    $student->update([
                        'first_name' => $row['first_name'],
                        'last_name' => $row['last_name'],
                        'middle_name' => $row['middle_name'] ?? null,
                        'birth_date' => $birthDate,
                        'gender' => strtolower($row['gender']),
                        'address' => $row['address'],
                        'contact_number' => $row['contact_number'],
                        'guardian_name' => $row['guardian_name'],
                        'guardian_contact' => $row['guardian_contact'],
                        'grade_level' => $gradeLevel,
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
                        'role' => 'student',
                        // 'email' => $row['email'] ?? ..., // Use the email from the row if provided
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
                'string', 
                'digits:12', 
                Rule::unique('students', 'lrn'),
            ],
            'student_id' => [
                'required',
                'string', 
                Rule::unique('students', 'student_id'),
                'numeric'
            ],
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255', 
            'birth_date' => 'required', 
            'gender' => 'required|string|in:male,female,Male,Female',
            'grade_level' => [
                'required',
                'string',
                Rule::in(['Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6']), 
            ],
            'section_name' => [
                'required',
                'string',
                // Rule::exists('sections', 'name'), // This checks existence across ALL sections, not just for the specific school year
            ],
            'school_year' => [
                'required',
                'string', 
                'regex:/^\d{4}-\d{4}$/', 
            ],
            'address' => 'required|string|max:255',
            'contact_number' => 'required|string|max:25', 
            'guardian_name' => 'required|string|max:255',
            'guardian_contact' => 'required|string|max:25', 
            'email' => 'nullable|string|email|max:255', 
        ];
    }

    public function columnFormats(): array
    {
        return [
            'lrn' => NumberFormat::FORMAT_TEXT,
            'student_id' => NumberFormat::FORMAT_TEXT, 
            'contact_number' => NumberFormat::FORMAT_TEXT, 
            'guardian_contact' => NumberFormat::FORMAT_TEXT, 
            'grade_level' => NumberFormat::FORMAT_TEXT, 
            'birth_date' => NumberFormat::FORMAT_DATE_YYYYMMDD, 
        ];
    }
}
            