<?php

namespace App\Http\Controllers;

use App\Models\QuizAssignment;
use App\Models\QuizAttempt;
use App\Models\QuizAttemptAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentQuizController extends Controller
{
    /**
     * Show the quiz taking interface.
     */
    public function take(QuizAssignment $assignment)
    {
        /** @var \App\Models\User $student */
        $student = Auth::user();

        // 1. Validate student is enrolled in the classroom
        if (!$assignment->classroom->students()->where('student_id', $student->id)->exists()) {
            abort(403, 'You are not enrolled in the class for this quiz.');
        }

        // 2. Check if already attempted
        $existingAttempt = $student->quizAttempts()
            ->where('quiz_assignment_id', $assignment->id)
            ->first();

        if ($existingAttempt) {
            return redirect()->route('student.quiz.results', $existingAttempt)
                ->with('info', 'You have already completed this quiz.');
        }

        $assignment->load(['quiz.questions']);

        return view('student.quiz-take', [
            'assignment' => $assignment,
            'quiz'       => $assignment->quiz,
        ]);
    }

    /**
     * Submit the quiz and grade it.
     */
    public function submit(Request $request, QuizAssignment $assignment)
    {
        /** @var \App\Models\User $student */
        $student = Auth::user();

        // Validate student is enrolled
        if (!$assignment->classroom->students()->where('student_id', $student->id)->exists()) {
            abort(403);
        }

        // Prevent multiple submissions
        // Prevent multiple submissions
        if ($student->quizAttempts()->where('quiz_assignment_id', $assignment->id)->exists()) {
            return redirect()->route('student.classroom.show', $assignment->classroom_id)
                ->with('error', 'Quiz already submitted.');
        }

        $quiz = $assignment->quiz;
        $quiz->load('questions');

        $timeTaken = $request->input('time_taken', 0); // time in seconds passed from frontend

        // Create Attempt Record via Relationship (student_id is injected automatically)
        $attempt = $student->quizAttempts()->create([
            'quiz_assignment_id' => $assignment->id,
            'score'              => 0, // will calculate below
            'total_questions'    => $quiz->questions->count(),
            'time_taken'         => $timeTaken,
            'started_at'         => now()->subSeconds($timeTaken),
            'completed_at'       => now(),
        ]);

        $score = 0;
        $answers = $request->input('answers', []);

        foreach ($quiz->questions as $question) {
            $studentAnswer = $answers[$question->id] ?? null;
            $isCorrect = false;

            if ($studentAnswer !== null) {
                if ($question->type === 'multiple_choice') {
                    // For MC, studentAnswer is the index (0, 1, 2, 3) from radio buttons
                    $choices = $question->choices ?? [];
                    if (isset($choices[$studentAnswer])) {
                        $selectedText = $choices[$studentAnswer];
                        $isCorrect = strtolower(trim($selectedText)) === strtolower(trim($question->correct_answer));
                        $studentAnswer = $selectedText; // Save the actual text for the record
                    }
                } elseif ($question->type === 'identification') {
                    // For ID, case-insensitive match
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

        // Update final score
        $attempt->update(['score' => $score]);

        return redirect()->route('student.quiz.results', $attempt)
            ->with('success', 'Quiz submitted successfully!');
    }

    /**
     * Show quiz results and rankings.
     */
    public function results(QuizAttempt $attempt)
    {
        /** @var \App\Models\User $student */
        $student = Auth::user();

        // Security check
        if ($attempt->student_id !== $student->id) {
            abort(403);
        }

        $attempt->load([
            'quizAssignment.quiz',
            'quizAssignment.classroom',
            'answers.question'
        ]);

        $assignment = $attempt->quizAssignment;

        // Calculate Rankings for ALL students on this specific assignment
        $allAttempts = QuizAttempt::where('quiz_assignment_id', $assignment->id)
            ->with('student') // Eager load the student so you can show their names on the leaderboard!
            ->get();

        // Sort them to determine the ranking
        $rankings = $allAttempts->sortBy([
            ['score', 'desc'],     // Highest score first
            ['time_taken', 'asc'], // If there's a tie, fastest time wins
        ])->values();

        return view('student.quiz-results', [
            'attempt'  => $attempt,
            'rankings' => $rankings,
            'quiz'     => $assignment->quiz,
        ]);
    }
}
