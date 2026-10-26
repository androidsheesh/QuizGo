<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Profile changes shared by students and teachers.
 */
class ProfileService
{
    public function update(User $user, array $data, ?UploadedFile $avatar): void
    {
        if ($avatar) {
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            $user->profile_picture = $avatar->store('avatars', 'public');
        }

        $user->firstname = $data['firstname'];
        $user->lastname  = $data['lastname'];
        $user->email     = $data['email'];
        $user->bio       = $data['bio'] ?? null;

        // Only teachers have a department field
        if ($user->isTeacher()) {
            $user->department = $data['department'] ?? null;
        }

        $user->save();
    }

    public function updatePassword(User $user, string $password): void
    {
        // The model's "hashed" cast hashes it on save
        $user->update(['password' => $password]);
    }

    public function deleteAccount(User $user): void
    {
        if ($user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
        }

        $user->delete();
    }
}
