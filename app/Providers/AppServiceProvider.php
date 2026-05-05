<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Deck;
use Illuminate\Support\Facades\Auth;
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
        View::composer('components.sidebar', function ($view) {
            // Increase the limit or remove it entirely if you want ALL decks
            $decks = Auth::check()
                ? Auth::user()->decks()->latest()->take(15)->get()
                : collect();

            $view->with('recentDecks', $decks);
        });
    }
}
