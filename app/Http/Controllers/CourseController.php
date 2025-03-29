<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::where('is_active', true)->get();
        return view('courses.index', compact('courses'));
    }

    public function create()
    {
        return view('courses.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|unique:courses,code',
            'name' => 'required',
            'description' => 'nullable',
            'price' => 'required|numeric|min:0',
        ]);

        Course::create($validated);
        return redirect()->route('courses.index')->with('success', 'Course created successfully');
    }

    public function edit(Course $course)
    {
        return view('courses.edit', compact('course'));
    }

    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'code' => 'required|unique:courses,code,' . $course->id,
            'name' => 'required',
            'description' => 'nullable',
            'price' => 'required|numeric|min:0',
        ]);

        $course->update($validated);
        return redirect()->route('courses.index')->with('success', 'Course updated successfully');
    }

    public function archive(Course $course)
    {
        $course->update(['is_active' => false]);
        return redirect()->route('courses.index')->with('success', 'Course archived successfully');
    }
}