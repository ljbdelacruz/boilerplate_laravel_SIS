<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\SchoolYear;
use Illuminate\Http\Request;

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
            'school_year_id' => 'required|exists:school_years,id'
        ]);

        Student::create($validated);
        return redirect()->route('students.index')->with('success', 'Student added successfully');
    }
}