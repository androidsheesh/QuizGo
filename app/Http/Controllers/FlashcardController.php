<?php

namespace App\Http\Controllers;

use App\Models\Deck;
use App\Models\Flashcard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FlashcardController extends Controller
{
    /**
     * Store a newly created manual flashcard in storage.
     */
    public function store(Request $request, Deck $deck)
    {
        // Ensure user owns the deck before adding cards
        if ($deck->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'question' => 'required|string',
            'answer'   => 'required|string',
        ]);

        $deck->flashcards()->create([
            'question' => $request->question,
            'answer'   => $request->answer,
        ]);

        return redirect()->route('decks.show', $deck)->with('success', 'Flashcard added successfully.');
    }

    /**
     * Remove the specified flashcard.
     */
    public function destroy(Flashcard $flashcard)
    {
        // Check ownership through the deck
        if ($flashcard->deck->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $flashcard->delete();

        return redirect()->back()->with('success', 'Flashcard deleted.');
    }
}
