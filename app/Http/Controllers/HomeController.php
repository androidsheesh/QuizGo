<?php

namespace App\Http\Controllers;

use App\Models\Deck;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Jobs\ProcessPdfFlashcards;
use App\Jobs\ProcessTopicFlashcards;
use App\Jobs\ProcessTextFlashcards;

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

    public function generateFromTopic(Request $request)
    {
        $request->validate([
            'topic' => 'required|string|max:255',
            'count' => 'nullable|integer|min:1|max:20',
        ]);

        try {
            $latestDeckId = Auth::user()->decks()->latest()->first()?->id ?? 0;
            $topic = strtolower($request->topic);
            $count = $request->input('count', 10);

            ProcessTopicFlashcards::dispatch(
                auth()->id(),
                $topic,
                $count
            );

            return redirect()->route('mydecks')
                ->with('success', 'Your topic is being processed in the background. You can move to another page while it finishes.')
                ->with('waiting_for_deck', $latestDeckId);

        } catch (\Exception $e) {
            \Log::error("Topic Generation Error: " . $e->getMessage());
            return back()->withErrors(['ai' => 'Failed to generate flashcards: ' . $e->getMessage()])->withInput();
        }
    }

    public function generateFromPdf(Request $request)
    {
        $request->validate([
            'pdf' => 'required|file|mimes:pdf|max:10240',
            'count' => 'nullable|integer|min:1|max:20',
        ]);

        try {
            // Get the current latest deck ID before the new one is created
            $latestDeckId = Auth::user()->decks()->latest()->first()?->id ?? 0;

            $count = $request->input('count', 10);
            $file = $request->file('pdf');
            $originalTitle = $file->getClientOriginalName();
            $fileName = time() . '_' . str_replace(' ', '_', $originalTitle);

            $path = $file->storeAs('pdf_uploads', $fileName, 'local');
            $fullPath = \Storage::disk('local')->path($path);

            ProcessPdfFlashcards::dispatch(
                auth()->id(),
                $fullPath,
                $count,
                $originalTitle
            );

            return redirect()->route('mydecks')
                ->with('success', 'Your PDF is being processed in the background. You can move to another page while it finishes.')
                ->with('waiting_for_deck', $latestDeckId);

        } catch (\Exception $e) {
            \Log::error("PDF Upload Error: " . $e->getMessage());
            return back()->withErrors(['ai' => 'Error: ' . $e->getMessage()]);
        }
    }
    public function generateFromText(Request $request)
    {
        $request->validate([
            'text' => 'required|string|min:20',
            'count' => 'nullable|integer|min:1|max:20',
        ]);

        try {
            $latestDeckId = Auth::user()->decks()->latest()->first()?->id ?? 0;
            $count = $request->input('count', 10);

            ProcessTextFlashcards::dispatch(
                auth()->id(),
                $request->text,
                $count
            );

            return redirect()->route('mydecks')
                ->with('success', 'Your text is being processed in the background. You can move to another page while it finishes.')
                ->with('waiting_for_deck', $latestDeckId);
        } catch (\Exception $e) {
            \Log::error("Text Generation Error: " . $e->getMessage());
            return back()->withErrors(['ai' => $e->getMessage()])->withInput();
        }
    }
}
