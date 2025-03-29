<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;

class StudentCourseController extends Controller
{
    public function available()
    {
        $courses = Course::where('is_active', true)->get();
        return view('student.courses.available', compact('courses'));
    }

    public function enrolled()
    {
        $student = Student::where('user_id', Auth::id())->first();
        
        if (!$student) {
            return redirect()->route('dashboard')->with('error', 'Student profile not found.');
        }

        $enrolledCourses = $student->courses;
        return view('student.courses.enrolled', compact('enrolledCourses'));
    }

    public function enroll(Course $course)
    {
        $student = Student::where('user_id', Auth::id())->first();
        
        if (!$student) {
            return back()->with('error', 'Student profile not found.');
        }
        
        if ($student->courses()->where('course_id', $course->id)->exists()) {
            return back()->with('error', 'You are already enrolled in this course.');
        }

        $student->courses()->attach($course->id, [
            'school_year_id' => $student->school_year_id,
            'status' => 'enrolled',
            'amount_paid' => 0
        ]);

        return back()->with('success', 'Successfully enrolled in the course.');
    }
}