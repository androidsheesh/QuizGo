<?php

namespace App\Http\Controllers;

use App\Models\Deck;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudyController extends Controller
{
    /**
     * Validate deck ownership and load cards with optional count limit.
     */
    private function prepareStudy(Deck $deck, Request $request)
    {
        // Ensure user owns the deck
        if ($deck->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $deck->load('flashcards');

        // Determine how many cards to study
        $total = $deck->flashcards->count();
        $count = $request->input('count', $total);
        $count = max(1, min((int) $count, $total));

        // Shuffle and take the requested count
        $cards = $deck->flashcards->shuffle()->take($count)->values();

        return [$deck, $cards];
    }

    /**
     * Flip Cards study mode.
     */
    public function flipcards(Deck $deck, Request $request)
    {
        [$deck, $cards] = $this->prepareStudy($deck, $request);

        return view('study.flipcards', [
            'deck'  => $deck,
            'cards' => $cards,
        ]);
    }

    /**
     * Multiple Choice study mode.
     */
    public function multiplechoice(Deck $deck, Request $request)
    {
        [$deck, $cards] = $this->prepareStudy($deck, $request);

        // Gather all answers in the deck for generating wrong choices
        $allAnswers = $deck->flashcards->pluck('answer')->unique()->values();

        return view('study.multiplechoice', [
            'deck'       => $deck,
            'cards'      => $cards,
            'allAnswers' => $allAnswers,
        ]);
    }

    /**
     * Identification (type-the-answer) study mode.
     */
    public function identification(Deck $deck, Request $request)
    {
        [$deck, $cards] = $this->prepareStudy($deck, $request);

        return view('study.identification', [
            'deck'  => $deck,
            'cards' => $cards,
        ]);
    }
}
