<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class StudentPaymentController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::check()) {
            Log::error('User not authenticated');
            return redirect()->route('login');
        }

        $user = $request->user();
        
        if ($user->role !== 'student') {
            Log::warning('Unauthorized access attempt to student payments', [
                'user_id' => $user->id,
                'role' => $user->role
            ]);
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }

        Log::info('Student accessing payment details', [
            'user_id' => $user->id
        ]);

        $enrolledCourses = $user->courses()->get();
        $totalAmount = $enrolledCourses->isEmpty() ? 0 : $enrolledCourses->sum('price');
        $totalPaid = $enrolledCourses->isEmpty() ? 0 : $enrolledCourses->sum('pivot.amount_paid');
        $balance = $totalAmount - $totalPaid;

        return view('student.payments.index', compact('enrolledCourses', 'totalAmount', 'totalPaid', 'balance'));
    }
}