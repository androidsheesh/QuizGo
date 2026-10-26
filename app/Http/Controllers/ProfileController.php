<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeleteAccountRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Services\ProfileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Profile pages for both students and teachers; each role gets its own view.
 */
class ProfileController extends Controller
{
    public function __construct(private ProfileService $profileService)
    {
    }

    public function show(Request $request)
    {
        $user = $request->user();

        return view($user->isTeacher() ? 'teacher.teacher-profile' : 'profile.show', [
            'user' => $user,
        ]);
    }

    public function update(UpdateProfileRequest $request)
    {
        $this->profileService->update($request->user(), $request->validated(), $request->file('profile_picture'));

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        $this->profileService->updatePassword($request->user(), $request->validated('password'));

        return back()->with('success', 'Password updated successfully.');
    }

    public function destroy(DeleteAccountRequest $request)
    {
        $user = $request->user();

        Auth::logout();
        $this->profileService->deleteAccount($user);

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
