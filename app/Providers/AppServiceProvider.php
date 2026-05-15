<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Observers\QuizObserver;
use App\Observers\ClassroomObserver;
use App\Models\Quiz;
use App\Models\Classroom;
use Illuminate\Support\Facades\Auth;
use App\Models\QuizAssignment;
use App\Observers\QuizAssignmentObserver;
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
        Quiz::observe(QuizObserver::class);
        Classroom::observe(ClassroomObserver::class);
        QuizAssignment::observe(QuizAssignmentObserver::class);

        View::composer('components.sidebar', function ($view) {
            // Increase the limit or remove it entirely if you want ALL decks
            $decks = Auth::check()
                ? Auth::user()->decks()->latest()->take(15)->get()
                : collect();

            $view->with('recentDecks', $decks);
        });
    }
}
