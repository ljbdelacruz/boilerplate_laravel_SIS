<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;
use Illuminate\Support\Facades\Auth;

class TeacherScheduleController extends Controller
{

    public function preferences()
    {
        $teacher = Auth::user();
        $schedules = Schedule::where('teacher_id', $teacher->id)
                           ->orderBy('day_of_week')
                           ->orderBy('start_time')
                           ->get();

        return view('teacher.schedules.preferences', compact('schedules'));
    }
}