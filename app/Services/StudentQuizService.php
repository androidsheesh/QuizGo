<?php

namespace App\Services;

use App\Models\QuizAssignment;
use App\Models\QuizAttempt;
use App\Models\QuizAttemptAnswer;
use App\Models\User;
use Illuminate\Support\Collection;

class StudentQuizService
{
    /**
     * Check if a student has already attempted a specific quiz assignment.
     */
    public function getExistingAttempt(QuizAssignment $assignment, User $student): ?QuizAttempt
    {
        return $student->quizAttempts()
            ->where('quiz_assignment_id', $assignment->id)
            ->first();
    }

    /**
     * Grade the student's answers, save the attempt, and return the record.
     */
    public function gradeAndSubmitQuiz(QuizAssignment $assignment, User $student, array $answers, int $timeTaken, int $violations = 0): QuizAttempt
    {
        $quiz = $assignment->quiz;
        $quiz->load('questions');

        // 1. Create the initial attempt record
        $attempt = $student->quizAttempts()->create([
            'quiz_assignment_id' => $assignment->id,
            'score'              => 0, // Placeholder, calculated below
            'total_questions'    => $quiz->questions->count(),
            'time_taken'         => $timeTaken,
            'violations'         => $violations,
            'started_at'         => now()->subSeconds($timeTaken),
            'completed_at'       => now(),
        ]);

        $score = 0;

        // 2. The Grading Engine
        foreach ($quiz->questions as $question) {
            $studentAnswer = $answers[$question->id] ?? null;
            $isCorrect = false;

            if ($studentAnswer !== null) {
                if ($question->type === 'multiple_choice') {
                    $choices = $question->choices ?? [];

                    if (isset($choices[$studentAnswer])) {
                        $selectedText = $choices[$studentAnswer];
                        $isCorrect = strtolower(trim($selectedText)) === strtolower(trim($question->correct_answer));
                        $studentAnswer = $selectedText; // Save actual text, not the index
                    }
                } elseif ($question->type === 'identification') {
                    $isCorrect = strtolower(trim($studentAnswer)) === strtolower(trim($question->correct_answer));
                }
            }

            if ($isCorrect) {
                $score++;
            }

            // 3. Save the individual answer
            QuizAttemptAnswer::create([
                'quiz_attempt_id'  => $attempt->id,
                'quiz_question_id' => $question->id,
                'student_answer'   => $studentAnswer,
                'is_correct'       => $isCorrect,
            ]);
        }

        // 4. Update and finalize the score
        $attempt->update(['score' => $score]);

        return $attempt;
    }

    /**
     * Get the sorted leaderboard/rankings for a specific assignment.
     */
    public function getQuizRankings(QuizAssignment $assignment): Collection
    {
        return QuizAttempt::where('quiz_assignment_id', $assignment->id)
            ->with('student')
            ->get()
            ->sortBy([
                ['score', 'desc'],     // Highest score first
                ['time_taken', 'asc'], // Tie-breaker: fastest time
            ])->values();
    }
}
