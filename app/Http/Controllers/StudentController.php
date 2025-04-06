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
            ->orderBy('grade_level')
            ->get();
        return view('admin.students.index', compact('students'));
    }

    public function create()
    {
        $sections = Section::where('is_active', true)->get();
        $schoolYears = SchoolYear::where('is_active', true)->get();
        return view('admin.students.create', compact('sections', 'schoolYears'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'lrn' => 'required|string|size:12|unique:students,lrn',
            'section_id' => 'required|exists:sections,id',
            'grade_level' => 'required|integer|between:7,12',
            'school_year_id' => 'required|exists:school_years,id'
        ]);

        DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['lrn']),
                'role' => 'student'
            ]);

            Student::create([
                'user_id' => $user->id,
                'lrn' => $validated['lrn'],
                'section_id' => $validated['section_id'],
                'grade_level' => $validated['grade_level'],
                'school_year_id' => $validated['school_year_id']
            ]);
        });

        return redirect()->route('admin.students.index')
            ->with('success', 'Student registered successfully');
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