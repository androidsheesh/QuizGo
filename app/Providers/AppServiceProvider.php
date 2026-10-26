<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\URL; // Import the URL facade
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
        // Force HTTPS URL generation in production/tunnel environments
        // if (config('app.env') === 'production' || env('APP_URL') !== 'http://localhost') {
        //     URL::forceScheme('https');
        // }

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
    }
}
