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
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class ProcessPdfFlashcards implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // Set a timeout for the AI processing (seconds)
    public $timeout = 120;

    protected $userId;
    protected $filePath;
    protected $count;
    protected $originalTitle;

    public function __construct($userId, $filePath, $count, $originalTitle)
    {
        $this->userId = $userId;
        $this->filePath = $filePath;
        $this->count = $count;
        $this->originalTitle = $originalTitle;
    }

    public function handle(GeminiServices $gemini)
    {
        try {
            // 1. SAFETY CHECK: Ensure the file actually exists where the worker is looking
            if (!File::exists($this->filePath)) {
                Log::error("PDF Job Failed: File not found at {$this->filePath}");
                return;
            }

            // 2. Generate Cache Key
            $fileHash = md5_file($this->filePath);
            $cacheKey = "pdf_flashcards_{$fileHash}_{$this->count}";

            // 3. Fetch Flashcards (Cache or API)
            $flashcards = Cache::remember($cacheKey, 86400, function () use ($gemini) {
                return $gemini->generateFlashcardsFromPdf($this->filePath, $this->count);
            });

            // 4. Validate AI Output (Ensure it's an array)
            if (empty($flashcards) || !is_array($flashcards)) {
                throw new \Exception("Gemini returned empty or invalid data.");
            }

            // 5. Database Transactions are safer here
            \DB::transaction(function () use ($flashcards) {
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
            });

            // 6. Invalidate Cache
            Cache::forget("user_{$this->userId}_latest_decks");

            // 7. Cleanup
            if (File::exists($this->filePath)) {
                File::delete($this->filePath);
            }

        } catch (\Exception $e) {
            Log::error('PDF Processing Failed: ' . $e->getMessage());
            // Rethrow so the queue knows it failed
            throw $e;
        }
    }
}
