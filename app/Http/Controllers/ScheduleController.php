<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Teacher;
use App\Models\Course;
use App\Models\Section;
use App\Models\User;
use App\Models\SchoolYear;  // Add this line
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index()
    {
        $schedules = Schedule::with(['teacher', 'course', 'section', 'schoolYear'])->get();
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
        $teachers = User::where('role', 'teacher')->get();
        $courses = Course::all();
        $sections = Section::where('is_active', true)->get();
        $schoolYears = SchoolYear::where('is_active', true)->get();  // Add this line
        return view('schedules.create', compact('teachers', 'courses', 'sections', 'schoolYears'));
    }

    public function edit(Schedule $schedule)
    {
        $teachers = User::where('role', 'teacher')->get();
        $courses = Course::all();
        $sections = Section::where('is_active', true)->get();
        $schoolYears = SchoolYear::where('is_active', true)->get();
        return view('schedules.edit', compact('schedule', 'teachers', 'courses', 'sections', 'schoolYears'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'teacher_id' => 'required|exists:users,id',
            'course_id' => 'required|exists:courses,id',
            'section_id' => 'required|exists:sections,id',
            'school_year_id' => 'required|exists:school_years,id',  // Add this line
            'days' => 'required|array',
            'days.*' => 'in:Monday,Tuesday,Wednesday,Thursday,Friday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        foreach ($validated['days'] as $day) {
            Schedule::create([
                'teacher_id' => $validated['teacher_id'],
                'course_id' => $validated['course_id'],
                'section_id' => $validated['section_id'],
                'school_year_id' => $validated['school_year_id'],  // Add this line
                'day_of_week' => $day,
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
            ]);
        }

        return redirect()->route('schedules.index')->with('success', 'Schedules created successfully');
    }

    public function manage()
    {
        $schedules = Schedule::with(['teacher', 'course', 'section'])->get();
        return view('schedules.manage', compact('schedules'));
    }

    public function update(Request $request, Schedule $schedule)
    {
        try {
            $validated = $request->validate([
                'teacher_id' => 'required|exists:users,id',
                'course_id' => 'required|exists:courses,id',
                'section_id' => 'required|exists:sections,id',
                'school_year_id' => 'required|exists:school_years,id',
                'day_of_week' => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i|after:start_time',
            ]);

            $schedule->update($validated);

            return redirect()->route('schedules.index')
                ->with('success', 'Schedule updated successfully');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to update schedule: ' . $e->getMessage()]);
        }
    }

    public function destroy(Schedule $schedule)
    {
        $schedule->delete();
        return redirect()->route('schedules.index')->with('success', 'Schedule deleted successfully');
    }
}
