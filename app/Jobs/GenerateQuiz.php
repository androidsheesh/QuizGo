<?php

namespace App\Jobs;

use App\Events\TeacherNotificationReceived;
use App\Models\Deck;
use App\Models\Generation;
use App\Models\Quiz;
use App\Services\GeminiService;
use App\Services\QuizCreationService;
use App\Services\SourceTextReader;
use Illuminate\Support\Facades\Cache;

class GenerateQuiz extends GenerationJob
{
    protected function generate(Generation $generation, SourceTextReader $reader): Deck|Quiz
    {
        $gemini = app(GeminiService::class);

        // Identical sources give identical questions, so cache to save Gemini quota
        if ($this->sourceType === 'topic') {
            $questions = Cache::remember('quiz_topic_'.md5($this->source)."_{$this->count}", 86400,
                fn () => $gemini->generateQuizFromTopic($this->source, $this->count));
        } else {
            $text = $reader->read($this->sourceType, $this->source);
            $questions = Cache::remember('quiz_text_'.md5($text)."_{$this->count}", 86400,
                fn () => $gemini->generateQuizFromText($text, $this->count));
        }

        // Results cached before validation was added may still hold invalid questions
        $questions = $gemini->normalizeQuizQuestions($questions);

        return app(QuizCreationService::class)->create(
            $generation->user_id,
            ['title' => $this->title, 'description' => 'AI Generated Quiz'],
            $questions,
        );
    }

    protected function notifySuccess(Generation $generation): void
    {
        TeacherNotificationReceived::dispatch(
            $generation->user_id,
            'Quiz ready! 🎉',
            "\"{$this->title}\" has been generated successfully.",
            'success',
            $generation->resultUrl(),
            'View Quiz',
        );
    }

    protected function notifyFailure(Generation $generation): void
    {
        TeacherNotificationReceived::dispatch(
            $generation->user_id,
            'Quiz generation failed',
            'We couldn\'t generate your quiz. Please try a different prompt or try again later.',
            'error',
        );
    }
}
