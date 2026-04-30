<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function show()
    {
        $user = Auth::user();

        // Grab the 2 most recent decks for the homepage preview
        $decks = $user
            ? $user->decks()->withCount('flashcards')->latest()->take(2)->get()
            : collect();

        return view('home', [
            'user'  => $user,
            'decks' => $decks,
        ]);
    }
}
