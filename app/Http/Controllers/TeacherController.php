<?php

namespace App\Http\Controllers;

use App\Models\SchoolYear;
use App\Models\Section;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function list(){
        $teachers = User::where('role', 'teacher')
                       ->orderBy('name')
                       ->get();
        
        return view('admin.teachers.index', compact('teachers'));
    }
    public function index()
    {
        $schoolYears = SchoolYear::where('is_archived', false)
                                ->orderBy('school_year', 'desc')
                                ->get();
        $sections = Section::orderBy('grade_level')->orderBy('name')->get();
        
        return view('dashboard.teacher', compact('schoolYears', 'sections'));
    }

    public function viewStudents(Request $request)
    {
        $schoolYears = SchoolYear::where('is_archived', false)
                                ->orderBy('school_year', 'desc')
                                ->get();
        $sections = Section::orderBy('grade_level')->orderBy('name')->get();
        
        $students = null;
        if ($request->filled(['school_year_id', 'section_id'])) {
            $students = Student::where('school_year_id', $request->school_year_id)
                             ->where('section_id', $request->section_id)
                             ->get();
        }
        
        return view('dashboard.teacher', compact('schoolYears', 'sections', 'students'));
    }

    public function saveGrades(Request $request, Student $student)
    {
        $validated = $request->validate([
            'prelim' => 'nullable|numeric|min:0|max:100',
            'midterm' => 'nullable|numeric|min:0|max:100',
            'prefinal' => 'nullable|numeric|min:0|max:100',
            'final' => 'nullable|numeric|min:0|max:100',
        ]);
    
        $student->grades()->updateOrCreate(
            ['student_id' => $student->id],
            $validated
        );
    
        return response()->json(['success' => true]);
    }

    public function submitGrades(Student $student)
    {
        return view('dashboard.submit-grades', [
            'student' => $student,
            'grades' => $student->grades
        ]);
    }

    public function edit(User $teacher)
    {
        return view('admin.teachers.edit', compact('teacher'));
    }

    public function update(Request $request, User $teacher)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $teacher->id,
        ]);

        $teacher->update($validated);

        return redirect()->route('teachers.index')->with('success', 'Teacher updated successfully');
    }

    public function destroy(User $teacher)
    {
        $teacher->delete();
        return redirect()->route('teachers.index')->with('success', 'Teacher deleted successfully');
    }
}