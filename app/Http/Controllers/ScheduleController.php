<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Teacher;
use App\Models\Course;
use App\Models\Section;
use App\Models\Curriculum;
use App\Models\User;
use App\Models\SchoolYear;  // Add this line
use App\Traits\ActivityLogger;
use Illuminate\Http\Request;


class ScheduleController extends Controller
{
    use ActivityLogger;
    public function index()
    {
        $activeSchoolYear = SchoolYear::where('is_active', true)->first();

        if ($activeSchoolYear) {
            $schedules = Schedule::where('school_year_id', $activeSchoolYear->id)->get();
        } else {
            $schedules = collect(); // Empty collection if no active school year
        }

        return view('schedules.index', compact('schedules'));
    }

    public function showGenerateForm()
    {
        $teachers = Teacher::all();
        $courses = Course::all();
        return view('schedules.generate', compact('teachers', 'courses'));
    }

    public function generate(Request $request)
    {
        $this->logActivity('schedule', 'Generated new schedule', 'Schedule generation completed');
        
        return redirect()->route('schedules.index')->with('success', 'Schedule generated successfully');
    }
    public function autoGenerateForm()
    {
        $schoolYears = SchoolYear::where('is_active', true)->get();
        $teachers = User::where('role', 'teacher')->orderBy('name')->get();
        // Fetch active courses 
        $courses = Course::where('is_active', true)->orderBy('grade_level')->get();

        return view('schedules.auto_generate', 
        compact('schoolYears', 'courses', 'teachers'));
    
    }

    public function autoGenerate(Request $request)
    {
        $validated = $request->validate([
            'school_year_id' => 'required|exists:school_years,id',
            'course_id' => 'required|exists:courses,id',
            'teacher_id' => 'required|exists:users,id',
            'time_slot' => 'required|in:morning,afternoon', // Added validation for time_slot
        ]);

        $course = Course::findOrFail($validated['course_id']);
        $teacher = User::findOrFail($validated['teacher_id']);
        $schoolYear = SchoolYear::findOrFail($validated['school_year_id']);

        // Find all curriculum entries for the selected course and sy
        $curriculumQuery = Curriculum::where('subject_id', $course->id)
            ->whereHas('section', function ($query) use ($schoolYear) {
                $query->where('school_year_id', $schoolYear->id);
            })
            ->with(['section', 'subject']);

        // Filter by time_slot
        // Morning: 07:00:00 to 12:00:00 (inclusive)
        // Afternoon: 13:00:00 to 18:00:00 (inclusive)
        if ($validated['time_slot'] === 'morning') {
            $curriculumQuery->whereTime('start_time', '>=', '07:00:00')->whereTime('start_time', '<=', '12:00:00');
        } elseif ($validated['time_slot'] === 'afternoon') {
            $curriculumQuery->whereTime('start_time', '>=', '13:00:00')->whereTime('start_time', '<=', '18:00:00');
        }

        $curriculums = $curriculumQuery->get();

        // Check if any curriculum items were found if not, redirect with a warning
        if ($curriculums->isEmpty()) {
            $errors = [
                'error' => "No {$validated['time_slot']} curriculum entries found for the course '{$course->name} ({$course->grade_level})' in the selected school year. Cannot generate schedule."
            ];
            return redirect()->route('schedules.auto-generate-form')->with('info', $errors['error'])->withInput();
       }

        $conflicts = [];
        $schedulesToCreate = [];
        $existingCount = 0;

        foreach ($curriculums as $curriculum) {
            // Ensure the curriculum item has a section and subject linked
            if (is_null($curriculum->section) || is_null($curriculum->subject)) {
                \Log::error("Curriculum item ID {$curriculum->id} is missing section or subject relationship.");
                $conflicts[] = "Skipping item: Curriculum ID {$curriculum->id} has incomplete data.";
                continue; 
            }
            $section = $curriculum->section;
            $sectionName = $section->name;
            $start_time = $curriculum->start_time;
            $end_time = $curriculum->end_time;
            $subjectName = $curriculum->subject->name;
            $timeDisplay = \Carbon\Carbon::parse($start_time)->format('h:i A') . ' - ' . \Carbon\Carbon::parse($end_time)->format('h:i A');

            // Check for Teacher Conflict
            $teacherConflict = Schedule::where('teacher_id', $teacher->id)
                ->where('school_year_id', $schoolYear->id)
                ->where('start_time', '<', $end_time)
                ->where('end_time', '>', $start_time)
                ->where('section_id', '!=', $section->id) // Check conflict in ohter sections
                ->with(['section', 'course']) 
                ->first();

            if ($teacherConflict) {
                $conflicts[] = "Teacher Conflict for Section '{$sectionName}': {$teacher->name} is already scheduled for {$teacherConflict->course->name} in Section {$teacherConflict->section->name} during {$timeDisplay}.";
                continue; 
            }

            // Check for Section Conflict
            $sectionConflict = Schedule::where('section_id', $section->id)
                ->where('school_year_id', $schoolYear->id)
                ->where('start_time', '<', $end_time)
                ->where('end_time', '>', $start_time)
                ->with(['teacher', 'course']) 
                ->first();

            if ($sectionConflict) {
                $conflicts[] = "Section Conflict for Section '{$sectionName}': Section {$sectionName} already has {$sectionConflict->course->name} with {$sectionConflict->teacher->name} scheduled during {$timeDisplay}.";
                continue; 
            }

            // Check if this exact schedule already exists
            $exactExists = Schedule::where([
                'teacher_id' => $teacher->id,
                'course_id' => $course->id,
                'section_id' => $section->id,
                'school_year_id' => $schoolYear->id,
                'start_time' => $start_time,
                'end_time' => $end_time,
            ])->exists();

            if ($exactExists) {
                $existingCount++;
                continue; 
            }

            // If no conflicts and not existing, proceed to create the schedule
            $schedulesToCreate[] = [
                'teacher_id' => $teacher->id,
                'course_id' => $curriculum->subject_id,
                'section_id' => $section->id,
                'school_year_id' => $schoolYear->id,
                'start_time' => $start_time,
                'end_time' => $end_time,
                'created_at' => now(), 
                'updated_at' => now(), 
            ];
        
        }

        if (!empty($conflicts)) {
            // Redirect back with conflict errors messages
            return redirect()->route('schedules.auto-generate-form')
                ->withErrors(['conflict' => $conflicts])
                ->withInput();
        } elseif (!empty($schedulesToCreate)) {
            // Bulk insert the valid schedules
            Schedule::insert($schedulesToCreate);
            $message = count($schedulesToCreate) . " new schedule(s) generated successfully for Teacher '{$teacher->name}' and Course '{$course->name}'.";
            if ($existingCount > 0) {
                $message .= " {$existingCount} schedule(s) already existed.";
            }
            return redirect()->route('schedules.index')->with('success', $message);
        } else {
            // No conflicts and no new schedules to create so just redirect
            return redirect()->route('schedules.index')->with('info', 'No new schedules needed to be generated for this section and teacher.');
        }
    }

    public function create()
    {
        $activeSchoolYear = SchoolYear::where('is_active', true)->first();

        if ($activeSchoolYear) {
            $sections = Section::where('school_year_id', $activeSchoolYear->id)->get(); 
        } else {
            $sections = collect();
        }

        $teachers = User::where('role', 'teacher')->get();
        $courses = Course::all();
        $schoolYears = SchoolYear::where('is_active', true)->get();

        return view('schedules.create', compact('teachers', 'courses', 'sections', 'schoolYears'));
    }

    public function edit(Schedule $schedule)
    {
        $activeSchoolYear = SchoolYear::where('is_active', true)->first();

        if ($activeSchoolYear) {
            $sections = Section::where('school_year_id', $activeSchoolYear->id)->get(); 
        } else {
            $sections = collect();
        }

        $teachers = User::where('role', 'teacher')->get();
        $courses = Course::all();
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
            'start_time' => [
                'required',
                'date_format:H:i',
                function ($attribute, $value, $fail) {
                    try {
                        $time = \Carbon\Carbon::createFromFormat('H:i', $value);
                    } catch (\InvalidArgumentException $e) {
                        // This should be caught by 'date_format' rule, but as a safeguard:
                        // $fail('Invalid start time format.'); 
                        return;
                    }
                    $morningStart = \Carbon\Carbon::parse('07:00:00');
                    $morningEnd = \Carbon\Carbon::parse('12:00:00');
                    $afternoonStart = \Carbon\Carbon::parse('13:00:00');
                    $afternoonEnd = \Carbon\Carbon::parse('18:00:00');

                    $isValid = ($time->gte($morningStart) && $time->lte($morningEnd)) ||
                               ($time->gte($afternoonStart) && $time->lte($afternoonEnd));

                    if (!$isValid) {
                        $fail('The schedule start time must be between 7:00 AM - 12:00 PM or 1:00 PM - 6:00 PM.');
                    }
                }
            ],
            'end_time' => [
                'required',
                'date_format:H:i',
                'after:start_time',
                function ($attribute, $value, $fail) use ($request) { // Duration rule
                    $startTimeString = $request->input('start_time');
                    if (empty($startTimeString)) return; 
                    try {
                        $startTime = \Carbon\Carbon::createFromFormat('H:i', $startTimeString);
                        $endTime = \Carbon\Carbon::createFromFormat('H:i', $value);
                        if ($startTime->diffInMinutes($endTime) > 60) {
                            $fail('The schedule duration cannot exceed 60 minutes.');
                        }
                    } catch (\InvalidArgumentException $e) { return; }
                },
                function ($attribute, $value, $fail) use ($request) { // Time slot consistency rule
                    $startTimeString = $request->input('start_time');
                    if (empty($startTimeString)) return;

                    try {
                        $start = \Carbon\Carbon::createFromFormat('H:i', $startTimeString);
                        $end = \Carbon\Carbon::createFromFormat('H:i', $value);
                    } catch (\InvalidArgumentException $e) { return; }

                    $morningStart = \Carbon\Carbon::parse('07:00:00');
                    $morningEnd = \Carbon\Carbon::parse('12:00:00');
                    $afternoonStart = \Carbon\Carbon::parse('13:00:00');
                    $afternoonEnd = \Carbon\Carbon::parse('18:00:00');

                    $isEndTimeValid = ($end->gte($morningStart) && $end->lte($morningEnd)) ||
                                      ($end->gte($afternoonStart) && $end->lte($afternoonEnd));
                    if (!$isEndTimeValid) {
                        $fail('The schedule end time must be between 7:00 AM - 12:00 PM or 1:00 PM - 6:00 PM.');
                        return;
                    }

                    $isStartTimeInMorning = $start->gte($morningStart) && $start->lte($morningEnd);
                    $isEndTimeInMorning = $end->gte($morningStart) && $end->lte($morningEnd);

                    if ($isStartTimeInMorning != $isEndTimeInMorning) {
                        $fail('The schedule must be entirely within the 7 AM - 12 PM slot or the 1 PM - 6 PM slot. It cannot span across the 12 PM - 1 PM break.');
                    }
                },
            ],
        ]);
        // Check for conflicts
        $conflict = Schedule::where('school_year_id', $validated['school_year_id'])
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
                ->withErrors(['error' => 'Schedule conflict detected for the selected teacher, section, or time.']);

        }

        // Create the schedule and log the activity
         Schedule::create([
            'teacher_id' => $validated['teacher_id'],
            'course_id' => $validated['course_id'],
            'section_id' => $validated['section_id'],
            'school_year_id' => $validated['school_year_id'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
        ]);

        // Log the activity using the trait
        $teacher = User::find($validated['teacher_id']);
        $course = Course::find($validated['course_id']);
        $section = Section::find($validated['section_id']);
           
        $this->logActivity('schedule', 'Created new schedule', "Added schedule for Teacher: {$teacher->name}, Course: {$course->name}, Section: {$section->name}, " . 
               "Time: {$validated['start_time']} - {$validated['end_time']}");

        return redirect()->route('schedules.index')->with('success', 'Schedule created successfully');
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
            'start_time' => [
                'required',
                'date_format:H:i',
                function ($attribute, $value, $fail) {
                    try {
                        $time = \Carbon\Carbon::createFromFormat('H:i', $value);
                    } catch (\InvalidArgumentException $e) {
                        return;
                    }
                    $morningStart = \Carbon\Carbon::parse('07:00:00');
                    $morningEnd = \Carbon\Carbon::parse('12:00:00');
                    $afternoonStart = \Carbon\Carbon::parse('13:00:00');
                    $afternoonEnd = \Carbon\Carbon::parse('18:00:00');

                    $isValid = ($time->gte($morningStart) && $time->lte($morningEnd)) ||
                               ($time->gte($afternoonStart) && $time->lte($afternoonEnd));

                    if (!$isValid) {
                        $fail('The schedule start time must be between 7:00 AM - 12:00 PM or 1:00 PM - 6:00 PM.');
                    }
                }
            ],
            'end_time' => [
                'required',
                'date_format:H:i',
                'after:start_time',
                function ($attribute, $value, $fail) use ($request) { // Duration rule
                    $startTimeString = $request->input('start_time');
                    if (empty($startTimeString)) return;
                    try {
                        $startTime = \Carbon\Carbon::createFromFormat('H:i', $startTimeString);
                        $endTime = \Carbon\Carbon::createFromFormat('H:i', $value);
                        if ($startTime->diffInMinutes($endTime) > 60) {
                            $fail('The schedule duration cannot exceed 60 minutes.');
                        }
                    } catch (\InvalidArgumentException $e) { return; }
                },
                function ($attribute, $value, $fail) use ($request) { // Time slot consistency rule
                    $startTimeString = $request->input('start_time');
                    if (empty($startTimeString)) return;

                    try {
                        $start = \Carbon\Carbon::createFromFormat('H:i', $startTimeString);
                        $end = \Carbon\Carbon::createFromFormat('H:i', $value);
                    } catch (\InvalidArgumentException $e) { return; }

                    $morningStart = \Carbon\Carbon::parse('07:00:00');
                    $morningEnd = \Carbon\Carbon::parse('12:00:00');
                    $afternoonStart = \Carbon\Carbon::parse('13:00:00');
                    $afternoonEnd = \Carbon\Carbon::parse('18:00:00');

                    $isEndTimeValid = ($end->gte($morningStart) && $end->lte($morningEnd)) ||
                                      ($end->gte($afternoonStart) && $end->lte($afternoonEnd));
                    if (!$isEndTimeValid) {
                        $fail('The schedule end time must be between 7:00 AM - 12:00 PM or 1:00 PM - 6:00 PM.');
                        return;
                    }

                    $isStartTimeInMorning = $start->gte($morningStart) && $start->lte($morningEnd);
                    $isEndTimeInMorning = $end->gte($morningStart) && $end->lte($morningEnd);

                    if ($isStartTimeInMorning != $isEndTimeInMorning) {
                        $fail('The schedule must be entirely within the 7 AM - 12 PM slot or the 1 PM - 6 PM slot. It cannot span across the 12 PM - 1 PM break.');
                    }
                },
            ],
        ]);

        // Check for conflicts
        $conflict = Schedule::where('school_year_id', $validated['school_year_id'])
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

        $oldData = $schedule->toArray(); 

        if ($conflict) {
            return redirect()->route('schedules.edit', $schedule->id)
                ->withInput()
                ->withErrors(['error' => 'Schedule conflict detected for the selected teacher, section, or time.']);
        }

        $schedule->update($validated);
        $newData = $schedule->fresh()->toArray();

        // For a more descriptive log message
        $teacher = User::find($validated['teacher_id']);
        $course = Course::find($validated['course_id']);
        $section = Section::find($validated['section_id']);
        $description = "Updated schedule for Teacher: " . ($teacher ? $teacher->name : 'N/A') .
                       ", Course: " . ($course ? $course->name : 'N/A') .
                       ", Section: " . ($section ? $section->name : 'N/A') .
                       ", Time: {$validated['start_time']} - {$validated['end_time']}";

        $this->logActivity(
            'update',
            $description,
            'schedules', // module
            $oldData,
            $newData,
            'success'
        );

        return redirect()->route('schedules.index')->with('success', 'Schedule updated successfully');
    }

    public function destroy(Schedule $schedule)
    {
        // Log the activity before deleting
        $teacher = $schedule->teacher;
        $course = $schedule->course;
        $section = $schedule->section;
        $timeInfo = "{$schedule->start_time} - {$schedule->end_time}";
        
        $this->logActivity(
            'schedule', 
            'Deleted schedule', 
            "Removed schedule for Teacher: {$teacher->name}, Course: {$course->name}, Section: {$section->name}, Time: {$timeInfo}"
        );

        $schedule->delete();
        return redirect()->route('schedules.index')->with('success', 'Schedule deleted successfully');
    }
}
