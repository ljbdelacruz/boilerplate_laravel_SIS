<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index()
    {
        $users = User::with(['activityLogs' => function($query) {
            $query->latest()->take(3);
        }])->paginate(10);

        return view('activity-logs.index', compact('users'));
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