<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherSigninController extends Controller
{
    public function create()
    {
        return view('teacher-signin');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            if (Auth::user()->role === 'teacher') {
                return redirect()->route('teacher.dashboard');
            } elseif (Auth::user()->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }

            // If a student tries to log in here, reject them
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'email' => 'You are not eligible to enter.',
            ]);
        }

        return back()->withErrors([
            'email' => 'Invalid email or password',
        ])->withInput();
    }
}
