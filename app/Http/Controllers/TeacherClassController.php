<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TeacherClassController extends Controller
{

    public function index()
    {
        $teacher = User::find(Auth::id());
        
        $classes = Schedule::where('teacher_id', $teacher->id)
                         ->with(['course', 'schoolYear'])
                         ->orderBy('day_of_week')
                         ->orderBy('start_time')
                         ->get();
                         
        return view('teacher.classes.index', [
            'classes' => $classes,
            'teacher' => $teacher
        ]);
    }

    public function show(Schedule $class)
    {
        if ($class->teacher_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('teacher.classes.show', [
            'class' => $class->load(['course', 'schoolYear'])
        ]);
    }
}