<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Schedule;

class TeacherManagementController extends Controller
{
    use ActivityLogger;
    public function index()
    {
        $teachers = User::where('role', 'teacher')
                       ->orderBy('name')
                       ->get();

        $this->logActivity(
            'view',
            'Viewed teacher list',
            'teacher_management',
            null,
            [
                'accessed_by' => Auth::user()->name,
                'teachers_count' => $teachers->count()
            ]
        );
        
        return view('admin.teachers.index', compact('teachers'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:8',
            ]);

            $teacher = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => bcrypt($validated['password']),
                'role' => 'teacher'
            ]);

            $this->logActivity(
                'create',
                'Created new teacher account',
                'teacher_management',
                null,
                array_merge($teacher->toArray(), [
                    'created_by' => Auth::user()->name,
                    'timestamp' => now()->format('Y-m-d H:i:s')
                ])
            );

            return redirect()->route('teachers.index')->with('success', 'Teacher created successfully');
        } catch (\Exception $e) {
            $this->logActivity(
                'create',
                'Warning: Failed to create teacher account',
                'teacher_management',
                null,
                [
                    'attempted_data' => $request->except('password'),
                    'error' => $e->getMessage()
                ],
                'error'
            );

            return back()->withErrors(['error' => 'Failed to create teacher'])->withInput();
        }
    }

    public function update(Request $request, User $teacher)
    {
        $oldData = $teacher->toArray();
        
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $teacher->id,
            ]);
            
            $teacher->update($validated);

            $this->logActivity(
                'update',
                "Updated teacher information for {$teacher->name}",
                'teacher_management',
                $oldData,
                array_merge($teacher->fresh()->toArray(), [
                    'updated_by' => Auth::user()->name,
                    'timestamp' => now()->format('Y-m-d H:i:s')
                ])
            );

            return redirect()->route('teachers.index')->with('success', 'Teacher updated successfully');
        } catch (\Exception $e) {
            $this->logActivity(
                'update',
                "Warning: Failed to update teacher {$teacher->name}",
                'teacher_management',
                $oldData,
                [
                    'attempted_data' => $request->all(),
                    'error' => $e->getMessage()
                ],
                'error'
            );

            return back()->withErrors(['error' => 'Failed to update teacher'])->withInput();
        }
    }

    public function destroy(User $teacher)
    {
        $teacherData = $teacher->toArray();
        
        try {
            $teacher->delete();

            $this->logActivity(
                'delete',
                "Deleted teacher {$teacher->name}",
                'teacher_management',
                $teacherData,
                [
                    'deleted_by' => Auth::user()->name,
                    'timestamp' => now()->format('Y-m-d H:i:s')
                ]
            );

            return redirect()->route('teachers.index')->with('success', 'Teacher deleted successfully');
        } catch (\Exception $e) {
            $this->logActivity(
                'delete',
                "Warning: Failed to delete teacher {$teacher->name}",
                'teacher_management',
                $teacherData,
                [
                    'error' => $e->getMessage()
                ],
                'error'
            );

            return back()->withErrors(['error' => 'Failed to delete teacher']);
        }
    }
}