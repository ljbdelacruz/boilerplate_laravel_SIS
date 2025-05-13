<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $selectedRole = $request->input('role');
        $allRoles = User::select('role')->distinct()->whereIn('role', ['admin', 'teacher'])->orderBy('role')->pluck('role');

        $usersQuery = User::with(['activityLogs' => function($query) {
            $query->latest()->take(3);
        }]);

        if ($selectedRole) {
            $usersQuery->where('role', $selectedRole);
        } else {
            $usersQuery->whereIn('role', ['admin', 'teacher']);
        }
        
        $users = $usersQuery->orderBy('name')->paginate(10);

        $users->appends(['role' => $selectedRole]);

        return view('activity-logs.index', compact('users', 'allRoles', 'selectedRole'));
    }

    public function userLogs($userId)
    {
        $user = User::findOrFail($userId);
        $logs = ActivityLog::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('activity-logs.user', compact('user', 'logs'));
    }
}