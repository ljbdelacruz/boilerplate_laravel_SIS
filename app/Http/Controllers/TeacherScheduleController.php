<?php

namespace App\Http\Controllers;

use App\Traits\ActivityLogger;
use Illuminate\Http\Request;
use App\Models\Schedule;
use Illuminate\Support\Facades\Auth;

class TeacherScheduleController extends Controller
{
    use ActivityLogger;
    public function preferences()
    {
        $teacher = Auth::user();
        $schedules = Schedule::where('teacher_id', $teacher->id)
                           ->orderBy('day_of_week')
                           ->orderBy('start_time')
                           ->get();

        $this->logActivity(
            'view',
            'Teacher viewed schedule preferences',
            'teacher_schedules',
            null,
            [
                'teacher_id' => $teacher->id,
                'teacher_name' => $teacher->name,
                'schedules_count' => $schedules->count()
            ]
        );

        return view('teacher.schedules.preferences', compact('schedules'));
    }

    public function updatePreferences(Request $request)
    {
        try {
            $teacher = Auth::user();
            $oldPreferences = Schedule::where('teacher_id', $teacher->id)->get()->toArray();

            $validated = $request->validate([
                'schedules' => 'required|array',
                'schedules.*.day_of_week' => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday',
                'schedules.*.start_time' => 'required|date_format:H:i',
                'schedules.*.end_time' => 'required|date_format:H:i|after:schedules.*.start_time',
                'schedules.*.notes' => 'nullable|string|max:500'
            ]);

            foreach ($validated['schedules'] as $scheduleData) {
                Schedule::updateOrCreate(
                    [
                        'teacher_id' => $teacher->id,
                        'day_of_week' => $scheduleData['day_of_week']
                    ],
                    [
                        'start_time' => $scheduleData['start_time'],
                        'end_time' => $scheduleData['end_time'],
                        'notes' => $scheduleData['notes'] ?? null
                    ]
                );
            }

            $newPreferences = Schedule::where('teacher_id', $teacher->id)->get()->toArray();

            $this->logActivity(
                'update',
                'Updated schedule preferences',
                'teacher_schedules',
                $oldPreferences,
                array_merge($newPreferences, [
                    'updated_by' => $teacher->name
                ])
            );

            return redirect()->route('teacher.schedules.preferences')
                           ->with('success', 'Schedule preferences updated successfully');

        } catch (\Exception $e) {
            $this->logActivity(
                'update',
                'Warning: Failed to update schedule preferences',
                'teacher_schedules',
                $oldPreferences ?? null,
                [
                    'attempted_data' => $request->all(),
                    'error' => $e->getMessage(),
                    'teacher_id' => Auth::id(),
                    'teacher_name' => Auth::user()->name
                ],
                'error'
            );

            return back()->withErrors(['error' => 'Failed to update schedule preferences'])->withInput();
        }
    }

    public function deletePreference(Schedule $schedule)
    {
        try {
            if ($schedule->teacher_id !== Auth::id()) {
                $this->logActivity(
                    'delete',
                    'Warning: Unauthorized attempt to delete schedule preference',
                    'teacher_schedules',
                    null,
                    [
                        'schedule_id' => $schedule->id,
                        'attempted_by' => Auth::id(),
                        'attempted_by_name' => Auth::user()->name
                    ],
                    'error'
                );
                return response()->json(['error' => 'Unauthorized action'], 403);
            }

            $oldData = $schedule->toArray();
            $schedule->delete();

            $this->logActivity(
                'delete',
                'Deleted schedule preference',
                'teacher_schedules',
                $oldData,
                [
                    'deleted_by' => Auth::user()->name
                ]
            );

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            $this->logActivity(
                'delete',
                'Warning: Failed to delete schedule preference',
                'teacher_schedules',
                $oldData ?? null,
                [
                    'error' => $e->getMessage(),
                    'schedule_id' => $schedule->id
                ],
                'error'
            );

            return response()->json(['error' => 'Failed to delete schedule preference'], 500);
        }
    }
}