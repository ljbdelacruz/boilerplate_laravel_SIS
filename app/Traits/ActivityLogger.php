<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

trait ActivityLogger
{
    protected function logActivity($action, $description, $module = null, $oldData = null, $newData = null, $status = 'success')
    {
        $request = request();
        
        $data = [
            'user_id' => Auth::id(),
            'action' => $action,
            'description' => $description,
            'module' => $module,
            'old_data' => $oldData ? json_encode($oldData) : null,
            'new_data' => $newData ? json_encode($newData) : null,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'status' => $status
        ];

        return ActivityLog::create($data);
    }
}