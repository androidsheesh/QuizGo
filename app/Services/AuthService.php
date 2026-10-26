<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthService
{
    private const PENDING_LOGIN_TTL_SECONDS = 300;

    /**
     * Attempt to authenticate the user.
     * Returns the redirect URL if successful, or null if it fails.
     */
    public function attemptLogin(array $credentials, Request $request, ?array $allowedRoles = null): ?string
    {
        if ($request->boolean('confirm_other_device')) {
            // Single use: the pending login is consumed whether or not the confirmation succeeds
            $pendingUserId = $request->session()->pull('pending_login_user_id');
            $pendingAt = (int) $request->session()->pull('pending_login_at', 0);

            if (! $pendingUserId || now()->timestamp - $pendingAt > self::PENDING_LOGIN_TTL_SECONDS) {
                $request->session()->forget('pending_login_remember');

                return null;
            }

            $user = User::find($pendingUserId);

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
            $request->session()->put('pending_login_at', now()->timestamp);
            $request->session()->put('pending_login_remember', $request->boolean('remember'));

            return 'confirm-other-device';
        }

        return $this->loginUser($user, $request);
    }

    /**
     * Drop an unconfirmed "sign in on this device?" login, e.g. when the sign-in page is reloaded.
     */
    public function clearPendingLogin(Request $request): void
    {
        $request->session()->forget(['pending_login_user_id', 'pending_login_at', 'pending_login_remember']);
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
