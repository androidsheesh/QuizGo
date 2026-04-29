<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MyProfileController extends Controller
{
    public function show()
    {
        return view('MyProfile', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname'  => 'required|string|max:255',
            'email'     => 'required|email|max:255|unique:users,email,' . Auth::id(),
            'bio'       => 'nullable|string|max:1000',
            'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $user = Auth::user();

        // Handle avatar upload
        if ($request->hasFile('profile_picture')) {
            // Delete old avatar if it exists
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            $path = $request->file('profile_picture')->store('avatars', 'public');
            $user->profile_picture = $path;
        }

        $user->firstname = $validated['firstname'];
        $user->lastname  = $validated['lastname'];
        $user->email     = $validated['email'];
        $user->bio       = $validated['bio'] ?? null;
        $user->save();

        return redirect()->route('myprofile')->with('success', 'Profile updated successfully.');
    }

    /**
     * Delete the user's account after confirmation.
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'confirmation' => 'required|string',
        ]);

        if ($request->confirmation !== 'CONFIRM') {
            return back()->withErrors(['confirmation' => 'You must type CONFIRM to delete your account.']);
        }

        $user = Auth::user();

        // Delete the avatar file if it exists
        if ($user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
        }

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
