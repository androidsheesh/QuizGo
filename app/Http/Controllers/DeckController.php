<?php

namespace App\Http\Controllers;

use App\Models\Deck;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeckController extends Controller
{

    public function index()
    {
        $decks = Auth::user()
            ->decks()
            ->withCount('flashcards')
            ->latest()
            ->paginate(9);

        return view('mydecks', compact('decks'));
    }

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

    public function show(Deck $deck)
    {
        // Ensure user owns the deck
        if ($deck->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $deck->load('flashcards');

        return view('decks.show', compact('deck'));
    }

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

    public function destroy(Deck $deck)
    {
        if ($deck->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $deck->delete($deck);

        return redirect()->route('mydecks')->with('success', 'Deck deleted successfully.');
    }
}
