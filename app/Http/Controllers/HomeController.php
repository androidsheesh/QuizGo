<?php

namespace App\Http\Controllers;

use App\Models\Deck;
use App\Models\Flashcard;
use App\Services\GeminiServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use App\Jobs\ProcessPdfFlashcards;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    public function show()
    {
        $user = Auth::user();

        if ($user) {
            $cacheKey = "user_{$user->id}_latest_decks";

            // 1. Get the data from Redis as a JSON string
            $jsonDecks = Cache::remember($cacheKey, 1800, function () use ($user) {
                return $user->decks()
                    ->withCount('flashcards')
                    ->latest()
                    ->take(2)
                    ->get()
                    ->toJson();
            });

            // 2. Turn the JSON back into an array, then "Hydrate" it into Deck Models
            $decksData = json_decode($jsonDecks, true);
            $decks = Deck::hydrate($decksData);

        } else {
            $decks = collect();
        }

        return view('home', compact('user', 'decks'));
    }

    public function generateFromTopic(Request $request, GeminiServices $gemini)
    {
        $request->validate([
            'topic' => 'required|string|max:255',
            'count' => 'nullable|integer|min:1|max:20',
        ]);

        try {
            $topic = strtolower($request->topic);
            $count = $request->input('count', 10);

            // 1. Create a unique cache key based on topic and count
            $cacheKey = "flashcards_" . md5($topic . $count);

            // 2. Try to get data from Redis, or run the AI logic if it's missing
            // We cache it for 24 hours (86400 seconds)
            $flashcards = Cache::remember($cacheKey, 86400, function () use ($gemini, $topic, $count) {
                return $gemini->generateFlashcardsFromTopic($topic, $count);
            });

            $deck = Deck::create([
                'user_id' => Auth::id(),
                'title' => ucfirst($topic),
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
            return back()->withErrors(['ai' => 'Failed to generate flashcards: ' . $e->getMessage()])->withInput();
        }
    }

        public function generateFromPdf(Request $request)
    {
        $request->validate([
            'pdf' => 'required|file|mimes:pdf|max:10000',
            'count' => 'nullable|integer|min:1|max:20',
        ]);

        try {
            $count = $request->input('count', 10);
            $file = $request->file('pdf');
            $originalTitle = $file->getClientOriginalName();

            $fileName = time() . '_' . str_replace(' ', '_', $originalTitle);

            // 1. SAVE the file using the 'local' disk
            $path = $file->storeAs('pdf_uploads', $fileName, 'local');

            // 2. GET the correct absolute path
            $fullPath = \Storage::disk('local')->path($path);

            // 3. VERIFY using the Storage facade
            if (!\Storage::disk('local')->exists($path)) {
                throw new \Exception("File was not saved correctly.");
            }
            // 4. DISPATCH the job synchronously so it waits to finish
            ProcessPdfFlashcards::dispatchSync(
                auth()->id(),
                $fullPath,
                $count,
                $originalTitle
            );

            // Fetch the newly created deck
            $newDeck = auth()->user()->decks()->latest()->first();

            return redirect()->route('decks.show', $newDeck)
                ->with('success', 'Flashcards generated successfully!');

        } catch (\Exception $e) {
            \Log::error("PDF Upload Error: " . $e->getMessage());
            return back()->withErrors(['ai' => 'Error: ' . $e->getMessage()]);
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
