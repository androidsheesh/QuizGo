<?php

namespace App\Http\Controllers;

use App\Models\Deck;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeckController extends Controller
{

    public function index(Request $request)
    {
        $query = Auth::user()
            ->decks()
            ->withCount('flashcards')
            ->latest();

        if ($request->has('search') && $request->search != '') {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $decks = $query->paginate(9)->appends(['search' => $request->search]);

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

    public function show(Request $request, Deck $deck)
    {
        // Ensure user owns the deck
        if ($deck->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $totalCards = $deck->flashcards()->count();
        $flashcards = $deck->flashcards()->latest()->paginate(12);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'html' => view('decks.partials.flashcards', compact('flashcards', 'deck'))->render(),
                'next_page' => $flashcards->nextPageUrl()
            ]);
        }

        return view('decks.show', compact('deck', 'flashcards', 'totalCards'));
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

        $deck->delete();

        return redirect()->route('mydecks')->with('success', 'Deck deleted successfully.');
    }
}
