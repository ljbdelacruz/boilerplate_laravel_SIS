<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Teacher;
use App\Models\Course;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index()
    {
        $schedules = Schedule::with(['teacher', 'course'])->get();
        return view('schedules.index', compact('schedules'));
    }

    public function showGenerateForm()
    {
        $teachers = Teacher::all();
        $courses = Course::all();
        return view('schedules.generate', compact('teachers', 'courses'));
    }

    public function generateSchedule(Request $request)
    {
        // Schedule generation logic will go here
        return redirect()->route('schedules.index')->with('success', 'Schedule generated successfully');
    }

    public function create()
    {
        $teachers = Teacher::all();
        $courses = Course::all();
        return view('schedules.create', compact('teachers', 'courses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'course_id' => 'required|exists:courses,id',
            'day' => 'required|string',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        Schedule::create($validated);
        return redirect()->route('schedules.index')->with('success', 'Schedule created successfully');
    }

    public function manage()
    {
        $schedules = Schedule::with(['teacher', 'course'])->get();
        return view('schedules.manage', compact('schedules'));
    }

    public function edit(Schedule $schedule)
    {
        $teachers = Teacher::all();
        $courses = Course::all();
        return view('schedules.edit', compact('schedule', 'teachers', 'courses'));
    }

    public function update(Request $request, Schedule $schedule)
    {
        $validated = $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'course_id' => 'required|exists:courses,id',
            'day' => 'required|string',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        $schedule->update($validated);
        return redirect()->route('schedules.index')->with('success', 'Schedule updated successfully');
    }

    public function destroy(Schedule $schedule)
    {
        $schedule->delete();
        return redirect()->route('schedules.index')->with('success', 'Schedule deleted successfully');
    }
}
