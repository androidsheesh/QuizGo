<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentClassroomController extends Controller
{
    /**
     * Show all classrooms the student is enrolled in.
     */
    public function index()
    {
        /** @var \App\Models\User $student */
        $student = Auth::user();
        $classrooms = $student->enrolledClassrooms()->withCount('quizAssignments')->with('teacher')->get();

        return view('student.assignments', [
            'classrooms' => $classrooms,
        ]);
    }

    /**
     * Show details of a specific classroom.
     */
    public function show(Classroom $classroom)
    {
        /** @var \App\Models\User $student */
        $student = Auth::user();

        // Ensure student is enrolled in this classroom
        if (!$classroom->students()->where('student_id', $student->id)->exists()) {
            abort(403, 'You are not enrolled in this class.');
        }

        $classroom->load(['teacher', 'quizAssignments.quiz', 'quizAssignments.attempts' => function($q) use ($student) {
            $q->where('student_id', $student->id);
        }]);

        $assignments = $classroom->quizAssignments;

        // Separate into pending and completed for this student
        $pendingQuizzes = collect();
        $completedQuizzes = collect();

        foreach ($assignments as $assignment) {
            $attempt = $assignment->attempts->first();
            if ($attempt) {
                $completedQuizzes->push([
                    'assignment' => $assignment,
                    'attempt'    => $attempt,
                ]);
            } else {
                $pendingQuizzes->push($assignment);
            }
        }

        // --- Class Rankings Calculation (same logic as teacher view) ---
        $classroom->load(['students', 'quizAssignments.attempts.student']);

        $assignmentIds = $assignments->pluck('id');
        $studentIds = $classroom->students->pluck('id');

        // FIX: Changed find() to whereIn()
        $allAttempts = QuizAttempt::whereIn('student_id', $studentIds)
            ->whereIn('quiz_assignment_id', $assignmentIds)
            ->get()
            ->groupBy('student_id');

        $studentRankings = collect();

        foreach ($classroom->students as $classStudent) {
            $attempts = $allAttempts->get($classStudent->id, collect());

            $bestScore  = $attempts->max('score') ?? 0;
            $totalScore = $attempts->sum('score');
            $totalQuestions = $attempts->sum('total_questions');
            $avgScore   = $totalQuestions > 0 ? round(($totalScore / $totalQuestions) * 100, 1) : 0;
            $bestTime   = $attempts->min('time_taken') ?? 0;
            $totalTime  = $attempts->sum('time_taken') ?? 0;
            $attemptCount = $attempts->count();

            $studentRankings->push([
                'student'       => $classStudent,
                'best_score'    => $bestScore,
                'avg_score'     => $avgScore,
                'best_time'     => $bestTime,
                'total_time'    => $totalTime,
                'attempt_count' => $attemptCount,
            ]);
        }

        $studentRankings = $studentRankings
            ->sortBy([
                ['best_score', 'desc'],
                ['best_time', 'asc'],
            ])
            ->values();

        return view('student.classroom-detail', [
            'classroom'        => $classroom,
            'pendingQuizzes'   => $pendingQuizzes,
            'completedQuizzes' => $completedQuizzes,
            'studentRankings'  => $studentRankings,
        ]);
    }

    /**
     * Join a classroom using a class code.
     */
    public function join(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|exists:classrooms,code',
        ]);

        /** @var \App\Models\User $student */
        $student = Auth::user();

        // FIX: Changed find() to where()
        $classroom = Classroom::where('code', $validated['code'])->firstOrFail();

        // Check if already enrolled
        if ($classroom->students()->where('student_id', $student->id)->exists()) {
            return back()->withErrors(['code' => 'You are already enrolled in this class.']);
        }

        $classroom->students()->attach($student->id);

        return back()->with('success', 'You have joined ' . $classroom->name . '!');
    }
}
