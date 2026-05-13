<?php

namespace App\Jobs;

use App\Models\Deck;
use App\Models\Flashcard;
use App\Services\GeminiServices;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ProcessTopicFlashcards implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 120;

    protected $userId;
    protected $topic;
    protected $count;

    public function __construct($userId, $topic, $count)
    {
        $this->userId = $userId;
        $this->topic = strtolower($topic);
        $this->count = $count;
    }

    public function handle(GeminiServices $gemini)
    {
        try {
            $cacheKey = "flashcards_" . md5($this->topic . $this->count);

            $flashcards = Cache::remember($cacheKey, 86400, function () use ($gemini) {
                return $gemini->generateFlashcardsFromTopic($this->topic, $this->count);
            });

            if (empty($flashcards) || !is_array($flashcards)) {
                throw new \Exception("Gemini returned empty or invalid data.");
            }

            \DB::transaction(function () use ($flashcards) {
                $deck = Deck::create([
                    'user_id' => $this->userId,
                    'title'   => ucfirst($this->topic),
                ]);

                foreach ($flashcards as $card) {
                    Flashcard::create([
                        'deck_id'  => $deck->id,
                        'question' => $card['question'] ?? $card['q'] ?? 'No Question',
                        'answer'   => $card['answer']   ?? $card['a'] ?? 'No Answer',
                    ]);
                }
            });

            Cache::forget("user_{$this->userId}_latest_decks");

        } catch (\Exception $e) {
            Log::error('Topic Processing Failed: ' . $e->getMessage());
            throw $e;
        }
    }
}
