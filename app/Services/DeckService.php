<?php
namespace App\Services;

use App\Models\Deck;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class DeckService
{
    /**
     * Fetch the user's latest decks using Redis caching.
     */
    public function getLatestDecksForUser(User $user): Collection
    {
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

        return Deck::hydrate($decksData);
    }
}
