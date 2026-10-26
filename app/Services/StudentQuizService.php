<?php

namespace App\Services;

use App\Models\QuizAssignment;
use App\Models\QuizAttempt;
use App\Models\QuizAttemptAnswer;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class StudentQuizService
{
    /**
     * Allowance for network delay between the browser's auto-submit and the request arriving.
     */
    public const GRACE_SECONDS = 60;

    /**
     * The page only allows 3 proctoring violations before auto-submitting.
     */
    public const MAX_VIOLATIONS = 3;

    /**
     * Get the student's submitted attempt for an assignment, if any.
     */
    public function getExistingAttempt(QuizAssignment $assignment, User $student): ?QuizAttempt
    {
        return $student->quizAttempts()
            ->completed()
            ->where('quiz_assignment_id', $assignment->id)
            ->first();
    }

    /**
     * Get the student's in-progress attempt, or start one. The server-side started_at is
     * what the time limit and time_taken are measured from.
     */
    public function startAttempt(QuizAssignment $assignment, User $student): QuizAttempt
    {
        return QuizAttempt::firstOrCreate(
            [
                'student_id'         => $student->id,
                'quiz_assignment_id' => $assignment->id,
            ],
            [
                'total_questions' => $assignment->quiz->questions()->count(),
                'started_at'      => now(),
            ]
        );
    }

    /**
     * Whether a student who hasn't started yet may open the quiz.
     */
    public function canStart(QuizAssignment $assignment): bool
    {
        return $assignment->quiz->is_active && ! $assignment->isPastDue();
    }

    /**
     * Seconds left on the time limit, or null when the quiz has no time limit.
     */
    public function remainingSeconds(QuizAttempt $attempt, QuizAssignment $assignment): ?int
    {
        $timeLimit = $assignment->quiz->time_limit;

        if (! $timeLimit) {
            return null;
        }

        $elapsed = (int) $attempt->started_at->diffInSeconds(now());

        return max(0, $timeLimit * 60 - $elapsed);
    }

    /**
     * The last moment a submission still counts: the time limit or the due date, whichever comes first.
     */
    public function submissionDeadline(QuizAttempt $attempt, QuizAssignment $assignment): ?CarbonInterface
    {
        $deadlines = [];

        if ($assignment->quiz->time_limit) {
            $deadlines[] = $attempt->started_at->copy()->addMinutes($assignment->quiz->time_limit);
        }

        if ($assignment->due_at) {
            $deadlines[] = $assignment->due_at->copy();
        }

        if (empty($deadlines)) {
            return null;
        }

        return collect($deadlines)->min()->addSeconds(self::GRACE_SECONDS);
    }

    public function wasSubmittedLate(QuizAttempt $attempt, QuizAssignment $assignment): bool
    {
        $deadline = $this->submissionDeadline($attempt, $assignment);

        return $deadline !== null && $attempt->completed_at->greaterThan($deadline);
    }

    /**
     * Grade the student's answers and complete their in-progress attempt.
     * Returns null when there is no in-progress attempt (never started, or already submitted).
     * Answers that arrive after the deadline are not counted.
     */
    public function gradeAndSubmitQuiz(QuizAssignment $assignment, User $student, array $answers, int $violations = 0): ?QuizAttempt
    {
        $quiz = $assignment->quiz;
        $quiz->load('questions');

        return DB::transaction(function () use ($assignment, $student, $answers, $violations, $quiz) {
            // Lock the row so a double-click or a simultaneous auto-submit can't grade twice
            $attempt = QuizAttempt::where('student_id', $student->id)
                ->where('quiz_assignment_id', $assignment->id)
                ->lockForUpdate()
                ->first();

            if (! $attempt || $attempt->isCompleted()) {
                return null;
            }

            $now = now();
            $deadline = $this->submissionDeadline($attempt, $assignment);

            if ($deadline !== null && $now->greaterThan($deadline)) {
                $answers = [];
            }

            $score = 0;

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

                QuizAttemptAnswer::create([
                    'quiz_attempt_id'  => $attempt->id,
                    'quiz_question_id' => $question->id,
                    'student_answer'   => $studentAnswer,
                    'is_correct'       => $isCorrect,
                ]);
            }

            $attempt->update([
                'score'           => $score,
                'total_questions' => $quiz->questions->count(),
                'time_taken'      => (int) $attempt->started_at->diffInSeconds($now),
                'violations'      => max(0, min($violations, self::MAX_VIOLATIONS)),
                'completed_at'    => $now,
            ]);

            return $attempt;
        });
    }

    /**
     * Get the sorted leaderboard/rankings for a specific assignment.
     */
    public function getQuizRankings(QuizAssignment $assignment): Collection
    {
        return QuizAttempt::completed()
            ->where('quiz_assignment_id', $assignment->id)
            ->with('student')
            ->get()
            ->sortBy([
                ['score', 'desc'],     // Highest score first
                ['time_taken', 'asc'], // Tie-breaker: fastest time
            ])->values();
    }
}
