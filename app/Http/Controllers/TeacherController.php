<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TeacherController extends Controller
{
    public function index()
    {
        $teachers = Teacher::with('user')->get();
        return view('teachers.index', compact('teachers'));
    }

    public function create()
    {
        $users = User::where('role', 'teacher')
                     ->whereDoesntHave('teacher')
                     ->get();
        return view('teachers.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'specialization' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'contact_number' => 'required|string|max:20'
        ]);

        Teacher::create($validated);

        return redirect()->route('teachers.index')
            ->with('success', 'Teacher profile created successfully');
    }

    public function edit(Teacher $teacher)
    {
        return view('teachers.edit', compact('teacher'));
    }

    public function update(Request $request, Teacher $teacher)
    {
        $validated = $request->validate([
            'specialization' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'contact_number' => 'required|string|max:20'
        ]);

        $teacher->update($validated);

        return redirect()->route('teachers.index')
            ->with('success', 'Teacher profile updated successfully');
    }

    public function destroy(Teacher $teacher)
    {
        $teacher->delete();
        return redirect()->route('teachers.index')
            ->with('success', 'Teacher profile deleted successfully');
    }
}