<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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

        // 2. Create the user
        $user = User::create([
            'firstname' => $validatedData['firstname'],
            'lastname'  => $validatedData['lastname'],
            'email'     => $validatedData['email'],
            'password'  => Hash::make($validatedData['password']),
        ]);

        // 3. Log the user in
        Auth::login($user);

        // after signing up, it will redirect directly to the dashboard page
        return redirect('home');

    }
}
