<?php

namespace App\Jobs;

use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Services\GeminiServices;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class ProcessAiQuiz implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 120;

    protected $teacherId;
    protected $sourceType;
    protected $source;
    protected $count;
    protected $title;

    public function __construct($teacherId, $sourceType, $source, $count, $title)
    {
        $this->teacherId = $teacherId;
        $this->sourceType = $sourceType;
        $this->source = $source;
        $this->count = $count;
        $this->title = $title;
    }

    public function handle(GeminiServices $gemini)
    {
        try {
            $questions = $this->generateQuestions($gemini);

            if (empty($questions) || !is_array($questions)) {
                throw new \Exception("Gemini returned empty or invalid data.");
            }

            \DB::transaction(function () use ($questions) {
                $quiz = Quiz::create([
                    'teacher_id' => $this->teacherId,
                    'title' => $this->title,
                    'description' => 'AI Generated Quiz',
                    'is_active' => true,
                ]);

                foreach ($questions as $index => $q) {
                    QuizQuestion::create([
                        'quiz_id' => $quiz->id,
                        'type' => $q['type'] ?? 'multiple_choice',
                        'question' => $q['question'] ?? 'No Question',
                        'correct_answer' => $q['correct_answer'] ?? '',
                        'choices' => $q['choices'] ?? [],
                        'order' => $index,
                    ]);
                }
            });

            Cache::forget("teacher_{$this->teacherId}_latest_quizzes");
            $this->deleteStoredFile();
        } catch (\Exception $e) {
            Log::error('AI Quiz Processing Failed: ' . $e->getMessage());
            throw $e;
        }
    }

    private function generateQuestions(GeminiServices $gemini): array
    {
        if ($this->sourceType === 'pdf') {
            if (!File::exists($this->source)) {
                throw new \Exception("Uploaded PDF file was not found.");
            }

            $fileHash = md5_file($this->source);
            $cacheKey = "quiz_pdf_{$fileHash}_{$this->count}";

            return Cache::remember($cacheKey, 86400, function () use ($gemini) {
                return $gemini->generateQuizFromPdf($this->source, $this->count);
            });
        }

        if ($this->sourceType === 'txt') {
            if (!File::exists($this->source)) {
                throw new \Exception("Uploaded text file was not found.");
            }

            return $gemini->generateQuizFromText(file_get_contents($this->source) ?: '', $this->count);
        }

        if ($this->sourceType === 'topic') {
            return $gemini->generateQuizFromTopic($this->source, $this->count);
        }

        return $gemini->generateQuizFromText($this->source, $this->count);
    }

    private function deleteStoredFile(): void
    {
        if (in_array($this->sourceType, ['pdf', 'txt'], true) && File::exists($this->source)) {
            File::delete($this->source);
        }
    }
}
