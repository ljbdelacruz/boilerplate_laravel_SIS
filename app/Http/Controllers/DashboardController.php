<?php

namespace App\Http\Controllers;

use App\Models\SchoolYear;
use App\Models\Section;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;


class DashboardController extends Controller
{
    public function index(Request $request)
    {
        
        if (!Auth::check()) {
            Log::error('User not authenticated');
            return redirect()->route('/');
        }

        $user = $request->user();
        Log::info('Dashboard access', [
            'user_id' => $user->id,
            'role' => $user->role
        ]);
        
        switch ($user->role) {
            case 'admin':
                return view('dashboard.admin');
            case 'teacher':
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
                if ($activeSchoolYear) {
                    $students = \App\Models\Student::whereHas('section', function ($query) use ($activeSchoolYear, $schedules) {
                        $query->where('school_year_id', $activeSchoolYear->id)
                            ->whereIn('id', $schedules->pluck('section_id')); // Filter sections based on teacher's schedule
                    })->with(['section'])->get();
                }

                return view('dashboard.teacher', 
                compact('schoolYears', 'sections', 'schedules', 'students'));
            case 'student':
                return view('dashboard.student');
            default:
                Log::warning('Unknown role', ['role' => $user->role]);
                return view('dashboard.user');
        }
    }
     public function showIndexPage()
{
    return view('dashboard.index');
}

}