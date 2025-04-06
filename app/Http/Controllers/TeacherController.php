<?php

namespace App\Http\Controllers;

use App\Models\SchoolYear;
use App\Models\Section;
use App\Models\Student;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
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
}