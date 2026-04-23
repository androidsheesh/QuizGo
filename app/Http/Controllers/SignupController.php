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

        // 2. Here is where you would typically save the data to your database using Eloquent
        // User::create([
        //     'firstname' => $validatedData['firstname'],
        //     'lastname' => $validatedData['lastname'],
        //     'email' => $validatedData['email'],
        //     'password' => bcrypt($validatedData['password']), // Always hash passwords!
        // ]);

        // 3. Return a success response or redirect
        return "Form submitted successfully!";
    }
}
