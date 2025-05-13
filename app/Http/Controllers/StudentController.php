<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use App\Models\Section;
use App\Models\GradeLevel;
use App\Models\SchoolYear;
use App\Models\StudentEnrollmentHistory;
use App\Traits\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;

class StudentController extends Controller
{
    use ActivityLogger;
    
    public function index(Request $request)
    {
        $schoolYears = SchoolYear::orderBy('start_year', 'desc')->get();
        $activeSchoolYear = SchoolYear::where('is_active', true)->first();

        $selectedSchoolYearId = $request->input('school_year_id', $activeSchoolYear->id ?? null);

        $studentsQuery = Student::with(['user', 'section', 'schoolYear']);

        if ($selectedSchoolYearId) {
            $studentsQuery->where('school_year_id', $selectedSchoolYearId);
        }

        $students = $studentsQuery
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(10);
            
        $students->appends(['school_year_id' => $selectedSchoolYearId]);

        return view('students.index', compact('students', 'schoolYears', 'selectedSchoolYearId'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'middle_name' => 'nullable|string|max:255',
                'email' => 'required|email|unique:users,email',
                'lrn' => 'required|string|unique:students,lrn',
                'student_id' => 'required|string|unique:students,student_id',
                'section_id' => 'required|exists:sections,id',
                'grade_level' => 'required|string|exists:grade_levels,grade_level',
                'school_year_id' => 'required|exists:school_years,id',
                'birth_date' => 'required|date',
                'gender' => 'required|in:male,female,other',
                'contact_number' => 'required|string',
                'guardian_name' => 'required|string',
                'guardian_contact' => 'required|string',
                'address' => 'nullable|string|max:255'  // Add this line
            ]);
    
            $student = null;
            DB::transaction(function () use ($validated, &$student) {
                $user = User::create([
                    'name' => $validated['first_name'] . ' ' . $validated['last_name'],
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['student_id']),
                    'role' => 'student'
                ]);
    
                $student = Student::create([
                    'lrn' => $validated['lrn'],
                    'user_id' => $user->id,
                    'student_id' => $validated['student_id'],
                    'first_name' => $validated['first_name'],
                    'last_name' => $validated['last_name'],
                    'middle_name' => $validated['middle_name'],
                    'birth_date' => $validated['birth_date'],
                    'gender' => strtolower($validated['gender']),
                    'contact_number' => $validated['contact_number'],
                    'guardian_name' => $validated['guardian_name'],
                    'guardian_contact' => $validated['guardian_contact'],
                    'section_id' => $validated['section_id'],
                    'grade_level' => $validated['grade_level'],
                    'school_year_id' => $validated['school_year_id'],
                    'address' => $validated['address'] ?? null  // Add this line
                ]);

                $this->logActivity(
                    'create',
                    'Created new student: ' . $validated['first_name'] . ' ' . $validated['last_name'],
                    'students',
                    null,
                    array_merge($validated, ['user_id' => $student->user_id]),
                    'success'
                );
            });
    
            return redirect()->route('students.index')
                ->with('success', 'Student registered successfully');

        } catch (\Exception $e) {
            $this->logActivity(
                'create',
                'Warning: Failed to create student: ' . $e->getMessage(),
                'students',
                null,
                $validated ?? null,
                'error'
            );
            throw $e;
        }
    }

    public function show(Student $student)
    {
        $student->load(['user', 'section', 'schoolYear']);
        return view('students.show', compact('student'));
    }

    public function create()
    {
        $sections = Section::where('is_active', true)->get();
        $schoolYears = SchoolYear::where('is_active', true)
                                ->orderBy('start_year', 'desc')
                                ->get();
        $gradeLevels = GradeLevel::orderBy('grade_level')->get();
        return view('students.create', compact('sections', 'schoolYears', 'gradeLevels'));
    }

    public function edit(Student $student)
    {
        $sections = Section::where('is_active', true)->get();
        $schoolYears = SchoolYear::where('is_active', true)->get();
        $gradeLevels = GradeLevel::orderBy('grade_level')->get(); 
        $student->load('user'); 

        return view('admin.students.edit', compact('student', 'sections', 'schoolYears', 'gradeLevels')); 
    }

    public function update(Request $request, Student $student)
    {
        try {
            $oldData = array_merge(
                $student->toArray(),
                ['name' => $student->user->name, 'email' => $student->user->email]
            );
    
            $validated = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'middle_name' => 'nullable|string|max:255',
                'section_id' => 'required|exists:sections,id',
                'gender' => 'required|in:male,female,other',
                'birth_date' => 'required|date',
                'address' => 'required|string',
                'contact_number' => 'nullable|string',
                'guardian_name' => 'required|string',
                'guardian_contact' => 'required|string',
                'grade_level' => 'required|string|exists:grade_levels,grade_level',
                'school_year_id' => 'required|exists:school_years,id',
                
            ]);

            DB::transaction(function () use ($validated, $student) {
            $student->user->update([
                'name' => $validated['first_name'] . ' ' . $validated['last_name'],
            ]);

            

            $student->update([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'middle_name' => $validated['middle_name'],
                'birth_date' => $validated['birth_date'],
                'gender' => strtolower($validated['gender']),
                'address' => $validated['address'],
                'contact_number' => $validated['contact_number'],
                'guardian_name' => $validated['guardian_name'],
                'guardian_contact' => $validated['guardian_contact'],
                'section_id' => $validated['section_id'],
                'grade_level' => $validated['grade_level'],
                'school_year_id' => $validated['school_year_id']
            ]);
        });

        $newData = array_merge(
            $student->fresh()->toArray(),
            ['name' => $student->user->name, 'email' => $student->user->email]
        );

        $this->logActivity(
            'update',
            'Updated student: ' . $student->user->name,
            'students',
            $oldData,
            $newData,
            'success'
        );

        return redirect()->route('admin.students.index')
            ->with('success', 'Student updated successfully');
        } catch (\Exception $e) {
            $this->logActivity(
                'update',
                'Warning: Failed to update student: ' . $e->getMessage(),
                'students',
                $oldData ?? null,
                $validated ?? null,
                'error'
            );
            throw $e;
        }
    }

    public function destroy(Student $student)
    {
        try {
            $studentData = array_merge(
                $student->toArray(),
                ['name' => $student->user->name, 'email' => $student->user->email]
            );
    
            DB::transaction(function () use ($student) {
                //$student->user->delete();
                $student->delete();
            });
    
            $this->logActivity(
                'archive',
                'Archived student: ' . $student->last_name . ', ' . $student->first_name,
                'students',
                $studentData,
                null,
                'success'
            );
    
            return redirect()->route('admin.students.index')
                ->with('success', 'Student archived successfully');
        } catch (\Exception $e) {
            $this->logActivity(
                'archive',
                'Warning: Failed to archive student: ' . $e->getMessage(),
                'students',
                $studentData ?? null,
                null,
                'error'
            );
            throw $e;
        }
    }

    public function archivedIndex(Request $request)
    {
        $schoolYears = SchoolYear::orderBy('start_year', 'desc')->get();
        $selectedSchoolYearId = $request->input('school_year_id', ''); 

        $studentsQuery = Student::onlyTrashed()->with(['user', 'section', 'schoolYear']);

        if ($selectedSchoolYearId !== '' && !is_null($selectedSchoolYearId)) { 
            $studentsQuery->where('school_year_id', $selectedSchoolYearId);
        }

        $archivedStudents = $studentsQuery
            ->orderBy('deleted_at', 'desc')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(10);

        // Append the school year ID to pagination links if it was used for filtering
        if ($selectedSchoolYearId !== '' && !is_null($selectedSchoolYearId)) {
            $archivedStudents->appends(['school_year_id' => $selectedSchoolYearId]);
        }

        return view('admin.students.archived_index', compact('archivedStudents', 'schoolYears', 'selectedSchoolYearId'));
    }

    public function archivedShow($student_id)
    {
        $student = Student::onlyTrashed()->with(['user', 'section', 'schoolYear'])->findOrFail($student_id);
        return view('admin.students.show_archived', compact('student'));
    }

    public function unarchive($student_id)
    {
        $student = Student::onlyTrashed()->findOrFail($student_id);

        try {
            $student->restore();

            $this->logActivity(
                'restore',
                'Restored student: ' . $student->user->name,
                'students',
                ['student_id' => $student->id, 'name' => $student->user->name],
                null,
                'success'
            );

            return redirect()->route('admin.students.archivedIndex')->with('success', 'Student restored successfully.');
        } catch (\Exception $e) {
            // Log error
            return redirect()->route('admin.students.archivedIndex')->with('error', 'Failed to restore student: ' . $e->getMessage());
        }
    }

    public function resetPassword(Student $student)
    {
        try {
            $student->user->update([
                'password' => Hash::make($student->lrn)
            ]);
    
            $this->logActivity(
                'reset',
                'Reset password for student: ' . $student->user->name,
                'students',
                ['student_id' => $student->student_id],
                ['student_id' => $student->student_id, 'reset_to' => 'LRN'],
                'success'
            );
    
            return redirect()->route('admin.students.index')
                ->with('success', 'Student password reset to LRN successfully');
        } catch (\Exception $e) {
            $this->logActivity(
                'reset',
                'Warning: Failed to reset student password: ' . $e->getMessage(),
                'students',
                ['student_id' => $student->student_id],
                null,
                'error'
            );
            throw $e;
        }
    }
    public function records()
    {
        $students = Student::with(['schoolYear', 'section'])->paginate(10); 
        return view('students.records', compact('students'));
    }

    public function levelUp(Student $student): RedirectResponse
    {
        // Checks authorization, only admin should be able to access this method
        if (!Auth::user() || (Auth::user()->role !== 'admin')) {
            return redirect()->route('students.index')->with('error', 'You are not authorized to perform this action.');
        }

        // Determine the current grade level
        $currentGradeModel = GradeLevel::where('grade_level', $student->grade_level)->first();
        if (!$currentGradeModel || !isset($currentGradeModel->order_sequence)) {
            return redirect()->route('students.index')->with('error', "Student's current grade level ('{$student->grade_level}') is invalid, not found, or missing order sequence in the system.");
        }
        // Find the next grade level
        $nextGradeModel = GradeLevel::where('order_sequence', '>', $currentGradeModel->order_sequence)
                                    ->orderBy('order_sequence', 'asc')
                                    ->first();

        if (!$nextGradeModel) {
            return redirect()->route('students.index')->with('info', "Student {$student->first_name} {$student->last_name} is already at the highest grade level or no next grade is defined.");
        }

        // Determine the current sy
        $currentSchoolYear = $student->schoolYear; 
        if (!$currentSchoolYear) {
            return redirect()->route('students.index')->with('error', "Student's current school year information is missing.");
        }

        // Save current enrollment details to history
        $student->loadMissing('section');
        StudentEnrollmentHistory::create([
            'student_id' => $student->id,
            'school_year_id' => $student->school_year_id,
            'grade_level' => $student->grade_level,
            'section_id' => $student->section_id,
            'adviser_id' => $student->section ? $student->section->adviser_id : null,
        ]);

        // Find the next sy
        $nextSchoolYear = SchoolYear::where('start_year', '>', $currentSchoolYear->start_year)
                                    ->orderBy('start_year', 'asc')
                                    ->first();

        if (!$nextSchoolYear) {
            return redirect()->route('students.index')->with('error', 'The next school year does not yet exist.');
        }

        // Update student record
        $student->grade_level = $nextGradeModel->grade_level;
        $student->school_year_id = $nextSchoolYear->id;
        $student->section_id = null; 
        $student->save();

        $message = "Student {$student->first_name} {$student->last_name} has been leveled up to {$nextGradeModel->grade_level} for the school year {$nextSchoolYear->start_year}-{$nextSchoolYear->end_year}. Please assign a new section if necessary.";
        return redirect()->route('students.index')->with('success', $message);
    }

    public function batchLevelUp(Request $request)
    {
        if (!Auth::user() || Auth::user()->role !== 'admin') {
            return redirect()->route('students.index')->with('error', 'You are not authorized to perform this action.');
        }

        $activeSchoolYear = SchoolYear::where('is_active', true)->first();
        if (!$activeSchoolYear) {
            return redirect()->route('students.index')->with('error', 'No active school year found. Cannot perform batch promotion.');
        }

        $nextSchoolYear = SchoolYear::where('start_year', '>', $activeSchoolYear->start_year)
                                    ->orderBy('start_year', 'asc')
                                    ->first();
        if (!$nextSchoolYear) {
            return redirect()->route('students.index')->with('error', 'The next school year has not been configured in the system. Cannot perform batch promotion.');
        }

         // Eager load section to efficiently get adviser_id later
         $studentsToPromote = Student::with('section')->where('school_year_id', $activeSchoolYear->id)->get();

        if ($studentsToPromote->isEmpty()) {
            return redirect()->route('students.index')->with('info', "No students found in the active school year ({$activeSchoolYear->start_year}-{$activeSchoolYear->end_year}) to promote.");
        }

        $promotedCount = 0;
        $skippedCount = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            foreach ($studentsToPromote as $student) {
                $currentGradeModel = GradeLevel::where('grade_level', $student->grade_level)->first();

                if (!$currentGradeModel || !isset($currentGradeModel->order_sequence)) {
                    $errors[] = "Student {$student->first_name} {$student->last_name} (ID: {$student->student_id}): Invalid current grade level ('{$student->grade_level}') or missing order sequence. Skipped.";
                    $skippedCount++;
                    continue;
                }

                $nextGradeModel = GradeLevel::where('order_sequence', '>', $currentGradeModel->order_sequence)
                                            ->orderBy('order_sequence', 'asc')
                                            ->first();

                if (!$nextGradeModel) {
                    $errors[] = "Student {$student->first_name} {$student->last_name} (ID: {$student->student_id}): Already at the highest grade level or no next grade defined. Skipped.";
                    $skippedCount++;
                    continue;
                }

                // Save current enrollment details to history
                StudentEnrollmentHistory::create([
                    'student_id' => $student->id,
                    'school_year_id' => $student->school_year_id,
                    'grade_level' => $student->grade_level,
                    'section_id' => $student->section_id,
                    'adviser_id' => $student->section ? $student->section->adviser_id : null,
                ]);

                $student->grade_level = $nextGradeModel->grade_level;
                $student->school_year_id = $nextSchoolYear->id;
                $student->section_id = null; 
                $student->save();
                $promotedCount++;
            }

            DB::commit();

            $message = "Batch promotion complete. Promoted: {$promotedCount} students. Skipped: {$skippedCount} students.";
            if (!empty($errors)) {
                $message .= " Details: " . implode('; ', $errors);
                return redirect()->route('students.index')->with('warning', $message);
            }
            return redirect()->route('students.index')->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('students.index')->with('error', 'An error occurred during batch promotion: ' . $e->getMessage());
        }
    }
}