<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Course;
use App\Models\Schedule;
use App\Models\SchoolYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TeacherCourseController extends Controller
{
    public function index()
    {
        $assignments = Schedule::with(['teacher', 'course'])
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        return view('teacher-courses.index', compact('assignments'));
    }

    public function create()
    {
        $teachers = User::teachers()->get();
        $courses = Course::where('is_active', true)->get();
        $schoolYears = SchoolYear::all();

        return view('teacher-courses.create', compact('teachers', 'courses', 'schoolYears'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'teacher_id' => 'required|exists:users,id',
            'course_id' => 'required|exists:courses,id',
            'school_year_id' => 'required|exists:school_years,id',
            'day_of_week' => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'notes' => 'nullable|string'
        ]);

        DB::beginTransaction();

        try {
            // Check for schedule conflicts
            $conflictingSchedule = Schedule::where('teacher_id', $validated['teacher_id'])
                ->where('day_of_week', $validated['day_of_week'])
                ->where(function ($query) use ($validated) {
                    $query->whereBetween('start_time', [$validated['start_time'], $validated['end_time']])
                        ->orWhereBetween('end_time', [$validated['start_time'], $validated['end_time']]);
                })->first();

            if ($conflictingSchedule) {
                throw new \Exception('Schedule conflicts with existing assignment');
            }

            // Create teacher-course relationship
            $teacher = User::findOrFail($validated['teacher_id']);
            $teacher->teachingCourses()->attach($validated['course_id'], [
                'school_year_id' => $validated['school_year_id']
            ]);

            // Create schedule
            Schedule::create($validated);

            DB::commit();
            Log::info('Teacher course assignment created', $validated);

            return redirect()->route('teacher-courses.index')
                ->with('success', 'Teacher assigned to course successfully');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Teacher course assignment failed', [
                'error' => $e->getMessage(),
                'data' => $validated
            ]);

            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }
}