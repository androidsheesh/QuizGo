<?php

namespace App\Jobs;

use App\Events\FlashcardGenerationFinished;
use App\Models\Deck;
use App\Models\Flashcard;
use App\Models\FlashcardGeneration;
use App\Services\GeminiServices;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProcessTopicFlashcards implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 120;
    public $tries = 1;
    public $failOnTimeout = true;

    protected $userId;
    protected $topic;
    protected $count;
    protected $generationId;

    public function __construct($userId, $topic, $count, $generationId)
    {
        $this->userId = $userId;
        $this->topic = strtolower($topic);
        $this->count = $count;
        $this->generationId = $generationId;
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

            $deck = DB::transaction(function () use ($flashcards) {
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

                return $deck;
            });

            $this->generation()?->markCompleted($deck);
            $this->broadcastGenerationFinished();
            Cache::forget("user_{$this->userId}_latest_decks");

        } catch (Throwable $e) {
            $this->generation()?->markFailed($e, 'AI_GENERATION_FAILED');
            Log::error('Topic Processing Failed', [
                'generation_id' => $this->generationId,
                'user_id' => $this->userId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function failed(Throwable $exception): void
    {
        $this->generation()?->markFailed($exception, 'QUEUE_JOB_FAILED');
        $this->broadcastGenerationFinished();
    }

    private function generation(): ?FlashcardGeneration
    {
        return FlashcardGeneration::find($this->generationId);
    }

    private function broadcastGenerationFinished(): void
    {
        $generation = $this->generation();

        if ($generation) {
            FlashcardGenerationFinished::dispatch($generation);
        }
    }
}
