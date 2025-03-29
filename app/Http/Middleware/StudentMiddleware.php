<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || Auth::user()->role !== 'student') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }
        return $next($request);
    }
}