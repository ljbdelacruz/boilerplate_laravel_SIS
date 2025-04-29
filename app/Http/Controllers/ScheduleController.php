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

    public function generate()
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
            'school_year_id' => 'required|exists:school_years,id',
            'days' => 'required|array',
            'days.*' => 'in:Monday,Tuesday,Wednesday,Thursday,Friday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        foreach ($validated['days'] as $day) {
            // Check for conflicts
            $conflict = Schedule::where('day_of_week', $day)
                ->where('school_year_id', $validated['school_year_id'])
                ->where(function ($query) use ($validated) {
                    $query->where(function ($q) use ($validated) {
                        $q->where('start_time', '<', $validated['end_time'])
                            ->where('end_time', '>', $validated['start_time']);
                    });
                })
                ->where(function ($query) use ($validated) {
                    $query->where('teacher_id', $validated['teacher_id'])
                        ->orWhere('section_id', $validated['section_id']);
                })
                ->exists();

            if ($conflict) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['error' => 'Schedule conflict detected for the selected day and time.']);
            }

            // Create the schedule if no conflict
            Schedule::create([
                'teacher_id' => $validated['teacher_id'],
                'course_id' => $validated['course_id'],
                'section_id' => $validated['section_id'],
                'school_year_id' => $validated['school_year_id'],
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
        $validated = $request->validate([
            'teacher_id' => 'required|exists:users,id',
            'course_id' => 'required|exists:courses,id',
            'section_id' => 'required|exists:sections,id',
            'school_year_id' => 'required|exists:school_years,id',
            'day_of_week' => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        // Check for conflicts
        $conflict = Schedule::where('day_of_week', $validated['day_of_week'])
            ->where('school_year_id', $validated['school_year_id'])
            ->where(function ($query) use ($validated) {
                $query->where(function ($q) use ($validated) {
                    $q->where('start_time', '<', $validated['end_time'])
                        ->where('end_time', '>', $validated['start_time']);
                });
            })
            ->where(function ($query) use ($validated) {
                $query->where('teacher_id', $validated['teacher_id'])
                    ->orWhere('section_id', $validated['section_id']);
            })
            ->where('id', '!=', $schedule->id) // Exclude the current schedule
            ->exists();

        if ($conflict) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Schedule conflict detected for the selected day and time.']);
        }

        // Update the schedule if no conflict
        $schedule->update($validated);

        return redirect()->route('schedules.index')
            ->with('success', 'Schedule updated successfully');
    }

    public function destroy(Schedule $schedule)
    {
        $schedule->delete();
        return redirect()->route('schedules.index')->with('success', 'Schedule deleted successfully');
    }

    public function autoGenerateForm()
{
    $schoolYears = \App\Models\SchoolYear::all();
    $sections = \App\Models\Section::all();
    $teachers = \App\Models\User::where('role', 'teacher')->get();

    return view('schedules.auto_generate', compact('schoolYears', 'sections', 'teachers'));
}

public function autoGenerate(Request $request)
{
    $validated = $request->validate([
        'school_year_id' => 'required|exists:school_years,id',
        'section_id' => 'required|exists:sections,id',
        'teacher_id' => 'required|exists:users,id',
    ]);

    $section = \App\Models\Section::findOrFail($validated['section_id']);
    $teacher = \App\Models\User::findOrFail($validated['teacher_id']);
    $schoolYear = \App\Models\SchoolYear::findOrFail($validated['school_year_id']);

    // Get all curriculums for this section
    $curriculums = $section->curriculums()->with('subject')->get();

    foreach ($curriculums as $curriculum) {
        // Parse time (e.g. "08:00 AM - 09:00 AM")
        [$start, $end] = array_map('trim', explode('-', $curriculum->time));
        $start_time = date('H:i', strtotime($start));
        $end_time = date('H:i', strtotime($end));

        // You can set a default day or customize as needed
        $day_of_week = 'Monday';

        // Check for existing schedule to avoid duplicates
        $exists = \App\Models\Schedule::where([
            'teacher_id' => $teacher->id,
            'course_id' => $curriculum->subject_id,
            'section_id' => $section->id,
            'school_year_id' => $schoolYear->id,
            'day_of_week' => $day_of_week,
            'start_time' => $start_time,
            'end_time' => $end_time,
        ])->exists();

        if (!$exists) {
            \App\Models\Schedule::create([
                'teacher_id' => $teacher->id,
                'course_id' => $curriculum->subject_id,
                'section_id' => $section->id,
                'school_year_id' => $schoolYear->id,
                'day_of_week' => $day_of_week,
                'start_time' => $start_time,
                'end_time' => $end_time,
            ]);
        }
    }

    return redirect()->route('schedules.index')->with('success', 'Schedules auto-generated for section and teacher!');
    }
}
