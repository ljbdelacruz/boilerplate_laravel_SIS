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
                // Redirect teachers to the dedicated TeacherController dashboard
                return redirect()->route('teacher.dashboard', $request->query());
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