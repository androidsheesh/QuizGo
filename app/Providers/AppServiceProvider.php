<?php

namespace App\Providers;

use App\Models\Classroom;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Keyed by email + IP so a whole classroom behind one school IP isn't locked out together;
        // the per-IP limit still stops one machine from spraying many accounts
        RateLimiter::for('login', function (Request $request) {
            return [
                Limit::perMinute(5)->by(strtolower((string) $request->input('email')).'|'.$request->ip()),
                Limit::perMinute(50)->by($request->ip()),
            ];
        });

        View::composer('components.sidebar', function ($view) {
            $decks = Auth::check()
                ? Auth::user()->decks()->latest()->take(15)->get()
                : collect();

            $view->with('recentDecks', $decks);
        });

        View::composer('components.teacher-sidebar', function ($view) {
            $view->with('sidebarClassrooms', Auth::check()
                ? Classroom::where('teacher_id', Auth::id())->orderBy('name')->get()
                : collect());
        });
    }
}
