<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Course;
use App\Models\Schedule;
use App\Models\SchoolYear;
use App\Models\Teacher;
use App\Traits\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TeacherCourseController extends Controller
{
    use ActivityLogger;
    public function index()
    {
        $assignments = Schedule::with(['teacher', 'course', 'schoolYear'])
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        return view('teacher-courses.index', compact('assignments'));
    }

    public function create()
    {
        $teachers = Teacher::with('user')->get();
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
            // Check if teacher is already assigned to this course in the same school year
            $existingAssignment = Schedule::where('teacher_id', $validated['teacher_id'])
                ->where('course_id', $validated['course_id'])
                ->where('school_year_id', $validated['school_year_id'])
                ->first();

            if ($existingAssignment) {
                throw new \Exception('Teacher is already assigned to this course for the selected school year');
            }

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


            // Create schedule first
            $schedule = Schedule::create($validated);

            // Then create teacher-course relationship
            $teacher = User::findOrFail($validated['teacher_id']);
            $teacher->teachingCourses()->attach($validated['course_id'], [
                'school_year_id' => $validated['school_year_id']
            ]);

            // Log the activity
            $this->logActivity(
                'create',
                'Created new teacher course assignment',
                'teacher_courses',
                null,
                array_merge($schedule->toArray(), [
                    'teacher_name' => $teacher->name,
                    'course_name' => Course::find($validated['course_id'])->name
                ])
            );

            DB::commit();

            return redirect()->route('teacher-courses.index')
                ->with('success', 'Teacher assigned to course successfully');

        } catch (\Exception $e) {
            DB::rollback();
            
            $this->logActivity(
                'create',
                'Warning: Failed to create teacher course assignment',
                'teacher_courses',
                null,
                $validated,
                'error'
            );

            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function edit(Schedule $teacherCourse)
    {
        $teachers = Teacher::with('user')->get();
        $courses = Course::where('is_active', true)->get();
        $schoolYears = SchoolYear::all();

        return view('teacher-courses.edit', compact('teacherCourse', 'teachers', 'courses', 'schoolYears'));
    }

    public function update(Request $request, Schedule $teacherCourse)
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
            // Store old data for logging
            $oldData = array_merge($teacherCourse->toArray(), [
                'teacher_name' => User::find($teacherCourse->teacher_id)->name,
                'course_name' => Course::find($teacherCourse->course_id)->name
            ]);

            // ...existing validation code...

            // Update teacher-course relationship
            $teacher = User::findOrFail($validated['teacher_id']);
            $teacher->teachingCourses()->detach($teacherCourse->course_id);
            $teacher->teachingCourses()->attach($validated['course_id'], [
                'school_year_id' => $validated['school_year_id']
            ]);

            // Update schedule
            $teacherCourse->update($validated);

            // Log the activity with old and new data
            $this->logActivity(
                'update',
                'Updated teacher course assignment',
                'teacher_courses',
                $oldData,
                array_merge($teacherCourse->fresh()->toArray(), [
                    'teacher_name' => $teacher->name,
                    'course_name' => Course::find($validated['course_id'])->name
                ])
            );

            DB::commit();

            return redirect()->route('teacher-courses.index')
                ->with('success', 'Teacher course assignment updated successfully');

        } catch (\Exception $e) {
            DB::rollback();
            
            $this->logActivity(
                'update',
                'Warning: Failed to update teacher course assignment',
                'teacher_courses',
                $oldData,
                $validated,
                'error'
            );

            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }
}