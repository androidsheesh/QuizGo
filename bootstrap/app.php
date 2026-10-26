<?php

use App\Exceptions\ActionFailedException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withBroadcasting(
        __DIR__.'/../routes/channels.php',
        ['middleware' => ['web', 'auth']],
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');

        $middleware->redirectGuestsTo(fn ($request) => $request->is('teacher*')
            ? route('teacher.signin')
            : route('signin'));

        $middleware->alias([
            'role'          => \App\Http\Middleware\EnsureRole::class,
            'prevent-back'  => \App\Http\Middleware\PreventBackHistory::class,
            'session.token' => \App\Http\Middleware\ValidateSessionToken::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Business-rule failures from services ("already enrolled", "no such student") go back to the form
        $exceptions->render(function (ActionFailedException $e) {
            return back()
                ->withErrors([$e->field => $e->getMessage()])
                ->withInput();
        });
    })->create();
