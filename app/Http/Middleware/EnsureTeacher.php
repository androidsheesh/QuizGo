<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTeacher
{
    /**
     * Handle an incoming request.
     * Only allow users with role = 'teacher' to proceed.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth('teacher')->user() ?? auth('web')->user();

        if (!$user || !$user->isTeacher()) {
            return redirect('/home')->with('error', 'Access denied. Teachers only.');
        }

        return $next($request);
    }
}
