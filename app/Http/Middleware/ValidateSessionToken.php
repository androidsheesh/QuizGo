<?php

namespace App\Http\Middleware;

use App\Enums\Role;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ValidateSessionToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user) {
            $sessionToken = session('session_token');

            if (!$sessionToken || $sessionToken !== $user->session_token) {
                $redirectRoute = $user->hasRole(Role::Teacher, Role::Admin)
                    ? 'teacher.signin'
                    : 'signin';

                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route($redirectRoute)
                    ->withErrors([
                        'email' => 'Your session was ended because your account signed in on another device.',
                    ]);
            }
        }

        return $next($request);
    }
}
