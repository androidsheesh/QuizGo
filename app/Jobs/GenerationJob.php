<?php

namespace App\Jobs;

use App\Models\Deck;
use App\Models\Generation;
use App\Models\Quiz;
use App\Services\SourceTextReader;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Runs one AI generation and records its progress on the Generation row.
 * Subclasses decide what gets generated and who is told about it.
 */
abstract class GenerationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 120;
    public $tries = 1;
    public $failOnTimeout = true;

    /**
     * @param  string  $sourceType  topic, text, pdf or txt
     * @param  string  $source  the topic or text itself, or an uploads-disk path for pdf/txt
     */
    public function __construct(
        public int $generationId,
        public string $sourceType,
        public string $source,
        public int $count,
        public string $title,
    ) {
    }

    /**
     * Create the deck or quiz.
     */
    abstract protected function generate(Generation $generation, SourceTextReader $reader): Deck|Quiz;

    abstract protected function notifySuccess(Generation $generation): void;

    abstract protected function notifyFailure(Generation $generation): void;

    public function handle(SourceTextReader $reader): void
    {
        $generation = Generation::find($this->generationId);

        if (! $generation) {
            $reader->discard($this->sourceType, $this->source);

            return;
        }

        try {
            $generation->markCompleted($this->generate($generation, $reader));
            $this->notifySuccess($generation);
        } catch (Throwable $e) {
            $generation->markFailed($e, 'AI_GENERATION_FAILED');
            Log::error('AI generation failed', [
                'generation_id' => $generation->id,
                'kind' => $generation->kind,
                'user_id' => $generation->user_id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        } finally {
            // Delete on failure too, otherwise failed uploads stay on disk forever
            $reader->discard($this->sourceType, $this->source);
        }
    }

    /**
     * Called by Laravel once the job has failed for good, including timeouts.
     */
    public function failed(Throwable $exception): void
    {
        $generation = Generation::find($this->generationId);

        if ($generation) {
            $generation->markFailed($exception, 'QUEUE_JOB_FAILED');
            $this->notifyFailure($generation);
        }
    }
}
