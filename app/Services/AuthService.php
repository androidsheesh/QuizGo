<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    /**
     * Attempt to authenticate the user.
     * Returns the redirect URL if successful, or null if it fails.
     */
    public function attemptLogin(array $credentials): ?string
    {
        if (Auth::attempt($credentials)) {
            /** @var User $user */
            $user = Auth::user();

            return $this->determineRedirectRoute($user);
        }

        return null;
    }

    /**
     * Determine where the user should go based on their role.
     */
    protected function determineRedirectRoute(User $user): string
    {
        return match ($user->role) {
            'teacher' => route('teacher.dashboard'),
            'admin'   => route('admin.dashboard'),
            default   => url('home'), // Default fallback for standard users
        };
    }
}
