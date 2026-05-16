<?php

namespace App\Jobs;

use App\Events\FlashcardGenerationFinished;
use App\Events\TeacherNotificationReceived;
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
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class ProcessPdfFlashcards implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // Set a timeout for the AI processing (seconds)
    public $timeout = 120;
    public $tries = 1;
    public $failOnTimeout = true;

    protected $userId;
    protected $filePath;
    protected $count;
    protected $originalTitle;
    protected $generationId;

    public function __construct($userId, $filePath, $count, $originalTitle, $generationId)
    {
        $this->userId = $userId;
        $this->filePath = $filePath;
        $this->count = $count;
        $this->originalTitle = $originalTitle;
        $this->generationId = $generationId;
    }

    public function handle(GeminiServices $gemini)
    {
        try {
            if (!File::exists($this->filePath)) {
                throw new RuntimeException("Uploaded PDF file was not found.");
            }

            $fileHash = md5_file($this->filePath);
            $cacheKey = "pdf_flashcards_{$fileHash}_{$this->count}";

            $flashcards = Cache::remember($cacheKey, 86400, function () use ($gemini) {
                return $gemini->generateFlashcardsFromPdf($this->filePath, $this->count);
            });

            if (empty($flashcards) || !is_array($flashcards)) {
                throw new \Exception("Gemini returned empty or invalid data.");
            }

            $deck = DB::transaction(function () use ($flashcards) {
                $deck = Deck::create([
                    'user_id' => $this->userId,
                    'title'   => str_replace('.pdf', '', $this->originalTitle),
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

            if (File::exists($this->filePath)) {
                File::delete($this->filePath);
            }

        } catch (Throwable $e) {
            $this->generation()?->markFailed($e, 'AI_GENERATION_FAILED');
            Log::error('PDF Processing Failed', [
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
