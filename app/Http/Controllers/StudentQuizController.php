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

        // 3. Hand off the data to the Service to grade and save
        $attempt = $this->quizService->gradeAndSubmitQuiz(
            $assignment,
            $student,
            $request->input('answers', []),
            $request->input('time_taken', 0)
        );

        // 4. Return the HTTP Redirect
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
