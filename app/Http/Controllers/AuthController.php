<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;

final class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (auth()->check()) {
            return redirect('/polls');
        }

        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        if (Auth::attempt($credentials)) {
            if (!auth()->user()->is_admin) {
                Auth::logout();
                return back()->with('error', 'Only admins can login.');
            }

            return redirect('/polls')->with('success', 'Welcome back, admin!');
        }

        return back()->with('error', 'Invalid credentials.');
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/')->with('success', 'Logged out successfully.');
    }
}
