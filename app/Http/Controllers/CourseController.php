<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Traits\ActivityLogger;
use Illuminate\Http\Request;
use App\Models\GradeLevel;
use Illuminate\Http\JsonResponse;

class CourseController extends Controller
{
    use ActivityLogger;
    public function index()
    {   
        $gradeLevels = GradeLevel::orderBy('grade_level')->get();
        $selectedGradeLevel = request()->input('grade_level');

        $coursesQuery = Course::where('is_active', true);

        if ($selectedGradeLevel) {
            $coursesQuery->where('grade_level', $selectedGradeLevel);
        }

        $courses = $coursesQuery->orderBy('name')->paginate(10);

        if ($selectedGradeLevel) {
            $courses->appends(['grade_level' => $selectedGradeLevel]);
        }
        return view('courses.index', compact('courses', 'gradeLevels', 'selectedGradeLevel'));
    }

    public function create()
    {
        $gradeLevels = GradeLevel::orderBy('grade_level')->get();
        return view('courses.create', compact('gradeLevels'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'code' => 'required|unique:courses,code',
                'name' => 'required',
                'description' => 'nullable',
                'grade_level' => 'required|string|exists:grade_levels,grade_level',
            ]);
    
            $course = Course::create($validated);
    
            $this->logActivity(
                'create',
                'Created new course: ' . $course->name,
                'courses',
                null,
                [
                    'code' => $course->code,
                    'name' => $course->name,
                    'grade_level' => $course->grade_level,
                    'description' => $course->description
                ],
                'success'
            );
    
            return redirect()->route('courses.index')->with('success', 'Course created successfully');
        } catch (\Exception $e) {
            $this->logActivity(
                'create',
                'Warning: Failed to create course: ' . $e->getMessage(),
                'courses',
                null,
                $validated ?? null,
                'error'
            );
            throw $e;
        }
    }

    public function edit(Course $course)
    {
        $gradeLevels = GradeLevel::orderBy('grade_level')->get();
        return view('courses.edit', compact('course', 'gradeLevels'));
    }

    public function update(Request $request, Course $course)
    {
        try {
            $oldData = [
                'code' => $course->code,
                'name' => $course->name,
                'grade_level' => $course->grade_level,
                'description' => $course->description
            ];
    
            $validated = $request->validate([
                'code' => 'required|unique:courses,code,' . $course->id,
                'name' => 'required',
                'description' => 'nullable',
                'grade_level' => 'required|string|exists:grade_levels,grade_level',
            ]);
    
            $course->update($validated);
    
            $newData = $course->fresh()->toArray();
            $changes = array_diff_assoc($newData, $oldData);
            
            $this->logActivity(
                'update',
                'Updated course: ' . $course->name . ' (Changed: ' . implode(', ', array_keys($changes)) . ')',
                'courses',
                $oldData,
                $newData,
                'success'
            );
    
            return redirect()->route('courses.index')->with('success', 'Course updated successfully');
        } catch (\Exception $e) {
            $this->logActivity(
                'update',
                'Warning: Failed to update course: ' . $e->getMessage(),
                'courses',
                $oldData ?? null,
                $validated ?? null,
                'error'
            );
            throw $e;
        }
    }

    public function archive(Course $course)
    {
        try {
            $oldData = $course->toArray();
            $course->update(['is_active' => false]);
    
            $this->logActivity(
                'archive',
                'Archived course: ' . $course->name,
                'courses',
                $oldData,
                $course->fresh()->toArray(),
                'success'
            );
    
            return redirect()->route('courses.index')->with('success', 'Course archived successfully');
        } catch (\Exception $e) {
            $this->logActivity(
                'archive',
                'Warning: Failed to archive course: ' . $e->getMessage(),
                'courses',
                $oldData ?? null,
                null,
                'error'
            );
            throw $e;
        }
    }

    public function getCoursesByGradeLevel(Request $request): JsonResponse
    {
        $request->validate([
            'grade_level' => 'required|string|exists:grade_levels,grade_level',
        ]);

        $courses = Course::where('grade_level', $request->grade_level)
            ->orderBy('name')
            ->get(['id', 'name', 'grade_level']);

        return response()->json($courses);
    }
}