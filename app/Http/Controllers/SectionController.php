<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\Student;
use App\Models\User;
use App\Models\SchoolYear;
use App\Models\GradeLevel;
use App\Models\Curriculum;
use App\Traits\ActivityLogger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use App\Rules\AdviserIsAvailable; 

class SectionController extends Controller
{
    use ActivityLogger;
    public function index()
    {
        $activeSchoolYear = SchoolYear::where('is_active', true)->first();

        if ($activeSchoolYear) {
            $sections = Section::with('schoolYear')
                ->where('school_year_id', $activeSchoolYear->id)
                ->where('is_active', true)
                ->orderBy('grade_level')
                ->orderBy('name')
                ->paginate(10);
        } else {
            $sections = collect(); 
        }
        return view('sections.index', compact('sections'));
    }

    public function create()
    {
        $schoolYears = SchoolYear::where('is_active', true)->get();
        $gradeLevels = GradeLevel::orderBy('grade_level')->get();
        $advisers = User::where('role', 'teacher')->orderBy('name')->get();

        return view('sections.create', compact('schoolYears', 'gradeLevels', 'advisers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'grade_level' => 'required|exists:grade_levels,grade_level',
            'school_year_id' => 'required|exists:school_years,id',
            'adviser_id' => [
                'nullable',
                'exists:users,id',
                new AdviserIsAvailable($request->input('school_year_id')),
            ],
        ]);

        $section = Section::create($validated);

        // Log the activity
        $this->logActivity(
            'create',
            'Created section: ' . $section->name,
            $section,
            Auth::user()
        );

        return redirect()->route('sections.index')
            ->with('success', 'Section created successfully');
        
    }
    public function edit(Section $section)
    {
        $schoolYears = SchoolYear::where('is_active', true)->get();
        $gradeLevels = GradeLevel::orderBy('grade_level')->get();
        $advisers = User::where('role', 'teacher')->orderBy('name')->get();
        return view('sections.edit', compact('section', 'schoolYears', 'gradeLevels', 'advisers'));
    }

    public function update(Request $request, Section $section)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'grade_level' => 'required_if:role,student|string|exists:grade_levels,grade_level',
            'school_year_id' => 'required|exists:school_years,id',
            'adviser_id' => [
                'nullable',
                'exists:users,id',
                new AdviserIsAvailable($request->input('school_year_id'), $section->id),
            ],
        ]);
        
        $oldData = $section->toArray();
        $section->update($validated);

        // Log the activity with changes
        $changes = array_diff_assoc($validated, $oldData);
        $changeDescription = 'Updated section: ' . $section->name . ' - Changes: ' . 
            collect($changes)->map(function ($value, $key) use ($oldData) {
            return "$key from '{$oldData[$key]}' to '$value'";
            })->implode(', ');

        $this->logActivity(
            'update',
            $changeDescription,
            $section,
            Auth::user()
        );

        return redirect()->route('sections.index')
            ->with('success', 'Section updated successfully');
    }

    public function getCurriculum(Section $section): JsonResponse
    {
        // Eager load the curriculum items and the associated subject.
        $curriculum = Curriculum::with('subject')
            ->where('section_id', $section->id)
            ->orderBy('start_time')
            ->get();

        if ($curriculum->isEmpty()) {
            return response()->json([
                'error' => 'No curriculum found for this section.',
                'section_name' => $section->name, // Pass section name for display
            ]);
        }

        // Format the data for the frontend.  Crucially, access subject name.
        $formattedCurriculum = $curriculum->map(function ($item) {
            $startTime = $item->start_time ? Carbon::parse($item->start_time)->format('h:i A') : null;
            $endTime = $item->end_time ? Carbon::parse($item->end_time)->format('h:i A') : null;
            return [
                'id' => $item->id,
                'subject_name' => $item->subject->name, // Access the subject name
                'grade_level' => $item->subject->grade_level,
                'start_time' => $startTime,
                'end_time' => $endTime,
            ];
        });

        return response()->json([
            'curriculum' => $formattedCurriculum,
            'section_name' => $section->name, // Pass section name for display
        ]);
    }

    public function archivedIndex()
    {
        $sections = Section::onlyTrashed()
                            ->with(['adviser', 'schoolYear'])
                            ->paginate(10);
        return view('sections.archived', compact('sections'));
    }

    public function archive(Section $section)
    {
        if ($section->trashed()) { 
            return redirect()->route('sections.index')
                             ->with('info', 'Section ' . $section->name . ' is already archived.');
        }

        try {
            DB::transaction(function () use ($section) {
                // Unassign students from this section
                Student::where('section_id', $section->id)->update(['section_id' => null]);

                // Update section properties before soft deleting
                $section->is_active = false; 
                $section->adviser_id = null; 
                $section->save(); 
                $section->delete(); 
            });

            $logMessage = 'Archived section: ' . $section->name . '. Students and adviser unassigned.';
            // Log the activity
            $this->logActivity(
                'archive',
                $logMessage,
                $section,
                Auth::user()
            );

            return redirect()->route('sections.index')
                ->with('success', 'Section archived successfully and its students and adviser have been unassigned.');
        } catch (\Exception $e) {
            // Log the error if something went wrong during the transaction
            \Log::error('Error archiving section ' . $section->id . ': ' . $e->getMessage());
            return redirect()->route('sections.index')
                ->with('error', 'Failed to archive section. Please try again.');
        }
    }

    public function restore($id)
    {
        $section = Section::onlyTrashed()->find($id);

        if (!$section) {
            return redirect()->route('sections.archivedIndex')->with('error', 'Archived section not found.');
        }

        $section->restore(); 

        $section->is_active = true;
        $section->save();

        $this->logActivity(
            'restore',
            'Restored section: ' . $section->name,
            $section, 
            Auth::user()
        );

        return redirect()->route('sections.archivedIndex')->with('success', 'Section restored successfully.');
    }

    public function showAssignStudentsForm(Section $section)
    {
        // Fetch students who are not in this section or are unassigned
        $assignableStudents = Student::where(function ($query) use ($section) {
                                        $query->where('section_id', '!=', $section->id)
                                            ->orWhereNull('section_id');})
                                            ->where('school_year_id', $section->school_year_id)
                                            ->where('grade_level', $section->grade_level)
                                            ->orderBy('last_name')
                                            ->orderBy('first_name')
                                            ->get();

        // Fetch students who are already assigned to this section
        $currentStudents = $section->students()->orderBy('last_name')->orderBy('first_name')->get();


        return view('sections.assign-students', compact('section', 'assignableStudents', 'currentStudents'));
    }

    public function assignStudents(Request $request, Section $section)
    {
        $validated = $request->validate([
            'student_ids' => 'nullable|array', 
            'student_ids.*' => 'exists:students,id', 
        ]);

        $studentIdsToAssign = $validated['student_ids'] ?? [];


        if (!empty($studentIdsToAssign)) { 
            // This will update the section_id for all selected students even the ones already in another section
            $updateCount = Student::whereIn('id', $studentIdsToAssign)->update([
                'section_id' => $section->id,
                'school_year_id' => $section->school_year_id, 
                'grade_level' => $section->grade_level,      
            ]);

            // Log the activity
            // Fetch student details for a more descriptive log message
            $assignedStudents = Student::whereIn('id', $studentIdsToAssign)
                                           ->select('id', 'last_name', 'first_name') // Be explicit with select
                                           ->get();

            if ($assignedStudents->isNotEmpty()) {
                $assignedStudentNames = $assignedStudents->map(function ($student) {
                    // Construct name, handling potential nulls gracefully
                    $nameParts = [];
                    if (!empty($student->last_name)) {
                        $nameParts[] = $student->last_name;
                    }
                    if (!empty($student->first_name)) {
                        $nameParts[] = $student->first_name;
                    }
                    if (empty($nameParts)) {
                        return 'Unknown Student (ID: ' . $student->id . ')';
                    }
                    return implode(', ', $nameParts); // Format as "Last, First" or "Last" or "First"
                })->implode('; ');


                $this->logActivity(
                    'assign_students',
                    'Assigned students (' . $assignedStudentNames . ') to section: ' . $section->name,
                    $section,
                    Auth::user()
                );
            }
        }

        return redirect()->route('sections.assignStudentsForm', $section)->with('success', 'Students assigned to section successfully.');
    }

}