<?php

namespace App\Http\Controllers;

use App\Models\Deck;
use App\Models\Flashcard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class FlashcardController extends Controller
{
    public function store(Request $request, Deck $deck)
    {
        Gate::authorize('manage', $deck);

        $request->validate([
            'question' => 'required|string|max:255',
            'answer'   => 'required|string|max:255',
        ]);

        $deck->flashcards()->create([
            'question' => $request->question,
            'answer'   => $request->answer,
        ]);

        return redirect()->route('decks.show', $deck)->with('success', 'Flashcard added successfully.');
    }

    public function destroy(Flashcard $flashcard)
    {
        Gate::authorize('manage', $flashcard);

        $flashcard->delete();

        return redirect()->back()->with('success', 'Flashcard deleted.');
    }

    public function update(Flashcard $flashcard, Request $request)
    {
        Gate::authorize('manage', $flashcard);

        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string|max:255',
        ]);

        $flashcard->update([
            'question' => $request->question,
            'answer' => $request->answer,
        ]);

        return redirect()->back()->with('success', 'Flashcard updated successfully.');
    }
}
