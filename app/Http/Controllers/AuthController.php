<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ActivityLog; // Add this at the top with other imports
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;  // Add this import
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            Log::info('Already authenticated user attempting to access login page', [
                'user_id' => Auth::id(),
                'role' => Auth::user()->role
            ]);
            return redirect('/dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required'
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                Log::warning('Failed login attempt', ['email' => $request->email]);
                throw ValidationException::withMessages([
                    'email' => ['The provided credentials are incorrect.'],
                ]);
            }

            // Clear existing tokens
            $user->tokens()->delete();

            // Create new token with role-based abilities
            $token = $user->createToken('auth-token', [$user->role])->plainTextToken;

            Auth::login($user);
            $request->session()->regenerate();

            // Log the successful login
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'login',
                'description' => 'User logged in successfully',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
            
            return redirect()->intended('/dashboard');

        } catch (\Exception $e) {
            Log::error('Login error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->withErrors([
                'email' => 'An error occurred during login. Please try again.'
            ])->withInput($request->except('password'));
        }
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            // Log the logout
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'logout',
                'description' => 'User logged out',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
        }

        if ($request->wantsJson()) {
            $request->user()->currentAccessToken()->delete();
            return response()->json(['message' => 'Logged out successfully']);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    public function user(Request $request)
    {
        return response()->json([
            'user' => $request->user(),
            'role' => $request->user()->role,
            'abilities' => $request->user()->currentAccessToken()->abilities ?? []
        ]);
    }
}