<?php

namespace App\Jobs;

use App\Events\FlashcardGenerationFinished;
use App\Models\Deck;
use App\Models\Generation;
use App\Models\Quiz;
use App\Services\GeminiService;
use App\Services\SourceTextReader;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class GenerateFlashcards extends GenerationJob
{
    protected function generate(Generation $generation, SourceTextReader $reader): Deck|Quiz
    {
        $gemini = app(GeminiService::class);

        // Identical sources give identical cards, so cache to save Gemini quota
        if ($this->sourceType === 'topic') {
            $flashcards = Cache::remember('flashcards_topic_'.md5($this->source)."_{$this->count}", 86400,
                fn () => $gemini->generateFlashcardsFromTopic($this->source, $this->count));
        } else {
            $text = $reader->read($this->sourceType, $this->source);
            $flashcards = Cache::remember('flashcards_text_'.md5($text)."_{$this->count}", 86400,
                fn () => $gemini->generateFlashcardsFromText($text, $this->count));
        }

        // Results cached before validation was added may still hold invalid cards
        $flashcards = $gemini->normalizeFlashcards($flashcards);

        return DB::transaction(function () use ($generation, $flashcards) {
            $deck = Deck::create([
                'user_id' => $generation->user_id,
                'title'   => $this->title,
            ]);

            $deck->flashcards()->createMany($flashcards);

            return $deck;
        });
    }

    protected function notifySuccess(Generation $generation): void
    {
        FlashcardGenerationFinished::dispatch($generation);
    }

    protected function notifyFailure(Generation $generation): void
    {
        FlashcardGenerationFinished::dispatch($generation);
    }
}
