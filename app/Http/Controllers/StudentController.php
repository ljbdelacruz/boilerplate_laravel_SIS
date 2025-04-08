<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use App\Models\Section;
use App\Models\SchoolYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::with(['user', 'section', 'schoolYear'])
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(10);
        return view('students.index', compact('students'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email',
            'student_id' => 'required|string|unique:students,student_id',
            'section_id' => 'required|exists:sections,id',
            'grade_level' => 'required|string|exists:grade_levels,grade_level',
            'school_year_id' => 'required|exists:school_years,id',
            'birth_date' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'contact_number' => 'required|string',
            'guardian_name' => 'required|string',
            'guardian_contact' => 'required|string'
        ]);

        DB::transaction(function () use ($validated) {
            // Extract numeric value from grade level string
            $gradeLevel = (int) filter_var($validated['grade_level'], FILTER_SANITIZE_NUMBER_INT);
            
            $user = User::create([
                'name' => $validated['first_name'] . ' ' . $validated['last_name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['student_id']),
                'role' => 'student'
            ]);

            Student::create([
                'user_id' => $user->id,
                'student_id' => $validated['student_id'],
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'middle_name' => $validated['middle_name'],
                'birth_date' => $validated['birth_date'],
                'gender' => strtolower($validated['gender']),
                'contact_number' => $validated['contact_number'],
                'guardian_name' => $validated['guardian_name'],
                'guardian_contact' => $validated['guardian_contact'],
                'section_id' => $validated['section_id'],
                'grade_level' => $gradeLevel,
                'school_year_id' => $validated['school_year_id']
            ]);
        });

        return redirect()->route('students.index')
            ->with('success', 'Student registered successfully');
    }

    public function show(Student $student)
    {
        $student->load(['user', 'section', 'schoolYear']);
        return view('students.show', compact('student'));
    }

    public function create()
    {
        return redirect()->route('users.create', ['preset_role' => 'student']);
    }

    public function edit(Student $student)
    {
        $sections = Section::where('is_active', true)->get();
        $schoolYears = SchoolYear::where('is_active', true)->get();
        return view('admin.students.edit', compact('student', 'sections', 'schoolYears'));
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $student->user_id,
            'section_id' => 'required|exists:sections,id',
            'grade_level' => 'required|integer|between:7,12',
            'school_year_id' => 'required|exists:school_years,id'
        ]);

        DB::transaction(function () use ($validated, $student) {
            $student->user->update([
                'name' => $validated['name'],
                'email' => $validated['email']
            ]);

            $student->update([
                'section_id' => $validated['section_id'],
                'grade_level' => $validated['grade_level'],
                'school_year_id' => $validated['school_year_id']
            ]);
        });

        return redirect()->route('admin.students.index')
            ->with('success', 'Student updated successfully');
    }

    public function destroy(Student $student)
    {
        DB::transaction(function () use ($student) {
            $student->user->delete();
            $student->delete();
        });

        return redirect()->route('admin.students.index')
            ->with('success', 'Student deleted successfully');
    }

    public function resetPassword(Student $student)
    {
        $student->user->update([
            'password' => Hash::make($student->lrn)
        ]);

        return redirect()->route('admin.students.index')
            ->with('success', 'Student password reset to LRN successfully');
    }
}