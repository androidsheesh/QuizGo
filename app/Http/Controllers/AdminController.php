<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Must be admin to view
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized access.');
        }

        $teachers = User::where('role', 'teacher')->orderBy('created_at', 'desc')->get();

        return view('admin.dashboard', compact('teachers'));
    }

    public function storeTeacher(Request $request)
    {
        // Must be admin to create
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        User::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'initial_email' => $request->email,
            'initial_password' => $request->password,
            'role' => 'teacher',
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Teacher account created successfully.');
    }

    public function deleteTeacher(User $teacher)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized access.');
        }

        if ($teacher->role !== 'teacher') {
            abort(400, 'Can only delete teachers.');
        }

        $teacher->delete();

        return redirect()->route('admin.dashboard')->with('success', 'Teacher account deleted successfully.');
    }
}
