<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TeacherClassroomController extends Controller
{
    /**
     * Show classroom detail with students, scores, and rankings.
     */
    public function show(Classroom $classroom)
    {
        $this->authorizeTeacher($classroom);

        // Fixed typo: 'quizAssignmnets' -> 'quizAssignments'
        $classroom->load(['students', 'quizAssignments.quiz', 'quizAssignments.attempts.student']);

        // 1. Get all IDs needed
        $assignmentIds = $classroom->quizAssignments->pluck('id');
        $studentIds = $classroom->students->pluck('id');

        // 2. Fetch all attempts in ONE query and group them by student_id
        // FIX: Changed find() to whereIn()
        $allAttempts = QuizAttempt::whereIn('student_id', $studentIds)
            ->whereIn('quiz_assignment_id', $assignmentIds)
            ->get()
            ->groupBy('student_id');

        // 3. Build student's rankings
        $studentRankings = collect();

        foreach ($classroom->students as $student) {
            // Pull the student's attempts from the grouped collection we just created
            $attempts = $allAttempts->get($student->id, collect());

            $bestScore  = $attempts->max('score') ?? 0;
            $totalScore = $attempts->sum('score');
            $totalQuestions = $attempts->sum('total_questions');
            $avgScore   = $totalQuestions > 0 ? round(($totalScore / $totalQuestions) * 100, 1) : 0;
            $bestTime   = $attempts->min('time_taken') ?? 0;
            $totalTime  = $attempts->sum('time_taken') ?? 0;
            $attemptCount = $attempts->count();

            $studentRankings->push([
                'student'       => $student,
                'best_score'    => $bestScore,
                'avg_score'     => $avgScore,
                'best_time'     => $bestTime,
                'total_time'    => $totalTime,
                'attempt_count' => $attemptCount,
            ]);
        }

        // Sort: highest score DESC, then shortest time ASC
        $studentRankings = $studentRankings
            ->sortBy([
                ['best_score', 'desc'],
                ['best_time', 'asc'],
            ])
            ->values();

        return view('teacher.classroom-detail', [
            'classroom'       => $classroom,
            'studentRankings' => $studentRankings,
        ]);
    }

    /**
     * Create a new classroom.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        // Generate a unique 6-character code
        do {
            $code = strtoupper(Str::random(6));
        // FIX: Changed find() to where()
        } while (Classroom::where('code', $code)->exists());

        Classroom::create([
            'teacher_id'  => Auth::id(),
            'name'        => $validated['name'],
            'code'        => $code,
            'description' => $validated['description'] ?? null,
        ]);

        return back()->with('success', 'Classroom "' . $validated['name'] . '" created! Code: ' . $code);
    }

    /**
     * Add a student to a classroom by email.
     */
    public function addStudent(Request $request, Classroom $classroom)
    {
        $this->authorizeTeacher($classroom);

        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        // Excellent use of firstWhere!
        $student = User::firstWhere([
            'email' => $validated['email'],
            'role'  => 'student',
        ]);

        if (!$student) {
            return back()->withErrors(['email' => 'No student found with that email.']);
        }

        if ($classroom->students()->where('student_id', $student->id)->exists()) {
            return back()->withErrors(['email' => 'This student is already in this class.']);
        }

        $classroom->students()->attach($student->id);

        return back()->with('success', $student->firstname . ' ' . $student->lastname . ' added to ' . $classroom->name . '!');
    }

    /**
     * Remove a student from a classroom.
     */
    public function removeStudent(Classroom $classroom, User $user)
    {
        $this->authorizeTeacher($classroom);

        $classroom->students()->detach($user->id);

        return back()->with('success', 'Student removed from class.');
    }

    /**
     * Delete a classroom.
     */
    public function destroy(Classroom $classroom)
    {
        $this->authorizeTeacher($classroom);

        // FIX: Removed the model variable from inside the delete method
        $classroom->delete();

        return redirect()->route('teacher.dashboard')
            ->with('success', 'Classroom deleted.');
    }

    /**
     * Ensure the authenticated teacher owns this classroom.
     */
    private function authorizeTeacher(Classroom $classroom): void
    {
        if ($classroom->teacher_id !== Auth::id()) {
            abort(403);
        }
    }
}
