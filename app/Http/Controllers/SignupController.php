<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SignupController extends Controller
{
    // Displays the form
    public function create()
    {
        return view('signup');
    }

    // Handles the form submission
    public function store(Request $request)
    {
        // 1. Validate the incoming data
        $validatedData = $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname'  => 'required|string|max:255',
            'email'     => 'required|email|max:255|unique:users', // Ensures email isn't already taken
            'password'  => 'required|string|min:8', // Requires at least 8 characters
        ]);

        // after signing up, it will redirect directly to the dashboard page
        return redirect('home');

    }
}
