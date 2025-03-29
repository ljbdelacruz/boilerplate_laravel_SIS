<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        switch ($user->role) {
            case 'admin':
                return view('dashboard.admin');
            case 'teacher':
                return view('dashboard.teacher');
            default:
                return view('dashboard.user');
        }
    }
}