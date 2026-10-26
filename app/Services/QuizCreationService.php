<?php

namespace App\Services;

use App\Models\Quiz;
use Illuminate\Support\Facades\DB;

class QuizCreationService
{
    /**
     * Create a quiz and its questions together. Used for both hand-written and AI-generated quizzes.
     *
     * @param  array  $attributes  title, and optionally description and time_limit
     * @param  array  $questions  each with type, question, correct_answer and choices (null for identification)
     */
    public function create(int $teacherId, array $attributes, array $questions): Quiz
    {
        return DB::transaction(function () use ($teacherId, $attributes, $questions) {
            $quiz = Quiz::create([
                'teacher_id'  => $teacherId,
                'title'       => $attributes['title'],
                'description' => $attributes['description'] ?? null,
                'time_limit'  => $attributes['time_limit'] ?? null,
                'is_active'   => true,
            ]);

            $quiz->questions()->createMany(
                collect($questions)->values()->map(fn (array $question, int $index) => [
                    'type'           => $question['type'],
                    'question'       => $question['question'],
                    'correct_answer' => $question['correct_answer'],
                    'choices'        => $question['choices'] ?? null,
                    'order'          => $index,
                ])->all()
            );

            return $quiz;
        });
    }
}
