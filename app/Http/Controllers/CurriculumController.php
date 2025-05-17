<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Curriculum;
use App\Models\Section;
use App\Models\SchoolYear;
use App\Models\Course;
use App\Traits\ActivityLogger;
use Illuminate\Support\Facades\Log;

class CurriculumController extends Controller
{
    use ActivityLogger;
    public function index()
    {
        try {
            $activeSchoolYear = SchoolYear::where('is_active', true)->first();
    
            if (!$activeSchoolYear) {
                Log::info('No active school year found');
                return view('curriculums.index', [
                    'sections' => collect(),
                    'activeSchoolYear' => null,
                    'error' => 'No active school year found'
                ]);
            }
    
            $sections = Section::where('school_year_id', $activeSchoolYear->id)
                ->with(['students', 'curriculums.subject'])
                ->get();
    
            if ($sections->isEmpty()) {
                Log::info('No sections found for school year: ' . $activeSchoolYear->id);
                return view('curriculums.index', [
                    'sections' => collect(),
                    'activeSchoolYear' => $activeSchoolYear,
                    'error' => 'No sections found for the current school year'
                ]);
            }
    
            // Debug information
            Log::info('Sections loaded:', [
                'count' => $sections->count(),
                'school_year' => $activeSchoolYear->id
            ]);
    
            return view('curriculums.index', [
                'sections' => $sections,
                'activeSchoolYear' => $activeSchoolYear
            ]);
    
        } catch (\Exception $e) {
            Log::error('Curriculum fetch error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return view('curriculums.index', [
                'sections' => collect(),
                'activeSchoolYear' => null,
                'error' => 'Failed to fetch curriculum: ' . $e->getMessage()
            ]);
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

        $courses = Course::all();

        return view('curriculums.create', compact('sections', 'courses', 'activeSchoolYear'));
    }
// push
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'section_id' => 'required|exists:sections,id',
            'subject_id' => 'required|exists:courses,id',
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
                        $fail('The curriculum start time must be between 7:00 AM - 12:00 PM or 1:00 PM - 6:00 PM.');
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
                            $fail('The curriculum duration cannot exceed 60 minutes.');
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
                        $fail('The curriculum end time must be between 7:00 AM - 12:00 PM or 1:00 PM - 6:00 PM.');
                        return;
                    }

                    $isStartTimeInMorning = $start->gte($morningStart) && $start->lte($morningEnd);
                    $isEndTimeInMorning = $end->gte($morningStart) && $end->lte($morningEnd);

                    if ($isStartTimeInMorning != $isEndTimeInMorning) {
                        $fail('The curriculum must be entirely within the 7 AM - 12 PM slot or the 1 PM - 6 PM slot. It cannot span across the 12 PM - 1 PM break.');
                    }
                },
            ],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();

        // Check for conflicts
        $conflictingCurriculum = Curriculum::where('section_id', $validated['section_id'])
            ->where(function ($query) use ($validated) {
            $query->where('start_time', '<', $validated['end_time'])
                  ->where('end_time', '>', $validated['start_time']);
            })
            ->first();

        if ($conflictingCurriculum) {
            return redirect()->back()
            ->withErrors(['time_conflict' => 'Time slot overlaps with an existing curriculum.'])
            ->withInput();
        }

        // Check for duplicate subjects
        $duplicateSubject = Curriculum::where('section_id', $validated['section_id'])
            ->where('subject_id', $validated['subject_id'])
            ->first();

        if ($duplicateSubject) {
            return redirect()->back()
            ->withErrors(['duplicate_subject' => 'This section already has this subject in its curriculum.'])
            ->withInput();
        }

        $curriculum = Curriculum::create($validated);

        // Log the activity
        $this->logActivity(
            'created',
            'Created new curriculum ' . $curriculum->subject->name . ' for section ' . $curriculum->section->name,
            'curriculum',
            null,
            $curriculum->toArray()
        );

        return redirect()->route('curriculums.index')->with('success', 'Curriculum added successfully!');
    }

    public function edit(Curriculum $curriculum)
    {
        $activeSchoolYear = SchoolYear::where('is_active', true)->first();

        if ($activeSchoolYear) {
            $sections = Section::where('school_year_id', $activeSchoolYear->id)->get(); 
        } else {
            $sections = collect();
        }

        $courses = Course::all();

        return view('curriculums.edit', compact('curriculum', 'sections', 'courses', 'activeSchoolYear'));
    }

    public function update(Request $request, Curriculum $curriculum)
    {
        $validator = Validator::make($request->all(), [
            'section_id' => 'required|exists:sections,id',
            'subject_id' => 'required|exists:courses,id',
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
                        $fail('The curriculum start time must be between 7:00 AM - 12:00 PM or 1:00 PM - 6:00 PM.');
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
                            $fail('The curriculum duration cannot exceed 60 minutes.');
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
                        $fail('The curriculum end time must be between 7:00 AM - 12:00 PM or 1:00 PM - 6:00 PM.');
                        return;
                    }

                    $isStartTimeInMorning = $start->gte($morningStart) && $start->lte($morningEnd);
                    $isEndTimeInMorning = $end->gte($morningStart) && $end->lte($morningEnd);

                    if ($isStartTimeInMorning != $isEndTimeInMorning) {
                        $fail('The curriculum must be entirely within the 7 AM - 12 PM slot or the 1 PM - 6 PM slot. It cannot span across the 12 PM - 1 PM break.');
                    }
                },
            ],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();

        // Check for conflicts
        $conflictingCurriculum = Curriculum::where('section_id', $validated['section_id'])
            ->where('id', '!=', $curriculum->id)
            ->where(function ($query) use ($validated) {
            $query->where('start_time', '<', $validated['end_time'])
                  ->where('end_time', '>', $validated['start_time']);
            })
            ->first();

        if ($conflictingCurriculum) {
            return redirect()->back()
            ->withErrors(['time_conflict' => 'Time slot overlaps with an existing curriculum.'])
            ->withInput();
        }

        // Check for duplicate subjects
        $duplicateSubject = Curriculum::where('section_id', $validated['section_id'])
            ->where('subject_id', $validated['subject_id'])
            ->where('id', '!=', $curriculum->id)
            ->first();

        if ($duplicateSubject) {
            return redirect()->back()
            ->withErrors(['duplicate_subject' => 'This section already has this subject in its curriculum.'])
            ->withInput();
        }

        // Store old values for logging
        $oldValues = $curriculum->toArray();

        $curriculum->update($validated);

        // Log the activity with changes
        $this->logActivity(
            'updated',
            'Updated curriculum' . $curriculum->subject->name . ' for section ' . $curriculum->section->name,
            'curriculum',
            $oldValues,
            $curriculum->toArray()
        );

        return redirect()->route('curriculums.index')->with('success', 'Curriculum updated successfully!');
    }

    public function destroy(Curriculum $curriculum)
    {
        // Store curriculum details before deletion for logging
        $oldValues = $curriculum->toArray();

        $curriculum->delete();


        // Log the deletion activity
        $this->logActivity(
            'deleted',
            'Deleted curriculum ' . $curriculum->subject->name . ' for section ' . $curriculum->section->name,
            'curriculum',
            $oldValues,
            $curriculum->toArray()
        );

        return redirect()->route('curriculums.index')->with('success', 'Curriculum deleted successfully!');
    }
}
