<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\SchoolYear;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::with('schoolYear')->paginate(10);
        return view('students.index', compact('students'));
    }

    public function create()
    {
        $schoolYears = SchoolYear::all();
        return view('students.create', compact('schoolYears'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|unique:students',
            'first_name' => 'required',
            'last_name' => 'required',
            'birth_date' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'address' => 'required',
            'guardian_name' => 'required',
            'guardian_contact' => 'required',
            'school_year_id' => 'required|exists:school_years,id',
            'email' => 'required|email|unique:users'
        ]); // Removed password validation

        // Generate default password (student ID + first 3 letters of last name)
        $defaultPassword = $validated['student_id'] . strtolower(substr($validated['last_name'], 0, 3));

        // Create user account
        $user = User::create([
            'name' => $validated['first_name'] . ' ' . $validated['last_name'],
            'email' => $validated['email'],
            'password' => Hash::make($defaultPassword),
            'role' => 'student'
        ]);

        // Create student record
        $studentData = collect($validated)->except(['email'])->toArray();
        $studentData['user_id'] = $user->id;
        
        Student::create($studentData);

        return redirect()->route('students.index')
            ->with('success', "Student added successfully. Default password: {$defaultPassword}");
    }

    public function show(Student $student)
    {
        $student->load('schoolYear');
        return view('students.show', compact('student'));
    }

    public function edit(Student $student)
    {
        $schoolYears = SchoolYear::all();
        return view('students.edit', compact('student', 'schoolYears'));
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'student_id' => 'required|unique:students,student_id,' . $student->id,
            'first_name' => 'required',
            'last_name' => 'required',
            'birth_date' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'address' => 'required',
            'guardian_name' => 'required',
            'guardian_contact' => 'required',
            'school_year_id' => 'required|exists:school_years,id'
        ]);

        $student->update($validated);
        return redirect()->route('students.index')->with('success', 'Student updated successfully');
    }
}