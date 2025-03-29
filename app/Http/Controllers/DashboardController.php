<?php

namespace App\Http\Controllers;

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
                return view('dashboard.teacher');
            case 'student':
                return view('dashboard.student');
            default:
                Log::warning('Unknown role', ['role' => $user->role]);
                return view('dashboard.user');
        }
    }
}