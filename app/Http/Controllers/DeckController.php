<?php

namespace App\Http\Controllers;

use App\Models\Deck;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeckController extends Controller
{
    /**
     * Display a listing of the user's decks.
     */
    public function index()
    {
        // Load decks for the logged-in user with their flashcard counts
        $decks = Auth::user()->decks()->withCount('flashcards')->latest()->get();
        return view('mydecks', compact('decks'));
    }

    /**
     * Store a newly created deck in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        Auth::user()->decks()->create([
            'title' => $request->title,
        ]);

        return redirect()->route('mydecks')->with('success', 'Deck created successfully.');
    }

    /**
     * Display the specified deck (and its flashcards).
     */
    public function show(Deck $deck)
    {
        // Ensure user owns the deck
        if ($deck->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $deck->load('flashcards');

        return view('decks.show', compact('deck'));
    }

    /**
     * Update the specified deck in storage.
     */
    public function update(Request $request, Deck $deck)
    {
        if ($deck->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $deck->update([
            'title' => $request->title,
        ]);

        return redirect()->back()->with('success', 'Deck updated successfully.');
    }

    /**
     * Remove the specified deck from storage.
     */
    public function destroy(Deck $deck)
    {
        if ($deck->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $deck->delete();

        return redirect()->route('mydecks')->with('success', 'Deck deleted successfully.');
    }
}
