<?php

namespace App\Http\Controllers;

use App\Traits\ActivityLogger;
use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TeacherClassController extends Controller
{
    use ActivityLogger;
    public function index()
    {
        $teacher = User::find(Auth::id());
        
        $classes = Schedule::where('teacher_id', $teacher->id)
                         ->with(['course', 'schoolYear'])
                         ->orderBy('day_of_week')
                         ->orderBy('start_time')
                         ->get();

        $this->logActivity(
            'view',
            'Viewed class list',
            'teacher_classes',
            null,
            ['teacher_id' => $teacher->id]
        );
                         
        return view('teacher.classes.index', [
            'classes' => $classes,
            'teacher' => $teacher
        ]);
    }

    public function show(Schedule $class)
    {
        if ($class->teacher_id !== Auth::id()) {
            $this->logActivity(
                'access',
                'Warning: Attempted unauthorized access to class',
                'teacher_classes',
                null,
                ['class_id' => $class->id],
                'error'
            );
            abort(403, 'Unauthorized action.');
        }

        $this->logActivity(
            'view',
            'Viewed class details',
            'teacher_classes',
            null,
            $class->load(['course', 'schoolYear'])->toArray()
        );

        return view('teacher.classes.show', [
            'class' => $class->load(['course', 'schoolYear'])
        ]);
    }

    public function update(Request $request, Schedule $class)
    {
        if ($class->teacher_id !== Auth::id()) {
            $this->logActivity(
                'modify',
                'Warning: Unauthorized attempt to modify class',
                'teacher_classes',
                null,
                [
                    'class_id' => $class->id,
                    'attempted_by' => Auth::id(),
                    'attempted_data' => $request->all()
                ],
                'error'
            );
            abort(403, 'Unauthorized action.');
        }

        $oldData = $class->toArray();

        try {
            $validated = $request->validate([
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i|after:start_time',
                'day_of_week' => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday',
                'notes' => 'nullable|string|max:500'
            ]);

            $class->update($validated);

            $this->logActivity(
                'update',
                'Updated class schedule details',
                'teacher_classes',
                $oldData,
                array_merge($class->fresh()->toArray(), [
                    'updated_by' => Auth::user()->name,
                    'timestamp' => now()->format('Y-m-d H:i:s')
                ])
            );

            return redirect()->route('teacher.classes.show', $class)
                           ->with('success', 'Class schedule updated successfully');

        } catch (\Exception $e) {
            $this->logActivity(
                'update',
                'Warning: Failed to update class schedule',
                'teacher_classes',
                $oldData,
                [
                    'error' => $e->getMessage(),
                    'attempted_data' => $validated ?? $request->all()
                ],
                'error'
            );

            return back()->withErrors(['error' => 'Failed to update class schedule'])
                        ->withInput();
        }
    }
}