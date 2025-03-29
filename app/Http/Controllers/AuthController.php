<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Clear existing tokens
        $user->tokens()->delete();

        // Create new token with role-based abilities
        $token = $user->createToken('auth-token', [$user->role])->plainTextToken;

        if ($request->wantsJson()) {
            return response()->json([
                'user' => $user,
                'token' => $token,
                'role' => $user->role
            ]);
        }

        Auth::login($user);
        $request->session()->regenerate();
        
        return redirect()->intended('/dashboard');
    }

    public function logout(Request $request)
    {
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