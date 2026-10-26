<?php

namespace App\Http\Controllers;

use App\Models\QuizAssignment;
use App\Models\QuizAttempt;
use App\Services\StudentQuizService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentQuizController extends Controller
{
    protected $quizService;

    public function __construct(StudentQuizService $quizService)
    {
        $this->quizService = $quizService;
    }

    /**
     * Show the quiz taking interface.
     */
    public function take(QuizAssignment $assignment)
    {
        $student = Auth::user();

        // 1. HTTP Security: Validate enrollment
        abort_unless(
            $assignment->classroom->students()->where('student_id', $student->id)->exists(),
            403,
            'You are not enrolled in the class for this quiz.'
        );

        // 2. Check for existing attempts via Service
        $existingAttempt = $this->quizService->getExistingAttempt($assignment, $student);

        if ($existingAttempt) {
            return redirect()->route('student.quiz.results', $existingAttempt)
                ->with('info', 'You have already completed this quiz.');
        }

        $assignment->load(['quiz.questions']);

        // 3. Only a student who already started may continue a closed or past-due quiz
        $hasStarted = $student->quizAttempts()->where('quiz_assignment_id', $assignment->id)->exists();

        if (! $hasStarted && ! $this->quizService->canStart($assignment)) {
            return redirect()->route('student.classroom.show', $assignment->classroom_id)
                ->with('error', 'This quiz is closed.');
        }

        $attempt = $this->quizService->startAttempt($assignment, $student);

        return view('student.quiz-take', [
            'assignment'       => $assignment,
            'quiz'             => $assignment->quiz,
            'remainingSeconds' => $this->quizService->remainingSeconds($attempt, $assignment),
            'elapsedSeconds'   => (int) $attempt->started_at->diffInSeconds(now()),
        ]);
    }

    /**
     * Submit the quiz and grade it.
     */
    public function submit(Request $request, QuizAssignment $assignment)
    {
        $student = Auth::user();

        // 1. HTTP Security: Validate enrollment
        abort_unless(
            $assignment->classroom->students()->where('student_id', $student->id)->exists(),
            403
        );

        // 2. Prevent multiple submissions
        if ($this->quizService->getExistingAttempt($assignment, $student)) {
            return redirect()->route('student.classroom.show', $assignment->classroom_id)
                ->with('error', 'Quiz already submitted.');
        }

        $validated = $request->validate([
            'answers'    => 'nullable|array',
            'answers.*'  => 'nullable|string|max:1000',
            'violations' => 'nullable|integer|min:0',
        ]);

        // 3. Hand off the data to the Service to grade and save. Time taken is measured
        //    on the server from when the quiz was opened, never taken from the form.
        $attempt = $this->quizService->gradeAndSubmitQuiz(
            $assignment,
            $student,
            $validated['answers'] ?? [],
            (int) ($validated['violations'] ?? 0)
        );

        if (! $attempt) {
            return redirect()->route('student.classroom.show', $assignment->classroom_id)
                ->with('error', 'This quiz has already been submitted or was never started.');
        }

        // 4. Return the HTTP Redirect
        if ($this->quizService->wasSubmittedLate($attempt, $assignment)) {
            return redirect()->route('student.quiz.results', $attempt)
                ->with('error', 'Your quiz was submitted after the deadline, so your answers were not counted.');
        }

        return redirect()->route('student.quiz.results', $attempt)
            ->with('success', 'Quiz submitted successfully!');
    }

    /**
     * Show quiz results and rankings.
     */
    public function results(QuizAttempt $attempt)
    {
        // 1. HTTP Security: Ensure the student owns this attempt
        abort_unless($attempt->student_id === Auth::id(), 403);
        abort_unless($attempt->isCompleted(), 404);

        $attempt->load([
            'quizAssignment.quiz',
            'quizAssignment.classroom',
            'answers.question'
        ]);

        // 2. Fetch the leaderboard via Service
        $rankings = $this->quizService->getQuizRankings($attempt->quizAssignment);

        return view('student.quiz-results', [
            'attempt'  => $attempt,
            'rankings' => $rankings,
            'quiz'     => $attempt->quizAssignment->quiz,
        ]);
    }
}
