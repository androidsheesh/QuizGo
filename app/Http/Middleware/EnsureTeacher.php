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
        if (!$request->user() || $request->user()->role !== 'teacher') {
            return redirect('/home')->with('error', 'Access denied. Teachers only.');
        }

        return $next($request);
    }
}
