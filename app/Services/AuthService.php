<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Events\UserLoggedInElsewhere;

class AuthService
{
    /**
     * Attempt to authenticate the user.
     * Returns the redirect URL if successful, or null if it fails.
     */
    public function attemptLogin(array $credentials, Request $request, ?array $allowedRoles = null): ?string
    {
        if ($request->boolean('confirm_other_device')) {
            $user = User::find($request->session()->get('pending_login_user_id'));

            if (! $user || ($credentials['email'] ?? null) !== $user->email) {
                return null;
            }

            if ($allowedRoles !== null && ! in_array($user->role, $allowedRoles, true)) {
                return null;
            }

            return $this->loginUser($user, $request);
        }

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return null;
        }

        if ($allowedRoles !== null && ! in_array($user->role, $allowedRoles, true)) {
            return null;
        }

        if ($this->hasActiveSession($user) && ! $request->boolean('confirm_other_device')) {
            $request->session()->put('pending_login_user_id', $user->id);
            $request->session()->put('pending_login_remember', $request->boolean('remember'));

            return 'confirm-other-device';
        }

        return $this->loginUser($user, $request);

            if (Auth::attempt($credentials)) {
        /** @var User $user */
        $user = Auth::user();

        // Notify any existing session on another device
        UserLoggedInElsewhere::dispatch($user->id);

        // Generate and store new session token
        $token = Str::random(60);
        $user->session_token = $token;
        $user->save();

        session(['session_token' => $token]);

        return $this->determineRedirectRoute($user);
    }

    return null;
    }

    public function loginUser(User $user, Request $request): string
    {
        Auth::login($user, $request->boolean('remember') || (bool) $request->session()->pull('pending_login_remember', false));
        $request->session()->forget('pending_login_user_id');
        $request->session()->regenerate();
        $this->issueSessionToken($user, $request);

        return $this->determineRedirectRoute($user);
    }

    public function issueSessionToken(User $user, Request $request): void
    {
        $token = Str::random(60);

        $user->forceFill([
            'session_token' => $token,
        ])->save();

        $request->session()->put('session_token', $token);
    }

    public function hasActiveSession(User $user): bool
    {
        return filled($user->session_token);
    }

    /**
     * Determine where the user should go based on their role.
     */
    protected function determineRedirectRoute(User $user): string
    {
        return match ($user->role) {
            'teacher' => route('teacher.dashboard'),
            'admin'   => route('admin.dashboard'),
            default   => url('home'),
        };
    }
}
