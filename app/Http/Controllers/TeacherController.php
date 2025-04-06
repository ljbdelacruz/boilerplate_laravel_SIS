<?php

namespace App\Http\Controllers;

use App\Models\SchoolYear;
use App\Models\Section;
use App\Models\Student;
use App\Models\User;
use App\Models\Course;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class TeacherController extends Controller
{
    public function list(){
        $teachers = User::where('role', 'teacher')
                       ->orderBy('name')
                       ->get();
        
        return view('admin.teachers.index', compact('teachers'));
    }
    public function index()
    {
        $schoolYears = SchoolYear::where('is_archived', false)
                                ->orderBy('school_year', 'desc')
                                ->get();
        $sections = Section::orderBy('grade_level')->orderBy('name')->get();
        
        return view('dashboard.teacher', compact('schoolYears', 'sections'));
    }

    public function viewStudents(Request $request)
    {
        $schoolYears = SchoolYear::where('is_archived', false)
                                ->orderBy('school_year', 'desc')
                                ->get();
        $sections = Section::orderBy('grade_level')->orderBy('name')->get();
        
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
        $validated = $request->validate([
            'subject_id' => 'required|exists:courses,id',
            'school_year_id' => 'required|exists:school_years,id',
            'prelim' => 'nullable|numeric|min:0|max:100',
            'midterm' => 'nullable|numeric|min:0|max:100',
            'prefinal' => 'nullable|numeric|min:0|max:100',
            'final' => 'nullable|numeric|min:0|max:100',
        ]);
    
        $student->grades()->updateOrCreate(
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
    
        return response()->json(['success' => true]);
    }

    public function submitGrades(Student $student, Request $request)
    {
        $currentTeacher = $request()->user();
        $teacherCourses = Course::whereHas('teachers', function($query) use ($currentTeacher) {
            $query->where('user_id', $currentTeacher->id);
        })->get();
        
        $schoolYearId = $request->school_year_id;

        return view('dashboard.submit-grades', [
            'student' => $student,
            'grades' => $student->grades()->where('school_year_id', $schoolYearId)->get(),
            'teacherCourses' => $teacherCourses,
            'schoolYearId' => $schoolYearId
        ]);
    }

    public function edit(User $teacher)
    {
        return view('admin.teachers.edit', compact('teacher'));
    }

    public function update(Request $request, User $teacher)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $teacher->id,
        ]);

        $teacher->update($validated);

        return redirect()->route('teachers.index')->with('success', 'Teacher updated successfully');
    }

    public function destroy(User $teacher)
    {
        $teacher->delete();
        return redirect()->route('teachers.index')->with('success', 'Teacher deleted successfully');
    }

    public function showSF10(Student $student)
    {
        $student->load(['grades', 'section']); // Eager load the relationships
        $schoolYears = SchoolYear::orderBy('school_year', 'desc')->get();
        $subjects = Course::where('grade_level', $student->grade_level)->get();
        
        return view('dashboard.sf10', [
            'student' => $student,
            'schoolYears' => $schoolYears,
            'subjects' => $subjects
        ]);
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
        $validated = $request->validate([
            'grades' => 'required|array',
            'grades.*.subject_id' => 'required|exists:courses,id',
            'grades.*.quarter' => 'required|in:1,2,3,4',
            'grades.*.grade' => 'nullable|numeric|min:60|max:100',
        ]);
    
        foreach ($validated['grades'] as $gradeData) {
            $grade = $student->grades()->firstOrCreate([
                'subject_id' => $gradeData['subject_id'],
                'school_year_id' => $student->school_year_id,
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
    
        return response()->json(['success' => true]);
    }

    public function exportSF10(Student $student)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
    
        // School Information
        $sheet->setCellValue('A1', 'Republic of the Philippines');
        $sheet->setCellValue('A2', 'Department of Education');
        $sheet->setCellValue('A3', 'Region VII');
        $sheet->setCellValue('A4', 'Division of Cebu Province');
        $sheet->mergeCells('A6:K6');
        $sheet->setCellValue('A6', 'LEARNER\'S PERMANENT ACADEMIC RECORD FOR ELEMENTARY SCHOOL');
        $sheet->getStyle('A6')->getAlignment()->setHorizontal('center');
    
        // Student Information
        $sheet->setCellValue('A8', 'LAST NAME:');
        $sheet->setCellValue('C8', $student->last_name);
        $sheet->setCellValue('E8', 'FIRST NAME:');
        $sheet->setCellValue('G8', $student->first_name);
        $sheet->setCellValue('I8', 'NAME EXT. (Jr,I,II):');
        $sheet->setCellValue('K8', '');
        $sheet->setCellValue('A9', 'MIDDLE NAME:');
        $sheet->setCellValue('C9', $student->middle_name ?? '');
        $sheet->setCellValue('E9', 'LRN:');
        $sheet->setCellValue('G9', $student->student_id);
    
        // Scholastic Record Header
        $sheet->setCellValue('A11', 'SCHOLASTIC RECORD');
        
        // Grade Headers
        $startRow = 13;
        $sheet->setCellValue('A'.$startRow, 'School:');
        $sheet->setCellValue('E'.$startRow, 'School ID:');
        $sheet->setCellValue('H'.$startRow, 'District:');
        $sheet->setCellValue('J'.$startRow, 'Division:');
        
        $startRow++;
        $sheet->setCellValue('A'.$startRow, 'Grade Level:');
        $sheet->setCellValue('C'.$startRow, $student->grade_level);
        $sheet->setCellValue('E'.$startRow, 'Section:');
        $sheet->setCellValue('G'.$startRow, $student->section->name);
        $sheet->setCellValue('I'.$startRow, 'School Year:');
        $sheet->setCellValue('K'.$startRow, $student->schoolYear->school_year);
    
        // Grades Table Header
        $startRow += 2;
        $sheet->setCellValue('A'.$startRow, 'LEARNING AREAS');
        $sheet->setCellValue('E'.$startRow, 'Quarter Rating');
        $sheet->mergeCells('E'.$startRow.':H'.$startRow);
        $sheet->setCellValue('I'.$startRow, 'FINAL');
        $sheet->setCellValue('J'.$startRow, 'REMARKS');
        
        $startRow++;
        $sheet->setCellValue('E'.($startRow), '1');
        $sheet->setCellValue('F'.($startRow), '2');
        $sheet->setCellValue('G'.($startRow), '3');
        $sheet->setCellValue('H'.($startRow), '4');
        $sheet->setCellValue('I'.($startRow), 'RATING');
    
        // Subjects and Grades
        $startRow++;
        $subjects = Course::where('grade_level', $student->grade_level)->get();
        foreach ($subjects as $subject) {
            $grade = $student->grades->where('subject_id', $subject->id)->first();
            
            $sheet->mergeCells('A'.$startRow.':D'.$startRow);
            $sheet->setCellValue('A'.$startRow, $subject->name);
            $sheet->setCellValue('E'.$startRow, $grade?->prelim);
            $sheet->setCellValue('F'.$startRow, $grade?->midterm);
            $sheet->setCellValue('G'.$startRow, $grade?->prefinal);
            $sheet->setCellValue('H'.$startRow, $grade?->final);
            
            // Calculate final rating
            $grades = [$grade?->prelim, $grade?->midterm, $grade?->prefinal, $grade?->final];
            $grades = array_filter($grades, function($grade) { return !is_null($grade); });
            $finalGrade = count($grades) > 0 ? round(array_sum($grades) / count($grades)) : null;
            
            $sheet->setCellValue('I'.$startRow, $finalGrade);
            $sheet->setCellValue('J'.$startRow, $finalGrade >= 75 ? 'PASSED' : ($finalGrade ? 'FAILED' : ''));
            
            $startRow++;
        }
    
        // General Average
        $sheet->mergeCells('A'.$startRow.':D'.$startRow);
        $sheet->setCellValue('A'.$startRow, 'General Average');
    
        // Calculate General Average
        $finalGrades = [];
        foreach ($subjects as $subject) {
            $grade = $student->grades->where('subject_id', $subject->id)->first();
            if ($grade) {
                $grades = [$grade->prelim, $grade->midterm, $grade->prefinal, $grade->final];
                $grades = array_filter($grades, function($g) { return !is_null($g); });
                if (count($grades) > 0) {
                    $finalGrades[] = array_sum($grades) / count($grades);
                }
            }
        }
        $generalAverage = count($finalGrades) > 0 ? round(array_sum($finalGrades) / count($finalGrades)) : null;
        $sheet->setCellValue('I'.$startRow, $generalAverage);
        $sheet->setCellValue('J'.$startRow, $generalAverage >= 75 ? 'PASSED' : ($generalAverage ? 'FAILED' : ''));
    
        // Attendance Record
        $startRow += 2;
        $sheet->setCellValue('A'.$startRow, 'ATTENDANCE RECORD');
    
        $startRow++;
        $months = ['Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'TOTAL'];
        $col = 'A';
        foreach ($months as $month) {
            $sheet->setCellValue($col.$startRow, $month);
            $col++;
        }
    
        // Styling
        $sheet->getStyle('A6')->getFont()->setBold(true);
        $sheet->getStyle('A11')->getFont()->setBold(true);
    
        // Auto-size columns
        foreach(range('A','K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    
        $writer = new Xlsx($spreadsheet);
        $filename = "SF10_{$student->student_id}.xlsx";
    
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. $filename .'"');
        header('Cache-Control: max-age=0');
    
        $writer->save('php://output');
        exit;
    }
    public function handleExcelUpload(Request $request, Student $student)
        {
            if (!$request->hasFile('excel_file')) {
                return response()->json(['error' => 'No file uploaded'], 400);
            }
    
            $file = $request->file('excel_file');
            
            try {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
                $worksheet = $spreadsheet->getActiveSheet();
                
                // Get values from cells
                $lastName = $worksheet->getCell('D9')->getValue() ?? '';
                $firstName = $worksheet->getCell('M9')->getValue() ?? '';
                $kinder1Prelim = $worksheet->getCell('G31')->getValue() ?? '';
                $kinder1Midterm = $worksheet->getCell('H31')->getValue() ?? '';
                $kinder1Prefi = $worksheet->getCell('I31')->getValue() ?? '';  // Changed from PreFi to Prefi
                $kinder1Final = $worksheet->getCell('J31')->getValue() ?? '';
    
                return response()->json([
                    'success' => true,
                    'lastName' => $lastName,
                    'firstName' => $firstName,
                    'kinder1_prelim' => $kinder1Prelim,
                    'kinder1_midterm' => $kinder1Midterm,
                    'kinder1_prefi' => $kinder1Prefi,  // Changed from prefinal to prefi
                    'kinder1_final' => $kinder1Final,
                ]);
            } catch (\Exception $e) {
                return response()->json(['error' => 'Error processing Excel file'], 500);
            }
        }
}