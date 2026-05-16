<?php

namespace App\Http\Controllers;

use App\Models\FlashcardGeneration;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class FlashcardGenerationStatusController extends Controller
{
    public function __invoke(string $uuid): JsonResponse
    {
        $generation = FlashcardGeneration::query()
            ->where('uuid', $uuid)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($generation->status === FlashcardGeneration::STATUS_PENDING && !$generation->deck_id) {
            $createdDeck = $generation->user
                ->decks()
                ->where('created_at', '>=', $generation->created_at)
                ->oldest()
                ->first();

            if ($createdDeck) {
                $generation->markCompleted($createdDeck);
                $generation->refresh();
            }
        }

        return response()->json([
            'id' => $generation->uuid,
            'status' => $generation->status,
            'deck_id' => $generation->deck_id,
            'deck_url' => $generation->deck_id
                ? route('decks.show', $generation->deck_id)
                : null,
            'error' => $generation->status === FlashcardGeneration::STATUS_FAILED
                ? [
                    'code' => $generation->error_code,
                    'message' => 'Generation failed. Please try a different prompt or try again later.',
                ]
                : null,
        ])->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
    }
}
