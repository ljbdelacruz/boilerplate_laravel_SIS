<?php

namespace App\Http\Controllers;

use App\Models\SchoolYear;
use App\Models\Section;
use App\Models\Student;
use App\Models\User;
use App\Models\Course;
use App\Models\Schedule;
use App\Traits\ActivityLogger;
use App\Models\Grade;
use App\Models\StudentEnrollmentHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class TeacherController extends Controller
{
    use ActivityLogger;
    public function list(){
        $teachers = User::where('role', 'teacher')
                       ->orderBy('name')
                       ->get();
        
        return view('admin.teachers.index', compact('teachers'));
    }
    public function index()
    {
        $schoolYears = SchoolYear::where('is_active', true)
            ->orderBy('start_year', 'desc')
            ->get();
        $sections = Section::orderBy('grade_level')->orderBy('name')->get();
        $activeSchoolYear = SchoolYear::where('is_active', true)->first();

        $schedules = [];
        if ($activeSchoolYear) {
            $schedules = Schedule::where('teacher_id', auth()->id())
                ->where('school_year_id', $activeSchoolYear->id) // Filter by active school year
                ->with(['course', 'section', 'schoolYear'])
                ->get();
        }

        $students = [];
        if ($activeSchoolYear && !$schedules->isEmpty()) { // Check if schedules exist
            $students = Student::whereHas('section', function ($query) use ($activeSchoolYear, $schedules) {
                $query->where('school_year_id', $activeSchoolYear->id)
                      ->whereIn('id', $schedules->pluck('section_id')->unique()); // Use unique section IDs
            })->with(['section'])
              ->orderBy('last_name')
              ->orderBy('first_name')
              ->get();
        }
        return view('dashboard.teacher', compact('schoolYears', 'sections', 'schedules', 'students')); // Add 'students'
    }

    public function viewStudents(Request $request)
    {
        $schoolYears = SchoolYear::where('is_active', true)
                                ->orderBy('start_year', 'desc')
                                ->get();
        
        $activeSchoolYear = SchoolYear::where('is_active', true)->first();
        $sections = Section::where('school_year_id', $activeSchoolYear->id)
            ->orderBy('grade_level')
            ->orderBy('name')
            ->get();
        
        $students = null;
        if ($request->filled(['school_year_id', 'section_id'])) {
            $students = Student::where('school_year_id', $request->school_year_id)
                             ->where('section_id', $request->section_id)
                             ->get();
        }
        
        return view('dashboard.teacher', compact('schoolYears', 'sections', 'students'));
    }

    public function saveGrades(Request $request, Student $student)
    {
        try {
            $validated = $request->validate([
                'subject_id' => 'required|exists:courses,id',
                'school_year_id' => 'required|exists:school_years,id',
                'prelim' => 'nullable|numeric|min:0|max:100',
                'midterm' => 'nullable|numeric|min:0|max:100',
                'prefinal' => 'nullable|numeric|min:0|max:100',
                'final' => 'nullable|numeric|min:0|max:100',
            ]);

            // Authorization Check: Ensure the teacher is assigned to this subject/section/year
        $currentTeacher = Auth::user();
        $isTeacherForSubject = Schedule::where('teacher_id', $currentTeacher->id)
                                        ->where('course_id', $validated['subject_id'])
                                        ->where('section_id', $student->section_id) 
                                        ->where('school_year_id', $validated['school_year_id'])
                                        ->exists();
        if (!$isTeacherForSubject) {
            return response()->json(['success' => false, 'message' => 'Unauthorized: You do not teach this subject to this student.'], 403);
        }
    
        $oldGrade = $student->grades()
                ->where('subject_id', $validated['subject_id'])
                ->where('school_year_id', $validated['school_year_id'])
                ->first();

            $grade = $student->grades()->updateOrCreate(
                [
                    'student_id' => $student->id,
                    'subject_id' => $validated['subject_id'],
                    'school_year_id' => $validated['school_year_id']
                ],
                [
                    'prelim' => $validated['prelim'],
                    'midterm' => $validated['midterm'],
                    'prefinal' => $validated['prefinal'],
                    'final' => $validated['final']
                ]
            );

            $this->logActivity(
                'update',
                'Updated student grades',
                'grades',
                $oldGrade ? $oldGrade->toArray() : null,
                $grade->toArray()
            );

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            $this->logActivity(
                'update',
                'Warning: Failed to update student grades',
                'grades',
                null,
                null,
                'error'
            );
            return response()->json(['error' => 'Error saving grades'], 500);
        }
    }

    public function submitGrades(Student $student, Request $request)
    {
        $currentTeacher = Auth::user();
        $student->load( 'section', 'schoolYear');
        
        // Check active school year and section
        $activeSchoolYear = $student->schoolYear;
        if (!$activeSchoolYear || !$student->section) {
            // Handle case where student doesn't have a valid school year or section
            return redirect()->route('teacher.dashboard')
                             ->with('error', 'Student does not belong to a valid section or school year.');
        }
        $schoolYearId = $activeSchoolYear->id;

        // Check if the teacher is assigned to the student's section
        $teacherSchedulesInSection = Schedule::where('teacher_id', $currentTeacher->id)
            ->where('section_id', $student->section_id)
            ->where('school_year_id', $schoolYearId)
            ->with('course') 
            ->get();

        if ($teacherSchedulesInSection->isEmpty()) {
            return redirect()->route('teacher.dashboard') // Redirect to teacher dashboard
                             ->with('error', "You do not have any scheduled classes for section '{$student->section->name}' in the {$activeSchoolYear->school_year_display} school year.");
        }

        // Check for a specific course_id in the request
        $requestedCourseId = $request->query('course_id'); 
        $selectedSchedule = null;
        
        if ($requestedCourseId) {
            // Find the schedule matching the requested course ID
            $selectedSchedule = $teacherSchedulesInSection->firstWhere('course_id', $requestedCourseId);
        }

        // If no valid course requested or not found, default to the first schedule
        if (!$selectedSchedule) {
            $selectedSchedule = $teacherSchedulesInSection->first();
        }
        $selectedCourse = $selectedSchedule->course;
        

        // Fetch the specific Grade record for this student, subject, and school year
        $grades = Grade::where('student_id', $student->id)
                    ->where('subject_id', $selectedCourse->id)
                    ->where('school_year_id', $schoolYearId)
                    ->first(); // Use first() to get a single model or null

        // Get all courses teacher teaches this section for a potential dropdown later
        $teacherCoursesForSection = $teacherSchedulesInSection->map->course->unique(); 

        return view('dashboard.submit-grades', [
            'student' => $student,
            'grades' => $grades, 
            'selectedCourse' => $selectedCourse, 
            'teacherCoursesForSection' => $teacherCoursesForSection, 
            'schoolYearId' => $schoolYearId,
            'subjectId' => $selectedCourse->id,
        ]);
    }

    public function edit(User $teacher)
    {
        return view('admin.teachers.edit', compact('teacher'));
    }

    public function update(Request $request, User $teacher)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $teacher->id,
            ]);

            $oldData = $teacher->toArray();
            
            $teacher->update($validated);

            $this->logActivity(
                'update',
                "Updated teacher information for {$teacher->name}",
                'teachers',
                $oldData,
                $teacher->toArray()
            );

            return redirect()->route('teachers.index')->with('success', 'Teacher updated successfully');
        } catch (\Exception $e) {
            $this->logActivity(
                'update',
                "Warning: Failed to update teacher {$teacher->name}",
                'teachers',
                null,
                null,
                'error'
            );
            return back()->with('error', 'Failed to update teacher');
        }
    }

    public function destroy(User $teacher)
    {
        try {
            $teacherData = $teacher->toArray();
            
            $teacher->delete();

            $this->logActivity(
                'delete',
                "Deleted teacher {$teacher->name}",
                'teachers',
                $teacherData,
                null
            );

            return redirect()->route('teachers.index')->with('success', 'Teacher deleted successfully');
        } catch (\Exception $e) {
            $this->logActivity(
                'delete',
                "Warning: Failed to delete teacher {$teacher->name}",
                'teachers',
                null,
                null,
                'error'
            );
            return back()->with('error', 'Failed to delete teacher');
        }
    }

    public function showSF10(Request $request, $studentId)
    {
        $student = Student::with([
            'section.adviser',
            'schoolYear', 
            'enrollmentHistories.schoolYear', 
            'enrollmentHistories.section.adviser',
            'enrollmentHistories.adviser',
        ])->findOrFail($studentId);

        $allSchoolYears = SchoolYear::orderBy('start_year', 'desc')->get();

        // Determine the selected school year
        $selectedSchoolYearId = $request->input('school_year_id', $student->school_year_id);

        if (!$allSchoolYears->contains('id', $selectedSchoolYearId) && $allSchoolYears->isNotEmpty()) {
            $selectedSchoolYearId = $student->school_year_id ?? $allSchoolYears->first()->id;
        }

         $selectedSchoolYear = SchoolYear::find($selectedSchoolYearId);

        // Determine the grade level, section, and adviser for the selected school year
        $gradeLevelForSelectedYear = $student->grade_level; 
        $sectionForSelectedYear = $student->section;
        $adviserForSelectedYear = $student->section ? $student->section->adviser : null;

        if ($selectedSchoolYear && $student->schoolYear && $selectedSchoolYearId != $student->schoolYear->id) {
            $historyForSelectedYear = $student->enrollmentHistories
                                           ->where('school_year_id', $selectedSchoolYearId)
                                           ->first();
            if ($historyForSelectedYear) {
                $gradeLevelForSelectedYear = $historyForSelectedYear->grade_level;
                $sectionForSelectedYear = $historyForSelectedYear->section;
                $adviserForSelectedYear = $historyForSelectedYear->adviser;
            } else {
                $sectionForSelectedYear = null;
                $adviserForSelectedYear = null;
            }
        }

        // Load grades specifically for the selected school year
        $student->load(['grades' => function ($query) use ($selectedSchoolYearId) {
            $query->where('school_year_id', $selectedSchoolYearId)->with('subject');
        }]);
    
        // Subjects are typically based on the student's current grade level for the SF10 form structure
        $subjects = Course::whereNull('parent_id')
            ->with('children')
            ->where('grade_level', $gradeLevelForSelectedYear)
            ->orderBy('id')
            ->get();
    
            return view('dashboard.sf10', 
            compact(
                'student', 
                'selectedSchoolYear',
                 'subjects', 
                 'allSchoolYears', 
                 'selectedSchoolYearId', 
                 'gradeLevelForSelectedYear', 
                 'sectionForSelectedYear', 
                 'adviserForSelectedYear'
                ));
    }

    public function viewSF10Editor(Student $student)
    {
        $student->load(['grades', 'section', 'schoolYear']);
        $subjects = Course::where('grade_level', $student->grade_level)->get();
        
        return view('dashboard.sf10-editor', [
            'student' => $student,
            'subjects' => $subjects,
        ]);
    }

    public function saveSF10(Request $request, Student $student)
    {
        try {
            $validated = $request->validate([
                'grades' => 'required|array',
                'grades.*.subject_id' => 'required|exists:courses,id',
                'grades.*.quarter' => 'required|in:1,2,3,4',
                'grades.*.grade' => 'nullable|numeric|min:60|max:100',
                'school_year_id' => 'required|exists:school_years,id', // Added school_year_id validation
            ]);

            $activeSchoolYearId = $validated['school_year_id'];
            $oldGrades = $student->grades()->where('school_year_id', $activeSchoolYearId)->get()->toArray();
            
            foreach ($validated['grades'] as $gradeData) {
                $grade = $student->grades()->firstOrCreate([
                    'subject_id' => $gradeData['subject_id'],
                    'school_year_id' => $activeSchoolYearId, // Use the passed school_year_id
                ]);

                $quarter = match($gradeData['quarter']) {
                    '1' => 'prelim',
                    '2' => 'midterm',
                    '3' => 'prefinal',
                    '4' => 'final',
                };

                $grade->update([
                    $quarter => $gradeData['grade']
                ]);
            }

            $this->logActivity(
                'update',
                "Updated SF10 grades for student {$student->name}",
                'sf10',
                $oldGrades,
                $student->grades()->where('school_year_id', $activeSchoolYearId)->get()->toArray(), // Log new grades for the specific SY
                'success'
            );

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            $this->logActivity(
                'update',
                "Warning: Failed to update SF10 grades for student {$student->name}",
                'sf10',
                null,
                ['school_year_id' => $activeSchoolYearId, 'error_detail' => $e->getMessage()],
                'error'
            );
            return response()->json(['error' => 'Failed to save grades'], 500);
        }
    }

    public function exportSF10(Student $student) 
    {
        try {
            // Load student's current details and all historical data
            $student->load([
                'user',
                'schoolYear', 
                'section.adviser', 
                'grades.subject', 
                'enrollmentHistories' => function ($query) {
                    $query->with(['schoolYear', 'section', 'adviser']) 
                          ->orderBy('school_year_id', 'asc'); 
                },
            ]);

            // Prepare a unified list of enrollments (historical and current)
            $allEnrollments = collect();

            // Add historical enrollments
            foreach ($student->enrollmentHistories as $historyEntry) {
                $allEnrollments->push([
                    'grade_level'    => $historyEntry->grade_level,
                    'schoolYear'     => $historyEntry->schoolYear,
                    'section'        => $historyEntry->section,
                    'adviser'        => $historyEntry->adviser, 
                ]);
            }

            // Add student's current enrollment if they have one
            if ($student->schoolYear && $student->grade_level) {
                $allEnrollments->push([
                    'grade_level'    => $student->grade_level,
                    'schoolYear'     => $student->schoolYear,
                    'section'        => $student->section,
                    'adviser'        => $student->section ? $student->section->adviser : null, 
                ]);
            }

            // Sort all enrollments by school year and remove potential duplicates
            $sortedEnrollments = $allEnrollments->sortBy(function ($enrollment) {
                return $enrollment['schoolYear'] ? $enrollment['schoolYear']->start_year : PHP_INT_MAX;
            })->unique(function ($enrollment) {
                // Unique based on school year ID and grade level string
                return ($enrollment['schoolYear'] ? $enrollment['schoolYear']->id : 'N/A') . '-' . $enrollment['grade_level'];
            });

            // Load the template file from storage
            $templatePath = public_path('templates/SF10_template.xlsx');


            // Verify template exists
            if (!file_exists($templatePath)) {
                throw new \Exception('SF10 Template file not found');
            }

            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($templatePath);
            $sheet = $spreadsheet->getActiveSheet();

            $sheet0 = $spreadsheet->getSheet(0); 
            $sheet0->setCellValue('C9', $student->last_name);
            $sheet0->setCellValue('L9', $student->first_name);
            $sheet0->setCellValue('Z9', $student->middle_name ?? '');
            $sheet0->setCellValue('F10', $student->lrn);
            $sheet0->setCellValue('P10', $student->birth_date ? $student->birth_date->format('m/d/Y') : '');
            $sheet0->setCellValue('AB10', $student->gender ?? '');

            
            $sheet0->setCellValue('C15', 'USUSAN ELEMENTARY SCHOOL'); 
            $sheet0->setCellValue('N15', '136879'); 
            $sheet0->setCellValue('S15', '76 Gen. Luna St. Ususan Taguig City'); 

            
            foreach ($sortedEnrollments as $enrollmentData) {
                $historicalGradeLevel = $enrollmentData['grade_level'];
                $historicalSchoolYear = $enrollmentData['schoolYear'];
                $historicalSectionName = $enrollmentData['section'] ? $enrollmentData['section']->name : 'N/A';
                $historicalAdviserName = $enrollmentData['adviser'] ? $enrollmentData['adviser']->name : 'N/A';

                // Filter grades for this specific historical school year
                $gradesForThisYear = $historicalSchoolYear ? $student->grades->where('school_year_id', $historicalSchoolYear->id) : collect();

                // Determine which sheet to use based on grade level
                $currentSheet = null;
                if (in_array($historicalGradeLevel, ['Grade 5', 'Grade 6'])) {
                    $currentSheet = $spreadsheet->getSheet(1); 
                } else if (in_array($historicalGradeLevel, ['Grade 1', 'Grade 2', 'Grade 3', 'Grade 4'])) {
                    $currentSheet = $spreadsheet->getSheet(0); 
                } else {
                    continue; 
                }

                if (!$currentSheet) continue;

                // Fetch subjects for this historical grade level
                $subjectsForThisGrade = Course::where('grade_level', $historicalGradeLevel)
                                            ->orderBy('id')
                                            ->get();

                switch ($historicalGradeLevel) {
                    case 'Grade 1':
                        $currentSheet->setCellValue('B27', 'USUSAN ELEMENTARY SCHOOL');
                        $currentSheet->setCellValue('M27', '136879');
                        $currentSheet->setCellValue('B28', 'Cluster 1');
                        $currentSheet->setCellValue('F28', 'Taguig City and Pateros');
                        $currentSheet->setCellValue('N28', 'NCR');
                        $currentSheet->setCellValue('D29', '1'); 
                        $currentSheet->setCellValue('F29', $historicalSectionName);
                        $currentSheet->setCellValue('M29', $historicalSchoolYear ? $historicalSchoolYear->school_year_display : 'N/A');
                        $currentSheet->setCellValue('E30', $historicalAdviserName);

                        $currentRow = 34;
                        $totalFinalGradePoints = 0;
                        $subjectCountWithGrades = 0;
                        $q1Total = 0; $q1Count = 0;
                        $q2Total = 0; $q2Count = 0;
                        $q3Total = 0; $q3Count = 0;
                        $q4Total = 0; $q4Count = 0;


                        foreach ($subjectsForThisGrade as $subject) {
                            $currentSheet->setCellValue("A{$currentRow}", $subject->name);

                            $courseModel = Course::with('children')->find($subject->id);
                            $finalSubjGrade = null;
                            $remarks = '';
                            $q1ForAvg = null; $q2ForAvg = null; $q3ForAvg = null; $q4ForAvg = null;


                            if ($courseModel && $courseModel->children && $courseModel->children->count() > 0) {
                                $parentQuarterSums = ['prelim' => 0, 'midterm' => 0, 'prefinal' => 0, 'final' => 0];
                                $parentQuarterCounts = ['prelim' => 0, 'midterm' => 0, 'prefinal' => 0, 'final' => 0];
                                $parentSumOfChildFinalGrades = 0;
                                $parentCountOfChildFinalGrades = 0;

                                foreach ($courseModel->children as $childSubject) {
                                    $childGradeEntry = $gradesForThisYear->where('subject_id', $childSubject->id)->first();
                                    if ($childGradeEntry) {
                                        $childQuarters = ['prelim' => $childGradeEntry->prelim, 'midterm' => $childGradeEntry->midterm, 'prefinal' => $childGradeEntry->prefinal, 'final' => $childGradeEntry->final];
                                        foreach ($childQuarters as $qKey => $qValue) {
                                            if (is_numeric($qValue)) {
                                                $parentQuarterSums[$qKey] += $qValue;
                                                $parentQuarterCounts[$qKey]++;
                                            }
                                        }
                                        $numericChildQuarterGrades = array_filter($childQuarters, 'is_numeric');
                                        if (count($numericChildQuarterGrades) > 0) {
                                            $childFinal = round((array_sum($numericChildQuarterGrades) / count($numericChildQuarterGrades)));
                                            $parentSumOfChildFinalGrades += $childFinal;
                                            $parentCountOfChildFinalGrades++;
                                        }
                                    }
                                }

                                $valPrelim = $parentQuarterCounts['prelim'] > 0 ? round(($parentQuarterSums['prelim'] / $parentQuarterCounts['prelim'])) : '';
                                $valMidterm = $parentQuarterCounts['midterm'] > 0 ? round(($parentQuarterSums['midterm'] / $parentQuarterCounts['midterm'])) : '';
                                $valPrefinal = $parentQuarterCounts['prefinal'] > 0 ? round(($parentQuarterSums['prefinal'] / $parentQuarterCounts['prefinal'])) : '';
                                $valFinal = $parentQuarterCounts['final'] > 0 ? round(($parentQuarterSums['final'] / $parentQuarterCounts['final'])) : '';

                                $currentSheet->setCellValue("F{$currentRow}", $valPrelim);
                                $currentSheet->setCellValue("G{$currentRow}", $valMidterm);
                                $currentSheet->setCellValue("H{$currentRow}", $valPrefinal);
                                $currentSheet->setCellValue("I{$currentRow}", $valFinal);

                                if (is_numeric($valPrelim)) $q1ForAvg = $valPrelim;
                                if (is_numeric($valMidterm)) $q2ForAvg = $valMidterm;
                                if (is_numeric($valPrefinal)) $q3ForAvg = $valPrefinal;
                                if (is_numeric($valFinal)) $q4ForAvg = $valFinal;

                                $finalSubjGrade = $parentCountOfChildFinalGrades > 0 ? round(($parentSumOfChildFinalGrades / $parentCountOfChildFinalGrades)) : null;
                            } else {
                                // Regular or Child Subject: Get direct grades
                                $gradeEntry = $gradesForThisYear->where('subject_id', $subject->id)->first();
                                $valPrelim = $gradeEntry?->prelim;
                                $valMidterm = $gradeEntry?->midterm;
                                $valPrefinal = $gradeEntry?->prefinal;
                                $valFinal = $gradeEntry?->final;

                                $currentSheet->setCellValue("F{$currentRow}", $valPrelim);
                                $currentSheet->setCellValue("G{$currentRow}", $valMidterm);
                                $currentSheet->setCellValue("H{$currentRow}", $valPrefinal);
                                $currentSheet->setCellValue("I{$currentRow}", $valFinal);

                                if ($gradeEntry) {
                                    if (is_numeric($valPrelim)) $q1ForAvg = $valPrelim;
                                    if (is_numeric($valMidterm)) $q2ForAvg = $valMidterm;
                                    if (is_numeric($valPrefinal)) $q3ForAvg = $valPrefinal;
                                    if (is_numeric($valFinal)) $q4ForAvg = $valFinal;
                                    $quarterGrades = array_filter([$gradeEntry->prelim, $gradeEntry->midterm, $gradeEntry->prefinal, $gradeEntry->final], 'is_numeric');
                                    if (count($quarterGrades) > 0) {
                                        $finalSubjGrade = round((array_sum($quarterGrades) / count($quarterGrades)));
                                    }
                                }
                            }

                            $currentSheet->mergeCells("J{$currentRow}:L{$currentRow}");
                            $currentSheet->mergeCells("M{$currentRow}:N{$currentRow}");

                            if (!is_null($finalSubjGrade)) {
                                $currentSheet->setCellValue("J{$currentRow}", $finalSubjGrade);
                                $remarks = $finalSubjGrade >= 75 ? 'PASSED' : 'FAILED';
                                $currentSheet->setCellValue("M{$currentRow}", $remarks);
                            } else {
                                $currentSheet->setCellValue("J{$currentRow}", '');
                                $currentSheet->setCellValue("M{$currentRow}", '');
                            }

                            if ($subject->parent_id === null) {
                                if (is_numeric($q1ForAvg)) { $q1Total += $q1ForAvg; $q1Count++; }
                                if (is_numeric($q2ForAvg)) { $q2Total += $q2ForAvg; $q2Count++; }
                                if (is_numeric($q3ForAvg)) { $q3Total += $q3ForAvg; $q3Count++; }
                                if (is_numeric($q4ForAvg)) { $q4Total += $q4ForAvg; $q4Count++; }

                                if (!is_null($finalSubjGrade)) {
                                    $totalFinalGradePoints += $finalSubjGrade;
                                    $subjectCountWithGrades++;
                                }
                            }
                            $currentRow++;
                        }

                        $genAvgQ1 = $q1Count > 0 ? round($q1Total / $q1Count) : '';
                        $genAvgQ2 = $q2Count > 0 ? round($q2Total / $q2Count) : '';
                        $genAvgQ3 = $q3Count > 0 ? round($q3Total / $q3Count) : '';
                        $genAvgQ4 = $q4Count > 0 ? round($q4Total / $q4Count) : '';

                        $currentSheet->setCellValue("F49", $genAvgQ1);
                        $currentSheet->setCellValue("G49", $genAvgQ2);
                        $currentSheet->setCellValue("H49", $genAvgQ3);
                        $currentSheet->setCellValue("I49", $genAvgQ4);

                        if ($subjectCountWithGrades > 0) {
                            $generalAverage = round(($totalFinalGradePoints / $subjectCountWithGrades));
                            $currentSheet->setCellValue("J49", $generalAverage); // General Average Row
                            $currentSheet->setCellValue("M49", $generalAverage >= 75 ? 'Promoted' : 'Failed');
                        } else {
                            $currentSheet->setCellValue("J49", '');
                            $currentSheet->setCellValue("M49", '');
                        }
                        break;

                    case 'Grade 2':
                        $currentSheet->setCellValue('Q27', 'USUSAN ELEMENTARY SCHOOL');
                        $currentSheet->setCellValue('AB27', '136879');
                        $currentSheet->setCellValue('Q28', 'Cluster 1');
                        $currentSheet->setCellValue('U28', 'Taguig City and Pateros');
                        $currentSheet->setCellValue('AC28', 'NCR');
                        $currentSheet->setCellValue('S29', '2');
                        $currentSheet->setCellValue('U29', $historicalSectionName);
                        $currentSheet->setCellValue('AC29', $historicalSchoolYear ? $historicalSchoolYear->school_year_display : 'N/A');
                        $currentSheet->setCellValue('T30', $historicalAdviserName);

                        $currentRow = 34;
                        $totalFinalGradePoints = 0;
                        $subjectCountWithGrades = 0;
                        $q1Total = 0; $q1Count = 0;
                        $q2Total = 0; $q2Count = 0;
                        $q3Total = 0; $q3Count = 0;
                        $q4Total = 0; $q4Count = 0;

                        foreach ($subjectsForThisGrade as $subject) {
                            $currentSheet->setCellValue("P{$currentRow}", $subject->name);

                            $courseModel = Course::with('children')->find($subject->id);
                            $finalSubjGrade = null;
                            $remarks = '';
                            $q1ForAvg = null; $q2ForAvg = null; $q3ForAvg = null; $q4ForAvg = null;



                            if ($courseModel && $courseModel->children && $courseModel->children->count() > 0) {
                                $parentQuarterSums = ['prelim' => 0, 'midterm' => 0, 'prefinal' => 0, 'final' => 0];
                                $parentQuarterCounts = ['prelim' => 0, 'midterm' => 0, 'prefinal' => 0, 'final' => 0];
                                $parentSumOfChildFinalGrades = 0;
                                $parentCountOfChildFinalGrades = 0;

                                foreach ($courseModel->children as $childSubject) {
                                    $childGradeEntry = $gradesForThisYear->where('subject_id', $childSubject->id)->first();
                                    if ($childGradeEntry) {
                                        $childQuarters = ['prelim' => $childGradeEntry->prelim, 'midterm' => $childGradeEntry->midterm, 'prefinal' => $childGradeEntry->prefinal, 'final' => $childGradeEntry->final];
                                        foreach ($childQuarters as $qKey => $qValue) {
                                            if (is_numeric($qValue)) {
                                                $parentQuarterSums[$qKey] += $qValue;
                                                $parentQuarterCounts[$qKey]++;
                                            }
                                        }
                                        $numericChildQuarterGrades = array_filter($childQuarters, 'is_numeric');
                                        if (count($numericChildQuarterGrades) > 0) {
                                            $childFinal = round((array_sum($numericChildQuarterGrades) / count($numericChildQuarterGrades)));
                                            $parentSumOfChildFinalGrades += $childFinal;
                                            $parentCountOfChildFinalGrades++;
                                        }
                                    }
                                }
                                $valPrelim = $parentQuarterCounts['prelim'] > 0 ? round(($parentQuarterSums['prelim'] / $parentQuarterCounts['prelim'])) : '';
                                $valMidterm = $parentQuarterCounts['midterm'] > 0 ? round(($parentQuarterSums['midterm'] / $parentQuarterCounts['midterm'])) : '';
                                $valPrefinal = $parentQuarterCounts['prefinal'] > 0 ? round(($parentQuarterSums['prefinal'] / $parentQuarterCounts['prefinal'])) : '';
                                $valFinal = $parentQuarterCounts['final'] > 0 ? round(($parentQuarterSums['final'] / $parentQuarterCounts['final'])) : '';

                                $currentSheet->setCellValue("V{$currentRow}", $valPrelim);
                                $currentSheet->setCellValue("W{$currentRow}", $valMidterm);
                                $currentSheet->setCellValue("X{$currentRow}", $valPrefinal);
                                $currentSheet->setCellValue("Y{$currentRow}", $valFinal);

                                if (is_numeric($valPrelim)) $q1ForAvg = $valPrelim;
                                if (is_numeric($valMidterm)) $q2ForAvg = $valMidterm;
                                if (is_numeric($valPrefinal)) $q3ForAvg = $valPrefinal;
                                if (is_numeric($valFinal)) $q4ForAvg = $valFinal;

                                $finalSubjGrade = $parentCountOfChildFinalGrades > 0 ? round(($parentSumOfChildFinalGrades / $parentCountOfChildFinalGrades)) : null;
                            } else {
                                $gradeEntry = $gradesForThisYear->where('subject_id', $subject->id)->first();
                                $valPrelim = $gradeEntry?->prelim;
                                $valMidterm = $gradeEntry?->midterm;
                                $valPrefinal = $gradeEntry?->prefinal;
                                $valFinal = $gradeEntry?->final;

                                $currentSheet->setCellValue("V{$currentRow}", $valPrelim);
                                $currentSheet->setCellValue("W{$currentRow}", $valMidterm);
                                $currentSheet->setCellValue("X{$currentRow}", $valPrefinal);
                                $currentSheet->setCellValue("Y{$currentRow}", $valFinal);

                                if ($gradeEntry) {
                                    if (is_numeric($valPrelim)) $q1ForAvg = $valPrelim;
                                    if (is_numeric($valMidterm)) $q2ForAvg = $valMidterm;
                                    if (is_numeric($valPrefinal)) $q3ForAvg = $valPrefinal;
                                    if (is_numeric($valFinal)) $q4ForAvg = $valFinal;
                                    $quarterGrades = array_filter([$gradeEntry->prelim, $gradeEntry->midterm, $gradeEntry->prefinal, $gradeEntry->final], 'is_numeric');
                                    if (count($quarterGrades) > 0) {
                                        $finalSubjGrade = round((array_sum($quarterGrades) / count($quarterGrades)));
                                    }
                                }
                            }

                            if (!is_null($finalSubjGrade)) {
                                    $currentSheet->setCellValue("AA{$currentRow}", $finalSubjGrade);
                                    $currentSheet->setCellValue("AC{$currentRow}", $finalSubjGrade >= 75 ? 'PASSED' : 'FAILED');
                                } else {
                                    $currentSheet->setCellValue("AA{$currentRow}", '');
                                    $currentSheet->setCellValue("AC{$currentRow}", '');
                                }

                            if ($subject->parent_id === null) {
                                if (is_numeric($q1ForAvg)) { $q1Total += $q1ForAvg; $q1Count++; }
                                if (is_numeric($q2ForAvg)) { $q2Total += $q2ForAvg; $q2Count++; }
                                if (is_numeric($q3ForAvg)) { $q3Total += $q3ForAvg; $q3Count++; }
                                if (is_numeric($q4ForAvg)) { $q4Total += $q4ForAvg; $q4Count++; }

                                if (!is_null($finalSubjGrade)) {
                                    $totalFinalGradePoints += $finalSubjGrade;
                                    $subjectCountWithGrades++;
                                }
                            }
                            $currentRow++;
                        }

                        $genAvgQ1 = $q1Count > 0 ? round($q1Total / $q1Count) : '';
                        $genAvgQ2 = $q2Count > 0 ? round($q2Total / $q2Count) : '';
                        $genAvgQ3 = $q3Count > 0 ? round($q3Total / $q3Count) : '';
                        $genAvgQ4 = $q4Count > 0 ? round($q4Total / $q4Count) : '';

                        $currentSheet->setCellValue("V49", $genAvgQ1);
                        $currentSheet->setCellValue("W49", $genAvgQ2);
                        $currentSheet->setCellValue("X49", $genAvgQ3);
                        $currentSheet->setCellValue("Y49", $genAvgQ4);

                        if ($subjectCountWithGrades > 0) {
                            $generalAverage = round(($totalFinalGradePoints / $subjectCountWithGrades));
                            $currentSheet->setCellValue("AA49", $generalAverage);
                            $currentSheet->setCellValue("AC49", $generalAverage >= 75 ? 'Promoted' : 'Failed');
                        } else {
                            $currentSheet->setCellValue("AA49", '');
                            $currentSheet->setCellValue("AC49", '');
                        }
                        break;

                    case 'Grade 3':
                        $currentSheet->setCellValue('B57', 'USUSAN ELEMENTARY SCHOOL');
                        $currentSheet->setCellValue('M57', '136879');
                        $currentSheet->setCellValue('B58', 'Cluster 1');
                        $currentSheet->setCellValue('F58', 'Taguig City and Pateros');
                        $currentSheet->setCellValue('N58', 'NCR');
                        $currentSheet->setCellValue('D59', '3');
                        $currentSheet->setCellValue('F59', $historicalSectionName);
                        $currentSheet->setCellValue('M59', $historicalSchoolYear ? $historicalSchoolYear->school_year_display : 'N/A');
                        $currentSheet->setCellValue('C60', $historicalAdviserName);

                        $currentRow = 65;
                        $totalFinalGradePoints = 0;
                        $subjectCountWithGrades = 0;
                        $q1Total = 0; $q1Count = 0;
                        $q2Total = 0; $q2Count = 0;
                        $q3Total = 0; $q3Count = 0;
                        $q4Total = 0; $q4Count = 0;

                        foreach ($subjectsForThisGrade as $subject) {
                            $currentSheet->setCellValue("A{$currentRow}", $subject->name);

                            $courseModel = Course::with('children')->find($subject->id);
                            $finalSubjGrade = null;
                            $remarks = '';
                            $q1ForAvg = null; $q2ForAvg = null; $q3ForAvg = null; $q4ForAvg = null;


                            if ($courseModel && $courseModel->children && $courseModel->children->count() > 0) {
                                $parentQuarterSums = ['prelim' => 0, 'midterm' => 0, 'prefinal' => 0, 'final' => 0];
                                $parentQuarterCounts = ['prelim' => 0, 'midterm' => 0, 'prefinal' => 0, 'final' => 0];
                                $parentSumOfChildFinalGrades = 0;
                                $parentCountOfChildFinalGrades = 0;

                                foreach ($courseModel->children as $childSubject) {
                                    $childGradeEntry = $gradesForThisYear->where('subject_id', $childSubject->id)->first();
                                    if ($childGradeEntry) {
                                        $childQuarters = ['prelim' => $childGradeEntry->prelim, 'midterm' => $childGradeEntry->midterm, 'prefinal' => $childGradeEntry->prefinal, 'final' => $childGradeEntry->final];
                                        foreach ($childQuarters as $qKey => $qValue) {
                                            if (is_numeric($qValue)) {
                                                $parentQuarterSums[$qKey] += $qValue;
                                                $parentQuarterCounts[$qKey]++;
                                            }
                                        }
                                        $numericChildQuarterGrades = array_filter($childQuarters, 'is_numeric');
                                        if (count($numericChildQuarterGrades) > 0) {
                                            $childFinal = round((array_sum($numericChildQuarterGrades) / count($numericChildQuarterGrades)));
                                            $parentSumOfChildFinalGrades += $childFinal;
                                            $parentCountOfChildFinalGrades++;
                                        }
                                    }
                                }
                                $valPrelim = $parentQuarterCounts['prelim'] > 0 ? round(($parentQuarterSums['prelim'] / $parentQuarterCounts['prelim'])) : '';
                                $valMidterm = $parentQuarterCounts['midterm'] > 0 ? round(($parentQuarterSums['midterm'] / $parentQuarterCounts['midterm'])) : '';
                                $valPrefinal = $parentQuarterCounts['prefinal'] > 0 ? round(($parentQuarterSums['prefinal'] / $parentQuarterCounts['prefinal'])) : '';
                                $valFinal = $parentQuarterCounts['final'] > 0 ? round(($parentQuarterSums['final'] / $parentQuarterCounts['final'])) : '';

                                $currentSheet->setCellValue("F{$currentRow}", $valPrelim);
                                $currentSheet->setCellValue("G{$currentRow}", $valMidterm);
                                $currentSheet->setCellValue("H{$currentRow}", $valPrefinal);
                                $currentSheet->setCellValue("I{$currentRow}", $valFinal);

                                if (is_numeric($valPrelim)) $q1ForAvg = $valPrelim;
                                if (is_numeric($valMidterm)) $q2ForAvg = $valMidterm;
                                if (is_numeric($valPrefinal)) $q3ForAvg = $valPrefinal;
                                if (is_numeric($valFinal)) $q4ForAvg = $valFinal;

                                $finalSubjGrade = $parentCountOfChildFinalGrades > 0 ? round(($parentSumOfChildFinalGrades / $parentCountOfChildFinalGrades)) : null;
                            } else {
                                $gradeEntry = $gradesForThisYear->where('subject_id', $subject->id)->first();
                                $valPrelim = $gradeEntry?->prelim;
                                $valMidterm = $gradeEntry?->midterm;
                                $valPrefinal = $gradeEntry?->prefinal;
                                $valFinal = $gradeEntry?->final;

                                $currentSheet->setCellValue("F{$currentRow}", $valPrelim);
                                $currentSheet->setCellValue("G{$currentRow}", $valMidterm);
                                $currentSheet->setCellValue("H{$currentRow}", $valPrefinal);
                                $currentSheet->setCellValue("I{$currentRow}", $valFinal);

                                if ($gradeEntry) {
                                    if (is_numeric($valPrelim)) $q1ForAvg = $valPrelim;
                                    if (is_numeric($valMidterm)) $q2ForAvg = $valMidterm;
                                    if (is_numeric($valPrefinal)) $q3ForAvg = $valPrefinal;
                                    if (is_numeric($valFinal)) $q4ForAvg = $valFinal;
                                    $quarterGrades = array_filter([$gradeEntry->prelim, $gradeEntry->midterm, $gradeEntry->prefinal, $gradeEntry->final], 'is_numeric');
                                    if (count($quarterGrades) > 0) {
                                        $finalSubjGrade = round((array_sum($quarterGrades) / count($quarterGrades)));
                                    }
                                }
                            }

                            $currentSheet->mergeCells("J{$currentRow}:L{$currentRow}");
                            $currentSheet->mergeCells("M{$currentRow}:N{$currentRow}");

                            if (!is_null($finalSubjGrade)) {
                                $currentSheet->setCellValue("J{$currentRow}", $finalSubjGrade);
                                $currentSheet->setCellValue("M{$currentRow}", $finalSubjGrade >= 75 ? 'PASSED' : 'FAILED');
                            } else {
                                $currentSheet->setCellValue("J{$currentRow}", '');
                                $currentSheet->setCellValue("M{$currentRow}", '');
                            }

                            if ($subject->parent_id === null) {
                                if (is_numeric($q1ForAvg)) { $q1Total += $q1ForAvg; $q1Count++; }
                                if (is_numeric($q2ForAvg)) { $q2Total += $q2ForAvg; $q2Count++; }
                                if (is_numeric($q3ForAvg)) { $q3Total += $q3ForAvg; $q3Count++; }
                                if (is_numeric($q4ForAvg)) { $q4Total += $q4ForAvg; $q4Count++; }

                                if (!is_null($finalSubjGrade)) {
                                    $totalFinalGradePoints += $finalSubjGrade;
                                    $subjectCountWithGrades++;
                                }
                            }
                            $currentRow++;
                        }

                        $genAvgQ1 = $q1Count > 0 ? round($q1Total / $q1Count) : '';
                        $genAvgQ2 = $q2Count > 0 ? round($q2Total / $q2Count) : '';
                        $genAvgQ3 = $q3Count > 0 ? round($q3Total / $q3Count) : '';
                        $genAvgQ4 = $q4Count > 0 ? round($q4Total / $q4Count) : '';

                        $currentSheet->setCellValue("F80", $genAvgQ1);
                        $currentSheet->setCellValue("G80", $genAvgQ2);
                        $currentSheet->setCellValue("H80", $genAvgQ3);
                        $currentSheet->setCellValue("I80", $genAvgQ4);

                        if ($subjectCountWithGrades > 0) {
                            $generalAverage = round(($totalFinalGradePoints / $subjectCountWithGrades));
                            $currentSheet->setCellValue("J80", $generalAverage);
                            $currentSheet->setCellValue("M80", $generalAverage >= 75 ? 'Promoted' : 'Failed');
                        } else {
                            $currentSheet->setCellValue("J80", '');
                            $currentSheet->setCellValue("M80", '');
                        }
                        break;

                    case 'Grade 4':
                        $currentSheet->setCellValue('Q57', 'USUSAN ELEMENTARY SCHOOL');
                        $currentSheet->setCellValue('AB57', '136879');
                        $currentSheet->setCellValue('Q58', 'Cluster 1');
                        $currentSheet->setCellValue('U58', 'Taguig City and Pateros');
                        $currentSheet->setCellValue('AC58', 'NCR');
                        $currentSheet->setCellValue('S59', '4');
                        $currentSheet->setCellValue('U59', $historicalSectionName);
                        $currentSheet->setCellValue('AC59', $historicalSchoolYear ? $historicalSchoolYear->school_year_display : 'N/A');
                        $currentSheet->setCellValue('T60', $historicalAdviserName);

                        $currentRow = 65;
                        $totalFinalGradePoints = 0;
                        $subjectCountWithGrades = 0;
                        $q1Total = 0; $q1Count = 0;
                        $q2Total = 0; $q2Count = 0;
                        $q3Total = 0; $q3Count = 0;
                        $q4Total = 0; $q4Count = 0;

                        foreach ($subjectsForThisGrade as $subject) {
                            $currentSheet->setCellValue("P{$currentRow}", $subject->name);

                            $courseModel = Course::with('children')->find($subject->id);
                            $finalSubjGrade = null;
                            $remarks = '';
                            $q1ForAvg = null; $q2ForAvg = null; $q3ForAvg = null; $q4ForAvg = null;


                            if ($courseModel && $courseModel->children && $courseModel->children->count() > 0) {
                                $parentQuarterSums = ['prelim' => 0, 'midterm' => 0, 'prefinal' => 0, 'final' => 0];
                                $parentQuarterCounts = ['prelim' => 0, 'midterm' => 0, 'prefinal' => 0, 'final' => 0];
                                $parentSumOfChildFinalGrades = 0;
                                $parentCountOfChildFinalGrades = 0;

                                foreach ($courseModel->children as $childSubject) {
                                    $childGradeEntry = $gradesForThisYear->where('subject_id', $childSubject->id)->first();
                                    if ($childGradeEntry) {
                                        $childQuarters = ['prelim' => $childGradeEntry->prelim, 'midterm' => $childGradeEntry->midterm, 'prefinal' => $childGradeEntry->prefinal, 'final' => $childGradeEntry->final];
                                        foreach ($childQuarters as $qKey => $qValue) {
                                            if (is_numeric($qValue)) {
                                                $parentQuarterSums[$qKey] += $qValue;
                                                $parentQuarterCounts[$qKey]++;
                                            }
                                        }
                                        $numericChildQuarterGrades = array_filter($childQuarters, 'is_numeric');
                                        if (count($numericChildQuarterGrades) > 0) {
                                            $childFinal = round((array_sum($numericChildQuarterGrades) / count($numericChildQuarterGrades)));
                                            $parentSumOfChildFinalGrades += $childFinal;
                                            $parentCountOfChildFinalGrades++;
                                        }
                                    }
                                }
                                $valPrelim = $parentQuarterCounts['prelim'] > 0 ? round(($parentQuarterSums['prelim'] / $parentQuarterCounts['prelim'])) : '';
                                $valMidterm = $parentQuarterCounts['midterm'] > 0 ? round(($parentQuarterSums['midterm'] / $parentQuarterCounts['midterm'])) : '';
                                $valPrefinal = $parentQuarterCounts['prefinal'] > 0 ? round(($parentQuarterSums['prefinal'] / $parentQuarterCounts['prefinal'])) : '';
                                $valFinal = $parentQuarterCounts['final'] > 0 ? round(($parentQuarterSums['final'] / $parentQuarterCounts['final'])) : '';

                                $currentSheet->setCellValue("V{$currentRow}", $valPrelim);
                                $currentSheet->setCellValue("W{$currentRow}", $valMidterm);
                                $currentSheet->setCellValue("X{$currentRow}", $valPrefinal);
                                $currentSheet->setCellValue("Y{$currentRow}", $valFinal);

                                if (is_numeric($valPrelim)) $q1ForAvg = $valPrelim;
                                if (is_numeric($valMidterm)) $q2ForAvg = $valMidterm;
                                if (is_numeric($valPrefinal)) $q3ForAvg = $valPrefinal;
                                if (is_numeric($valFinal)) $q4ForAvg = $valFinal;

                                $finalSubjGrade = $parentCountOfChildFinalGrades > 0 ? round(($parentSumOfChildFinalGrades / $parentCountOfChildFinalGrades)) : null;
                            } else {
                                $gradeEntry = $gradesForThisYear->where('subject_id', $subject->id)->first();
                                $valPrelim = $gradeEntry?->prelim;
                                $valMidterm = $gradeEntry?->midterm;
                                $valPrefinal = $gradeEntry?->prefinal;
                                $valFinal = $gradeEntry?->final;

                                $currentSheet->setCellValue("V{$currentRow}", $valPrelim);
                                $currentSheet->setCellValue("W{$currentRow}", $valMidterm);
                                $currentSheet->setCellValue("X{$currentRow}", $valPrefinal);
                                $currentSheet->setCellValue("Y{$currentRow}", $valFinal);

                                if ($gradeEntry) {
                                    if (is_numeric($valPrelim)) $q1ForAvg = $valPrelim;
                                    if (is_numeric($valMidterm)) $q2ForAvg = $valMidterm;
                                    if (is_numeric($valPrefinal)) $q3ForAvg = $valPrefinal;
                                    if (is_numeric($valFinal)) $q4ForAvg = $valFinal;
                                    $quarterGrades = array_filter([$gradeEntry->prelim, $gradeEntry->midterm, $gradeEntry->prefinal, $gradeEntry->final], 'is_numeric');
                                    if (count($quarterGrades) > 0) {
                                        $finalSubjGrade = round((array_sum($quarterGrades) / count($quarterGrades)));
                                    }
                                }
                            }

                            if (!is_null($finalSubjGrade)) {
                                    $currentSheet->setCellValue("AA{$currentRow}", $finalSubjGrade);
                                    $currentSheet->setCellValue("AC{$currentRow}", $finalSubjGrade >= 75 ? 'PASSED' : 'FAILED');
                                } else {
                                    $currentSheet->setCellValue("AA{$currentRow}", '');
                                    $currentSheet->setCellValue("AC{$currentRow}", '');
                                }

                            if ($subject->parent_id === null) {
                                if (is_numeric($q1ForAvg)) { $q1Total += $q1ForAvg; $q1Count++; }
                                if (is_numeric($q2ForAvg)) { $q2Total += $q2ForAvg; $q2Count++; }
                                if (is_numeric($q3ForAvg)) { $q3Total += $q3ForAvg; $q3Count++; }
                                if (is_numeric($q4ForAvg)) { $q4Total += $q4ForAvg; $q4Count++; }

                                if (!is_null($finalSubjGrade)) {
                                    $totalFinalGradePoints += $finalSubjGrade;
                                    $subjectCountWithGrades++;
                                }
                            }
                            $currentRow++;
                        }

                        $genAvgQ1 = $q1Count > 0 ? round($q1Total / $q1Count) : '';
                        $genAvgQ2 = $q2Count > 0 ? round($q2Total / $q2Count) : '';
                        $genAvgQ3 = $q3Count > 0 ? round($q3Total / $q3Count) : '';
                        $genAvgQ4 = $q4Count > 0 ? round($q4Total / $q4Count) : '';

                        $currentSheet->setCellValue("V80", $genAvgQ1);
                        $currentSheet->setCellValue("W80", $genAvgQ2);
                        $currentSheet->setCellValue("X80", $genAvgQ3);
                        $currentSheet->setCellValue("Y80", $genAvgQ4);

                        if ($subjectCountWithGrades > 0) {
                            $generalAverage = round(($totalFinalGradePoints / $subjectCountWithGrades));
                            $currentSheet->setCellValue("AA80", $generalAverage);
                            $currentSheet->setCellValue("AC80", $generalAverage >= 75 ? 'Promoted' : 'Failed');
                        } else {
                            $currentSheet->setCellValue("AA80", '');
                            $currentSheet->setCellValue("AC80", '');
                        }
                        break;

                    case 'Grade 5': 
                        $currentSheet->setCellValue('C3', 'USUSAN ELEMENTARY SCHOOL');
                        $currentSheet->setCellValue('N3', '136879');
                        $currentSheet->setCellValue('C4', 'Cluster 1');
                        $currentSheet->setCellValue('G4', 'Taguig City and Pateros');
                        $currentSheet->setCellValue('O4', 'NCR');
                        $currentSheet->setCellValue('E5', '5');
                        $currentSheet->setCellValue('G5', $historicalSectionName);
                        $currentSheet->setCellValue('N5', $historicalSchoolYear ? $historicalSchoolYear->school_year_display : 'N/A');
                        $currentSheet->setCellValue('F6', $historicalAdviserName);

                        $currentRow = 10;
                        $totalFinalGradePoints = 0;
                        $subjectCountWithGrades = 0;
                        $q1Total = 0; $q1Count = 0;
                        $q2Total = 0; $q2Count = 0;
                        $q3Total = 0; $q3Count = 0;
                        $q4Total = 0; $q4Count = 0;

                        foreach ($subjectsForThisGrade as $subject) {
                            $currentSheet->setCellValue("B{$currentRow}", $subject->name);

                            $courseModel = Course::with('children')->find($subject->id);
                            $finalSubjGrade = null;
                            $remarks = '';
                            $q1ForAvg = null; $q2ForAvg = null; $q3ForAvg = null; $q4ForAvg = null;


                            if ($courseModel && $courseModel->children && $courseModel->children->count() > 0) {
                                $parentQuarterSums = ['prelim' => 0, 'midterm' => 0, 'prefinal' => 0, 'final' => 0];
                                $parentQuarterCounts = ['prelim' => 0, 'midterm' => 0, 'prefinal' => 0, 'final' => 0];
                                $parentSumOfChildFinalGrades = 0;
                                $parentCountOfChildFinalGrades = 0;

                                foreach ($courseModel->children as $childSubject) {
                                    $childGradeEntry = $gradesForThisYear->where('subject_id', $childSubject->id)->first();
                                    if ($childGradeEntry) {
                                        $childQuarters = ['prelim' => $childGradeEntry->prelim, 'midterm' => $childGradeEntry->midterm, 'prefinal' => $childGradeEntry->prefinal, 'final' => $childGradeEntry->final];
                                        foreach ($childQuarters as $qKey => $qValue) {
                                            if (is_numeric($qValue)) {
                                                $parentQuarterSums[$qKey] += $qValue;
                                                $parentQuarterCounts[$qKey]++;
                                            }
                                        }
                                        $numericChildQuarterGrades = array_filter($childQuarters, 'is_numeric');
                                        if (count($numericChildQuarterGrades) > 0) {
                                            $childFinal = round((array_sum($numericChildQuarterGrades) / count($numericChildQuarterGrades)));
                                            $parentSumOfChildFinalGrades += $childFinal;
                                            $parentCountOfChildFinalGrades++;
                                        }
                                    }
                                }
                                $valPrelim = $parentQuarterCounts['prelim'] > 0 ? round(($parentQuarterSums['prelim'] / $parentQuarterCounts['prelim'])) : '';
                                $valMidterm = $parentQuarterCounts['midterm'] > 0 ? round(($parentQuarterSums['midterm'] / $parentQuarterCounts['midterm'])) : '';
                                $valPrefinal = $parentQuarterCounts['prefinal'] > 0 ? round(($parentQuarterSums['prefinal'] / $parentQuarterCounts['prefinal'])) : '';
                                $valFinal = $parentQuarterCounts['final'] > 0 ? round(($parentQuarterSums['final'] / $parentQuarterCounts['final'])) : '';

                                $currentSheet->setCellValue("G{$currentRow}", $valPrelim);
                                $currentSheet->setCellValue("H{$currentRow}", $valMidterm);
                                $currentSheet->setCellValue("I{$currentRow}", $valPrefinal);
                                $currentSheet->setCellValue("J{$currentRow}", $valFinal);

                                if (is_numeric($valPrelim)) $q1ForAvg = $valPrelim;
                                if (is_numeric($valMidterm)) $q2ForAvg = $valMidterm;
                                if (is_numeric($valPrefinal)) $q3ForAvg = $valPrefinal;
                                if (is_numeric($valFinal)) $q4ForAvg = $valFinal;

                                $finalSubjGrade = $parentCountOfChildFinalGrades > 0 ? round(($parentSumOfChildFinalGrades / $parentCountOfChildFinalGrades)) : null;
                            } else {
                                $gradeEntry = $gradesForThisYear->where('subject_id', $subject->id)->first();
                                $valPrelim = $gradeEntry?->prelim;
                                $valMidterm = $gradeEntry?->midterm;
                                $valPrefinal = $gradeEntry?->prefinal;
                                $valFinal = $gradeEntry?->final;

                                $currentSheet->setCellValue("G{$currentRow}", $valPrelim);
                                $currentSheet->setCellValue("H{$currentRow}", $valMidterm);
                                $currentSheet->setCellValue("I{$currentRow}", $valPrefinal);
                                $currentSheet->setCellValue("J{$currentRow}", $valFinal);

                                if ($gradeEntry) {
                                    if (is_numeric($valPrelim)) $q1ForAvg = $valPrelim;
                                    if (is_numeric($valMidterm)) $q2ForAvg = $valMidterm;
                                    if (is_numeric($valPrefinal)) $q3ForAvg = $valPrefinal;
                                    if (is_numeric($valFinal)) $q4ForAvg = $valFinal;
                                    $quarterGrades = array_filter([$gradeEntry->prelim, $gradeEntry->midterm, $gradeEntry->prefinal, $gradeEntry->final], 'is_numeric');
                                    if (count($quarterGrades) > 0) {
                                        $finalSubjGrade = round((array_sum($quarterGrades) / count($quarterGrades)));
                                    }
                                }
                            }

                            $currentSheet->mergeCells("K{$currentRow}:M{$currentRow}");
                            $currentSheet->mergeCells("N{$currentRow}:O{$currentRow}");

                            if (!is_null($finalSubjGrade)) {
                                $currentSheet->setCellValue("K{$currentRow}", $finalSubjGrade);
                                $currentSheet->setCellValue("N{$currentRow}", $finalSubjGrade >= 75 ? 'PASSED' : 'FAILED');
                            } else {
                                $currentSheet->setCellValue("K{$currentRow}", '');
                                $currentSheet->setCellValue("N{$currentRow}", '');
                            }

                            if ($subject->parent_id === null) {
                                if (is_numeric($q1ForAvg)) { $q1Total += $q1ForAvg; $q1Count++; }
                                if (is_numeric($q2ForAvg)) { $q2Total += $q2ForAvg; $q2Count++; }
                                if (is_numeric($q3ForAvg)) { $q3Total += $q3ForAvg; $q3Count++; }
                                if (is_numeric($q4ForAvg)) { $q4Total += $q4ForAvg; $q4Count++; }

                                if (!is_null($finalSubjGrade)) {
                                    $totalFinalGradePoints += $finalSubjGrade;
                                    $subjectCountWithGrades++;
                                }
                            }
                            $currentRow++;
                        }

                        $genAvgQ1 = $q1Count > 0 ? round($q1Total / $q1Count) : '';
                        $genAvgQ2 = $q2Count > 0 ? round($q2Total / $q2Count) : '';
                        $genAvgQ3 = $q3Count > 0 ? round($q3Total / $q3Count) : '';
                        $genAvgQ4 = $q4Count > 0 ? round($q4Total / $q4Count) : '';

                        $currentSheet->setCellValue("G25", $genAvgQ1);
                        $currentSheet->setCellValue("H25", $genAvgQ2);
                        $currentSheet->setCellValue("I25", $genAvgQ3);
                        $currentSheet->setCellValue("J25", $genAvgQ4);

                        if ($subjectCountWithGrades > 0) {
                            $generalAverage = round(($totalFinalGradePoints / $subjectCountWithGrades));
                            $currentSheet->setCellValue("K25", $generalAverage);
                            $currentSheet->setCellValue("N25", $generalAverage >= 75 ? 'Promoted' : 'Failed');
                        } else {
                            $currentSheet->setCellValue("K25", '');
                            $currentSheet->setCellValue("N25", '');
                        }
                        break;

                    case 'Grade 6': 
                        $currentSheet->setCellValue('R3', 'USUSAN ELEMENTARY SCHOOL');
                        $currentSheet->setCellValue('AC3', '136879');
                        $currentSheet->setCellValue('R4', 'Cluster 1');
                        $currentSheet->setCellValue('V4', 'Taguig City and Pateros');
                        $currentSheet->setCellValue('AD4', 'NCR');
                        $currentSheet->setCellValue('T5', '6');
                        $currentSheet->setCellValue('V5', $historicalSectionName);
                        $currentSheet->setCellValue('AD5', $historicalSchoolYear ? $historicalSchoolYear->school_year_display : 'N/A');
                        $currentSheet->setCellValue('U6', $historicalAdviserName);

                        $currentRow = 10;
                        $totalFinalGradePoints = 0;
                        $subjectCountWithGrades = 0;
                        $q1Total = 0; $q1Count = 0;
                        $q2Total = 0; $q2Count = 0;
                        $q3Total = 0; $q3Count = 0;
                        $q4Total = 0; $q4Count = 0;

                        foreach ($subjectsForThisGrade as $subject) {
                            $currentSheet->setCellValue("Q{$currentRow}", $subject->name);

                            $courseModel = Course::with('children')->find($subject->id);
                            $finalSubjGrade = null;
                            $remarks = '';
                            $q1ForAvg = null; $q2ForAvg = null; $q3ForAvg = null; $q4ForAvg = null;


                            if ($courseModel && $courseModel->children && $courseModel->children->count() > 0) {
                                $parentQuarterSums = ['prelim' => 0, 'midterm' => 0, 'prefinal' => 0, 'final' => 0];
                                $parentQuarterCounts = ['prelim' => 0, 'midterm' => 0, 'prefinal' => 0, 'final' => 0];
                                $parentSumOfChildFinalGrades = 0;
                                $parentCountOfChildFinalGrades = 0;

                                foreach ($courseModel->children as $childSubject) {
                                    $childGradeEntry = $gradesForThisYear->where('subject_id', $childSubject->id)->first();
                                    if ($childGradeEntry) {
                                        $childQuarters = ['prelim' => $childGradeEntry->prelim, 'midterm' => $childGradeEntry->midterm, 'prefinal' => $childGradeEntry->prefinal, 'final' => $childGradeEntry->final];
                                        foreach ($childQuarters as $qKey => $qValue) {
                                            if (is_numeric($qValue)) {
                                                $parentQuarterSums[$qKey] += $qValue;
                                                $parentQuarterCounts[$qKey]++;
                                            }
                                        }
                                        $numericChildQuarterGrades = array_filter($childQuarters, 'is_numeric');
                                        if (count($numericChildQuarterGrades) > 0) {
                                            $childFinal = round((array_sum($numericChildQuarterGrades) / count($numericChildQuarterGrades)));
                                            $parentSumOfChildFinalGrades += $childFinal;
                                            $parentCountOfChildFinalGrades++;
                                        }
                                    }
                                }
                                $valPrelim = $parentQuarterCounts['prelim'] > 0 ? round(($parentQuarterSums['prelim'] / $parentQuarterCounts['prelim'])) : '';
                                $valMidterm = $parentQuarterCounts['midterm'] > 0 ? round(($parentQuarterSums['midterm'] / $parentQuarterCounts['midterm'])) : '';
                                $valPrefinal = $parentQuarterCounts['prefinal'] > 0 ? round(($parentQuarterSums['prefinal'] / $parentQuarterCounts['prefinal'])) : '';
                                $valFinal = $parentQuarterCounts['final'] > 0 ? round(($parentQuarterSums['final'] / $parentQuarterCounts['final'])) : '';

                                $currentSheet->setCellValue("W{$currentRow}", $valPrelim);
                                $currentSheet->setCellValue("X{$currentRow}", $valMidterm);
                                $currentSheet->setCellValue("Y{$currentRow}", $valPrefinal);
                                $currentSheet->setCellValue("Z{$currentRow}", $valFinal);

                                if (is_numeric($valPrelim)) $q1ForAvg = $valPrelim;
                                if (is_numeric($valMidterm)) $q2ForAvg = $valMidterm;
                                if (is_numeric($valPrefinal)) $q3ForAvg = $valPrefinal;
                                if (is_numeric($valFinal)) $q4ForAvg = $valFinal;

                                $finalSubjGrade = $parentCountOfChildFinalGrades > 0 ? round(($parentSumOfChildFinalGrades / $parentCountOfChildFinalGrades)) : null;
                            } else {
                                $gradeEntry = $gradesForThisYear->where('subject_id', $subject->id)->first();
                                $valPrelim = $gradeEntry?->prelim;
                                $valMidterm = $gradeEntry?->midterm;
                                $valPrefinal = $gradeEntry?->prefinal;
                                $valFinal = $gradeEntry?->final;

                                $currentSheet->setCellValue("W{$currentRow}", $valPrelim);
                                $currentSheet->setCellValue("X{$currentRow}", $valMidterm);
                                $currentSheet->setCellValue("Y{$currentRow}", $valPrefinal);
                                $currentSheet->setCellValue("Z{$currentRow}", $valFinal);

                                if ($gradeEntry) {
                                    if (is_numeric($valPrelim)) $q1ForAvg = $valPrelim;
                                    if (is_numeric($valMidterm)) $q2ForAvg = $valMidterm;
                                    if (is_numeric($valPrefinal)) $q3ForAvg = $valPrefinal;
                                    if (is_numeric($valFinal)) $q4ForAvg = $valFinal;
                                    $quarterGrades = array_filter([$gradeEntry->prelim, $gradeEntry->midterm, $gradeEntry->prefinal, $gradeEntry->final], 'is_numeric');
                                    if (count($quarterGrades) > 0) {
                                        $finalSubjGrade = round((array_sum($quarterGrades) / count($quarterGrades)));
                                    }
                                }
                            }

                            $currentSheet->mergeCells("AD{$currentRow}:AE{$currentRow}");
                            
                            if (!is_null($finalSubjGrade)) {
                                $currentSheet->setCellValue("AB{$currentRow}", $finalSubjGrade);
                                $currentSheet->setCellValue("AD{$currentRow}", $finalSubjGrade >= 75 ? 'PASSED' : 'FAILED');
                            } else {
                                $currentSheet->setCellValue("AB{$currentRow}", '');
                                $currentSheet->setCellValue("AD{$currentRow}", '');
                            }

                            if ($subject->parent_id === null) {
                                if (is_numeric($q1ForAvg)) { $q1Total += $q1ForAvg; $q1Count++; }
                                if (is_numeric($q2ForAvg)) { $q2Total += $q2ForAvg; $q2Count++; }
                                if (is_numeric($q3ForAvg)) { $q3Total += $q3ForAvg; $q3Count++; }
                                if (is_numeric($q4ForAvg)) { $q4Total += $q4ForAvg; $q4Count++; }

                                if (!is_null($finalSubjGrade)) {
                                    $totalFinalGradePoints += $finalSubjGrade;
                                    $subjectCountWithGrades++;
                                }
                            }
                            $currentRow++;
                        }

                        $genAvgQ1 = $q1Count > 0 ? round($q1Total / $q1Count) : '';
                        $genAvgQ2 = $q2Count > 0 ? round($q2Total / $q2Count) : '';
                        $genAvgQ3 = $q3Count > 0 ? round($q3Total / $q3Count) : '';
                        $genAvgQ4 = $q4Count > 0 ? round($q4Total / $q4Count) : '';

                        $currentSheet->setCellValue("W25", $genAvgQ1);
                        $currentSheet->setCellValue("X25", $genAvgQ2);
                        $currentSheet->setCellValue("Y25", $genAvgQ3);
                        $currentSheet->setCellValue("Z25", $genAvgQ4);

                        if ($subjectCountWithGrades > 0) {
                            $generalAverage = round(($totalFinalGradePoints / $subjectCountWithGrades));
                            $currentSheet->setCellValue("AB25", $generalAverage);
                            $currentSheet->setCellValue("AD25", $generalAverage >= 75 ? 'Promoted' : 'Failed');
                        } else {
                            $currentSheet->setCellValue("AB25", '');
                            $currentSheet->setCellValue("AD25", '');
                        }
                        break;
                }
            }
            $spreadsheet->setActiveSheetIndex(0);

            // Generate file
            $writer = new Xlsx($spreadsheet);
            $filename = "SF10_{$student->student_id}_{$student->last_name}_{$student->first_name}.xlsx";
    
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="'. $filename .'"');
            header('Cache-Control: max-age=0');
    
            $writer->save('php://output');

            $this->logActivity(
                'export',
                "Exported SF10 for student {$student->first_name} {$student->last_name}"
            );

            exit;

        } catch (\Exception $e) {
            $this->logActivity(
                'export',
                "Warning: Error exporting SF10 for student {$student->first_name} {$student->last_name}: " . $e->getMessage()
            );
            return back()->with('error', 'Error generating SF10: ' . $e->getMessage());
            
        }
    }
    private function parseSchoolYearString(?string $syString): ?int
    {
        if (empty($syString) || !preg_match('/(\d{4})-(\d{4})/', $syString, $matches)) {
            return null;
        }
        $startYear = $matches[1];
        $schoolYear = SchoolYear::where('start_year', $startYear)->first();
        return $schoolYear ? $schoolYear->id : null;
    }

    private function processGradeSection(
        \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet,
        Student $student,
        string $expectedGradeLevelName,
        string $schoolYearCell,
        string $gradeLevelCell, 
        string $subjectStartCellCol,
        int $subjectStartRow,
        string $sectionCell, 
        string $adviserCell,
        string $q1Col, string $q2Col, string $q3Col, string $q4Col, 
        array &$updatedGradesData 
    ): void {
        $syString = $sheet->getCell($schoolYearCell)->getValue();
        $gradeLevelNumStr = (string) $sheet->getCell($gradeLevelCell)->getValue(); 

        $gradeLevelMap = ['1' => 'Grade 1', '2' => 'Grade 2', '3' => 'Grade 3', '4' => 'Grade 4', '5' => 'Grade 5', '6' => 'Grade 6'];
        $parsedGradeLevelName = $gradeLevelMap[$gradeLevelNumStr] ?? null;

        // Validate that the grade level found in the sheet matches what we expect for this section
        if ($parsedGradeLevelName !== $expectedGradeLevelName) {
            \Log::info("SF10 Import: Skipping section. Expected '{$expectedGradeLevelName}' but found '{$gradeLevelNumStr}' (maps to '{$parsedGradeLevelName}') in grade cell '{$gradeLevelCell}'. SY string: '{$syString}'.");
            return;
        }

        $schoolYearId = $this->parseSchoolYearString($syString);
        if (!$schoolYearId) {
            \Log::info("SF10 Import: Could not parse school year '{$syString}' from cell '{$schoolYearCell}' for {$expectedGradeLevelName}. Skipping section.");
            return;
        }

        // Process Enrollment History
        $sectionNameFromExcel = trim((string)$sheet->getCell($sectionCell)->getValue());
        $adviserNameFromExcel = trim((string)$sheet->getCell($adviserCell)->getValue());
        $sectionId = null;
        $adviserId = null;

        if (!empty($sectionNameFromExcel)) {
            $section = Section::where('name', $sectionNameFromExcel)
                                ->where('school_year_id', $schoolYearId)
                                ->where('grade_level', $parsedGradeLevelName)
                                ->first();
            if ($section) {
                $sectionId = $section->id;
            } else {
                \Log::warning("SF10 Import: Section '{$sectionNameFromExcel}' for SY ID {$schoolYearId}, Grade '{$parsedGradeLevelName}' not found.");
            }
        }

        if (!empty($adviserNameFromExcel)) {
            $adviser = User::where('name', $adviserNameFromExcel)->where('role', 'teacher')->first();
            if ($adviser) {
                $adviserId = $adviser->id;
            } else {
                \Log::warning("SF10 Import: Adviser '{$adviserNameFromExcel}' not found.");
            }
        }

        $currentRow = $subjectStartRow;
        $subjectsProcessedCount = 0;
        // Limit to a reasonable number of subjects to prevent infinite loops on malformed files
        for ($i = 0; $i < 20; $i++) { 
            $subjectName = $sheet->getCell("{$subjectStartCellCol}{$currentRow}")->getValue();
            if (empty(trim((string)$subjectName))) { // Stop if subject name is empty or only whitespace
                break;
            }

            $q1Val = $sheet->getCell("{$q1Col}{$currentRow}")->getValue();
            $q2Val = $sheet->getCell("{$q2Col}{$currentRow}")->getValue();
            $q3Val = $sheet->getCell("{$q3Col}{$currentRow}")->getValue();
            $q4Val = $sheet->getCell("{$q4Col}{$currentRow}")->getValue();

            $courseId = Course::where('name', $subjectName)
                                ->where('grade_level', $parsedGradeLevelName)
                                ->value('id');

            if ($courseId) {
                $gradeRecord = Grade::updateOrCreate(
                    ['student_id' => $student->id, 'subject_id' => $courseId, 'school_year_id' => $schoolYearId],
                    [
                        'prelim' => is_numeric($q1Val) ? (float)$q1Val : null,
                        'midterm' => is_numeric($q2Val) ? (float)$q2Val : null,
                        'prefinal' => is_numeric($q3Val) ? (float)$q3Val : null,
                        'final' => is_numeric($q4Val) ? (float)$q4Val : null,
                    ]
                );
                $updatedGradesData[] = $gradeRecord->refresh()->toArray();
                $subjectsProcessedCount++;
            } else {
                \Log::warning("SF10 Import: Course '{$subjectName}' for grade '{$parsedGradeLevelName}' (SY: {$syString}) not found. Skipping this subject.");
            }
            $currentRow++;
        }

        // Create or update enrollment history if we have the necessary info
        if ($schoolYearId && $parsedGradeLevelName) {
            StudentEnrollmentHistory::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'school_year_id' => $schoolYearId,
                    'grade_level' => $parsedGradeLevelName,
                ],
                [
                    'section_id' => $sectionId,
                    'adviser_id' => $adviserId,
                ]
            );
            \Log::info("SF10 Import: Processed enrollment history for {$expectedGradeLevelName}, SY: {$syString} (ID: {$schoolYearId}). Section: {$sectionNameFromExcel} (ID: {$sectionId}), Adviser: {$adviserNameFromExcel} (ID: {$adviserId}).");
        }
        \Log::info("SF10 Import: Processed {$subjectsProcessedCount} subjects for {$expectedGradeLevelName}, SY: {$syString} (ID: {$schoolYearId}).");
    }

    public function handleExcelUpload(Request $request, Student $student)
        {
            $request->validate([
                'excel_file' => 'required|file|mimes:xlsx,xls',
                'selected_school_year_id' => 'required|exists:school_years,id' // Validate the passed SY ID
            ], [
                'excel_file.required' => 'No file uploaded.',
                'excel_file.mimes' => 'File must be Excel format (.xlsx or .xls).',
                'selected_school_year_id.required' => 'School year context is missing for the Excel upload.',
                'selected_school_year_id.exists' => 'Invalid school year selected for the Excel upload.',
            ]);

            $file = $request->file('excel_file');
            $selectedSchoolYearId = $request->input('selected_school_year_id');

            try {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
                $updatedGradesData = [];

                // Process Sheet 0 (Grades 1-4)
                if ($spreadsheet->getSheetCount() > 0) {
                    $sheet0 = $spreadsheet->getSheet(0);
                    $this->processGradeSection($sheet0, $student, 'Grade 1', 'M29', 'D29', 'A', 34, 'F29', 'E30', 'F', 'G', 'H', 'I', $updatedGradesData);
                    $this->processGradeSection($sheet0, $student, 'Grade 2', 'AC29', 'S29', 'P', 34, 'U29', 'T30', 'V', 'W', 'X', 'Y', $updatedGradesData);
                    $this->processGradeSection($sheet0, $student, 'Grade 3', 'M59', 'D59', 'A', 65, 'F59', 'C60', 'F', 'G', 'H', 'I', $updatedGradesData); // Used C60 for G3 adviser as per export
                    $this->processGradeSection($sheet0, $student, 'Grade 4', 'AC59', 'S59', 'P', 65, 'U59', 'T60', 'V', 'W', 'X', 'Y', $updatedGradesData);
                }

                // Process Sheet 1 (Grades 5-6)
                if ($spreadsheet->getSheetCount() > 1) {
                    $sheet1 = $spreadsheet->getSheet(1);
                    $this->processGradeSection($sheet1, $student, 'Grade 5', 'N5', 'E5', 'B', 10, 'G5', 'F6', 'G', 'H', 'I', 'J', $updatedGradesData);
                    $this->processGradeSection($sheet1, $student, 'Grade 6', 'AD5', 'T5', 'Q', 10, 'V5', 'U6', 'W', 'X', 'Y', 'Z', $updatedGradesData);
                }

                if (empty($updatedGradesData)) {
                     return response()->json([
                        'success' => false,
                        'message' => 'No relevant grade data found in the Excel file matching the SF10 template structure.',
                    ], 400);
                }

                $this->logActivity(
                    'import',
                    "Imported grades from SF10 Excel for student {$student->first_name} {$student->last_name}. Context SY ID from UI: {$selectedSchoolYearId}."
                );

                return response()->json([
                    'success' => true,
                    'message' => 'Grades imported successfully.',
                    'updated_grades' => $updatedGradesData,
                    'student_info' => [
                        'first_name' => $student->first_name,
                        'last_name' => $student->last_name,
                    ]
                ]);

            } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {
                \Log::error("Error loading/processing Excel file (PhpSpreadsheet): " . $e->getMessage(), ['student_id' => $student->id, 'file' => $file->getClientOriginalName()]);
                return response()->json([
                    'success' => false,
                    'message' => 'Error processing Excel file structure or content. Ensure the template is correct.',
                    'error' => 'Error processing Excel file structure or content: ' . $e->getMessage(),
                    'error_detail' => $e->getMessage()
                ], 500);
            } catch (\Exception $e) {
                \Log::error("General error processing Excel file: " . $e->getMessage(), [
                    'student_id' => $student->id,
                    'file' => $file->getClientOriginalName(),
                    'trace' => $e->getTraceAsString()
                ]);
                return response()->json([
                    'error' => 'Error processing Excel file: ' . $e->getMessage()
                ], 500);
            }
        }
    public function schedule(Request $request)
    {
        $sections = Section::where('is_active', true)
            ->orderBy('grade_level')
            ->orderBy('name')
            ->get();
    
        $schedules = Schedule::where('teacher_id', $request->user()->id)
            ->with(['course', 'section', 'schoolYear'])
            ->get();
    
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        $selectedDay = $request->get('day', 'Monday');
    
        $timeSlots = [
            '07:00 - 08:00',
            '08:00 - 09:00',
            '09:00 - 10:00',
            '10:00 - 11:00',
            '11:00 - 12:00',
            '13:00 - 14:00',
            '14:00 - 15:00',
            '15:00 - 16:00',
            '16:00 - 17:00',
            '17:00 - 18:00'
        ];
    
        // Filter schedules by day and group by section
        $scheduledSections = $schedules
            ->filter(function ($schedule) use ($selectedDay) {
                return $schedule->day_of_week === $selectedDay;
            })
            ->map(function ($schedule) {
                $schedule->start_time = date('H:i', strtotime($schedule->start_time));
                $schedule->end_time = date('H:i', strtotime($schedule->end_time));
                return $schedule;
            })
            ->groupBy('section_id');
    
        return view('teacher.schedule', compact('schedules', 'sections', 'timeSlots', 'scheduledSections', 'days', 'selectedDay'));
    }
}