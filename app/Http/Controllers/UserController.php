<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Teacher;
use App\Models\Section;
use App\Models\SchoolYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\StudentsImport;
use App\Imports\TeachersImport;

class UserController extends Controller
{
    public function index()
    {
        $users = User::paginate(10);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $sections = Section::where('is_active', true)->get();
        $schoolYears = SchoolYear::where('is_active', true)->get();
        return view('users.create', compact('sections', 'schoolYears'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,teacher,student',
            // Add teacher-specific validation
            'specialization' => 'required_if:role,teacher|string|max:255',
            'bio' => 'nullable|string',
            'contact_number' => 'required_if:role,teacher|string|max:20'
        ]);

        DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role']
            ]);

            if ($validated['role'] === 'teacher') {
                Teacher::create([
                    'user_id' => $user->id,
                    'specialization' => $validated['specialization'],
                    'bio' => $validated['bio'] ?? null,
                    'contact_number' => $validated['contact_number']
                ]);
            }
        });

        return redirect()->route('users.index')->with('success', 'User created successfully');
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'role' => 'required|in:admin,teacher,student'
        ]);

        $user->update($validated);
        return redirect()->route('users.index')->with('success', 'User updated successfully');
    }

    public function uploadStudentsForm()
    {
        return view('users.upload-students');
    }

    public function uploadTeachersForm()
    {
        return view('users.upload-teachers');
    }

    public function uploadStudents(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        try {
            Excel::import(new StudentsImport, $request->file('file'));
            return redirect()->route('users.index')->with('success', 'Students imported successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Error importing students: ' . $e->getMessage());
        }
    }

    public function uploadTeachers(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        try {
            Excel::import(new TeachersImport, $request->file('file'));
            return redirect()->route('users.index')->with('success', 'Teachers imported successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Error importing teachers: ' . $e->getMessage());
        }
    }
}