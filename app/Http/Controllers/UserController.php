<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Teacher;
use App\Models\Section;
use App\Models\SchoolYear;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\StudentsImport;
use App\Imports\TeachersImport;

class UserController extends Controller
{
    public function index()
    {
        $users = User::paginate(10);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $sections = Section::where('is_active', true)->get();
        $schoolYears = SchoolYear::where('is_active', true)
                                ->orderBy('start_year', 'desc')
                                ->get();
        $gradeLevels = \App\Models\GradeLevel::orderBy('grade_level')->get();
        return view('users.create', compact('sections', 'schoolYears', 'gradeLevels'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,teacher,student',
            // Teacher-specific validation
            'specialization' => 'nullable|string|max:255|required_if:role,teacher',
            'bio' => 'nullable|string',
            'contact_number' => 'nullable|string|max:20',
            // Student-specific validation
            'first_name' => 'required_if:role,student|string|max:255',
            'last_name' => 'required_if:role,student|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'student_id' => 'required_if:role,student|string|unique:students,student_id',
            'section_id' => 'required_if:role,student|exists:sections,id',
            'grade_level' => 'required_if:role,student|string|exists:grade_levels,grade_level',
            'school_year_id' => 'required_if:role,student|exists:school_years,id',
            'birth_date' => 'required_if:role,student|date',
            'gender' => 'required_if:role,student|in:male,female,other',
            'guardian_name' => 'required_if:role,student|string',
            'guardian_contact' => 'required_if:role,student|string',
            // Add address validation for students
            'address' => 'required_if:role,student|string|max:255',
        ]);

        DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $validated['role'] === 'student' 
                    ? $validated['first_name'] . ' ' . $validated['last_name']
                    : $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role']
            ]);

            if ($validated['role'] === 'teacher') {
                Teacher::create([
                    'user_id' => $user->id,
                    'specialization' => $validated['specialization'],
                    'bio' => $validated['bio'] ?? null,
                    'contact_number' => $validated['contact_number']
                ]);
            } elseif ($validated['role'] === 'student') {
                $gradeLevel = (int) filter_var($validated['grade_level'], FILTER_SANITIZE_NUMBER_INT);
                
                Student::create([
                    'user_id' => $user->id,
                    'student_id' => $validated['student_id'],
                    'first_name' => $validated['first_name'],
                    'last_name' => $validated['last_name'],
                    'middle_name' => $validated['middle_name'],
                    'birth_date' => $validated['birth_date'],
                    'gender' => strtolower($validated['gender']),
                    'contact_number' => $validated['contact_number'] ?? null,
                    'guardian_name' => $validated['guardian_name'],
                    'guardian_contact' => $validated['guardian_contact'],
                    'address' => $validated['address'],
                    'section_id' => $validated['section_id'],
                    'grade_level' => $gradeLevel,
                    'school_year_id' => $validated['school_year_id']
                ]);
            }
        });

        return redirect()->route('users.index')->with('success', 'User created successfully');
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'role' => 'required|in:admin,teacher,student'
        ]);

        $user->update($validated);
        return redirect()->route('users.index')->with('success', 'User updated successfully');
    }

    public function uploadStudentsForm()
    {
        return view('users.upload-students');
    }

    public function uploadTeachersForm()
    {
        return view('users.upload-teachers');
    }

    public function uploadStudents(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        try {
            DB::beginTransaction();
            Excel::import(new StudentsImport, $request->file('file'));
            DB::commit();
            return redirect()->route('users.index')->with('success', 'Students imported successfully');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            DB::rollBack();
            $failures = $e->failures();
            $errors = [];
            foreach ($failures as $failure) {
                $errors[] = "Row {$failure->row()}: {$failure->errors()[0]}";
            }
            return back()->with('error', 'Validation errors in Excel file: ' . implode(', ', $errors));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error importing students: ' . $e->getMessage());
        }
    }

    public function uploadTeachers(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        try {
            DB::beginTransaction();
            Excel::import(new TeachersImport, $request->file('file'));
            DB::commit();
            return redirect()->route('users.index')->with('success', 'Teachers imported successfully');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            DB::rollBack();
            $failures = $e->failures();
            $errors = [];
            foreach ($failures as $failure) {
                $errors[] = "Row {$failure->row()}: {$failure->errors()[0]}";
            }
            return back()->with('error', 'Validation errors in Excel file: ' . implode(', ', $errors));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error importing teachers: ' . $e->getMessage());
        }
    }
}