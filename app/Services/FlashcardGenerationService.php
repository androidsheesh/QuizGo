<?php
namespace App\Services;

use App\Jobs\GenerateFlashcards;
use App\Models\Generation;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class FlashcardGenerationService
{
    public function __construct(private UploadStorage $uploads)
    {
    }

    public function generateFromTopic(int $userId, string $topic, int $count): Generation
    {
        $topic = strtolower($topic);

        return $this->start($userId, 'topic', fn () => $topic, $count, ucfirst($topic));
    }

    public function generateFromPdf(int $userId, UploadedFile $file, int $count): Generation
    {
        $title = str_replace('.pdf', '', $file->getClientOriginalName());

        return $this->start($userId, 'pdf', fn () => $this->uploads->store($file, 'pdf_uploads'), $count, $title);
    }

    public function generateFromText(int $userId, string $text, int $count): Generation
    {
        return $this->start($userId, 'text', fn () => $text, $count, ucfirst(Str::words($text, 5, '...')));
    }

    /**
     * Record the generation, then queue it. $source is resolved inside the try so a failed
     * upload is reported on the generation like any other dispatch failure.
     */
    private function start(int $userId, string $sourceType, callable $source, int $count, string $title): Generation
    {
        $generation = Generation::create([
            'kind' => Generation::KIND_FLASHCARDS,
            'user_id' => $userId,
            'source_type' => $sourceType,
            'status' => Generation::STATUS_PENDING,
        ]);

        try {
            GenerateFlashcards::dispatch($generation->id, $sourceType, $source(), $count, $title);
        } catch (Throwable $e) {
            $generation->markFailed($e, 'QUEUE_DISPATCH_FAILED');

            Log::error('Flashcard generation dispatch failed', [
                'generation_id' => $generation->id,
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);
        }

        return $generation;
    }
}
