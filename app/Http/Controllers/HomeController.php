<?php

namespace App\Http\Controllers;

use App\Models\Deck;
use App\Models\Flashcard;
use App\Services\GeminiServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

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
            'user' => $user,
            'decks' => $decks,
        ]);
    }

    public function generateFromTopic(Request $request, GeminiServices $gemini)
    {
        $request->validate([
            'topic' => 'required|string|max:255',
            'count' => 'nullable|integer|min:1|max:20',
        ]);

        try {
            $count = $request->input('count', 10);
            $flashcards = $gemini->generateFlashcardsFromTopic($request->topic, $count);

            $deck = Deck::create([
                'user_id' => Auth::id(),
                'title' => ucfirst($request->topic),
            ]);

            foreach ($flashcards as $card) {
                Flashcard::create([
                    'deck_id' => $deck->id,
                    'question' => $card['question'] ?? $card['q'] ?? '',
                    'answer' => $card['answer'] ?? $card['a'] ?? '',
                ]);
            }

            return redirect()->route('decks.show', $deck)->with('success', 'Flashcards generated!');
        } catch (\Exception $e) {
            return back()->withErrors(['ai' => $e->getMessage()])->withInput();
        }
    }

    public function generateFromPdf(Request $request, GeminiServices $gemini)
    {
        $request->validate([
            'pdf' => 'required|file|mimes:pdf|max:10000',
            'count' => 'nullable|integer|min:1|max:20',
        ]);

        try {
            $count = $request->input('count', 10);
            $path = $request->file('pdf')->getPathname();
            $flashcards = $gemini->generateFlashcardsFromPdf($path, $count);

            $title = $request->file('pdf')->getClientOriginalName();
            $deck = Deck::create([
                'user_id' => Auth::id(),
                'title' => str_replace('.pdf', '', $title),
            ]);

            foreach ($flashcards as $card) {
                Flashcard::create([
                    'deck_id' => $deck->id,
                    'question' => $card['question'] ?? ($card['q'] ?? ''),
                    'answer' => $card['answer'] ?? ($card['a'] ?? ''),
                ]);
            }

            return redirect()->route('decks.show', $deck)->with('success', 'Flashcards generated successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['ai' => $e->getMessage()])->withInput();
        }
    }
    public function generateFromText(Request $request, GeminiServices $gemini)
    {
        $request->validate([
            'text' => 'required|string|min:20',
            'count' => 'nullable|integer|min:1|max:20',
        ]);

        try {
            $count = $request->input('count', 10);
            $flashcards = $gemini->generateFlashcardsFromText($request->text, $count);

            $title = Str::words($request->text, 5, '...');
            $deck = Deck::create([
                'user_id' => Auth::id(),
                'title' => ucfirst($title),
            ]);

            foreach ($flashcards as $card) {
                Flashcard::create([
                    'deck_id' => $deck->id,
                    'question' => $card['question'] ?? $card['q'] ?? '',
                    'answer' => $card['answer'] ?? $card['a'] ?? '',
                ]);
            }

            return redirect()->route('decks.show', $deck)->with('success', 'Flashcards generated!');
        } catch (\Exception $e) {
            return back()->withErrors(['ai' => $e->getMessage()])->withInput();
        }
    }
}
