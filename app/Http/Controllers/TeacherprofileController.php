<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TeacherprofileController extends Controller
{
    /**
     * Show the teacher profile page.
     */
    public function show()
    {
        return view('teacher.teacher-profile', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * Update the teacher's profile information.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'firstname'       => 'required|string|max:255',
            'lastname'        => 'required|string|max:255',
            'email'           => 'required|email|max:255|unique:users,email,' . Auth::id(),
            'department'      => 'nullable|string|max:255',
            'bio'             => 'nullable|string|max:1000',
            'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Handle avatar upload
        if ($request->hasFile('profile_picture')) {
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            $path = $request->file('profile_picture')->store('avatars', 'public');
            $user->profile_picture = $path;
        }

        $user->firstname  = $validated['firstname'];
        $user->lastname   = $validated['lastname'];
        $user->email      = $validated['email'];
        $user->department = $validated['department'] ?? null;
        $user->bio        = $validated['bio'] ?? null;
        $user->save();

        return redirect()->route('teacher.profile')->with('success', 'Profile updated successfully.');
    }

    /**
     * Delete the teacher's account after confirmation.
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'confirmation' => 'required|string',
        ]);

        if ($request->confirmation !== 'CONFIRM') {
            return back()->withErrors(['confirmation' => 'You must type CONFIRM to delete your account.']);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Delete their profile picture from storage if they have one
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
