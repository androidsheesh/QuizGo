<?php
namespace App\Services;

use App\Models\FlashcardGeneration;
use App\Jobs\ProcessPdfFlashcards;
use App\Jobs\ProcessTopicFlashcards;
use App\Jobs\ProcessTextFlashcards;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Throwable;

class FlashcardGenerationService
{
    public function __construct(private UploadStorage $uploads)
    {
    }

    public function generateFromTopic(int $userId, string $topic, int $count): FlashcardGeneration
    {
        $generation = FlashcardGeneration::create([
            'user_id' => $userId,
            'source_type' => 'topic',
            'status' => FlashcardGeneration::STATUS_PENDING,
        ]);

        try {
            ProcessTopicFlashcards::dispatch($userId, strtolower($topic), $count, $generation->id);
        } catch (Throwable $e) {
            $this->handleFailure($generation, $userId, $e, 'Topic Generation Dispatch Failed');
        }

        return $generation;
    }

    public function generateFromPdf(int $userId, UploadedFile $file, int $count): FlashcardGeneration
    {
        $generation = FlashcardGeneration::create([
            'user_id' => $userId,
            'source_type' => 'pdf',
            'status' => FlashcardGeneration::STATUS_PENDING,
        ]);

        try {
            $originalTitle = $file->getClientOriginalName();
            $path = $this->uploads->store($file, 'pdf_uploads');

            ProcessPdfFlashcards::dispatch($userId, $path, $count, $originalTitle, $generation->id);
        } catch (Throwable $e) {
            $this->handleFailure($generation, $userId, $e, 'PDF Generation Dispatch Failed');
        }

        return $generation;
    }

    public function generateFromText(int $userId, string $text, int $count): FlashcardGeneration
    {
        $generation = FlashcardGeneration::create([
            'user_id' => $userId,
            'source_type' => 'text',
            'status' => FlashcardGeneration::STATUS_PENDING,
        ]);

        try {
            ProcessTextFlashcards::dispatch($userId, $text, $count, $generation->id);
        } catch (Throwable $e) {
            $this->handleFailure($generation, $userId, $e, 'Text Generation Dispatch Failed');
        }

        return $generation;
    }

    /**
     * Centralized error handling for generation failures.
     */
    protected function handleFailure(FlashcardGeneration $generation, int $userId, Throwable $e, string $logMessage): void
    {
        $generation->markFailed($e, 'QUEUE_DISPATCH_FAILED');

        Log::error($logMessage, [
            'generation_id' => $generation->id,
            'user_id' => $userId,
            'error' => $e->getMessage(),
        ]);
    }
}
