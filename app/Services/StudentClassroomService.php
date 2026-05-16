<?php

namespace App\Services;

use App\Models\Classroom;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class StudentClassroomService
{
    /**
     * Get paginated classrooms with optional search filtering.
     */
    public function getEnrolledClassrooms(User $student, ?string $search): LengthAwarePaginator
    {
        $query = $student->enrolledClassrooms()
            ->withCount('quizAssignments')
            ->with('teacher');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('code', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%')
                  ->orWhereHas('teacher', function($q2) use ($search) {
                      $q2->where('firstname', 'like', '%' . $search . '%')
                         ->orWhere('lastname', 'like', '%' . $search . '%');
                  });
            });
        }

        return $query->paginate(6)->appends(['search' => $search]);
    }

    /**
     * Sort a student's assignments into pending and completed lists.
     */
    public function getStudentAssignments(Classroom $classroom, User $student): array
    {
        $classroom->load([
            'teacher',
            'quizAssignments.quiz',
            'quizAssignments.attempts' => function($q) use ($student) {
                $q->where('student_id', $student->id);
            }
        ]);

        $pendingQuizzes = collect();
        $completedQuizzes = collect();

        foreach ($classroom->quizAssignments as $assignment) {
            $attempt = $assignment->attempts->first();

            if ($attempt) {
                $completedQuizzes->push(['assignment' => $assignment, 'attempt' => $attempt]);
            } else {
                $pendingQuizzes->push($assignment);
            }
        }

        return [$pendingQuizzes, $completedQuizzes];
    }

    /**
     * Calculate and return the leaderboard/rankings for a specific classroom.
     */
    public function calculateClassRankings(Classroom $classroom): Collection
    {
        $classroom->load(['students', 'quizAssignments.attempts.student']);

        $assignmentIds = $classroom->quizAssignments->pluck('id');
        $studentIds = $classroom->students->pluck('id');

        $allAttempts = QuizAttempt::whereIn('student_id', $studentIds)
            ->whereIn('quiz_assignment_id', $assignmentIds)
            ->get()
            ->groupBy('student_id');

        $studentRankings = collect();

        foreach ($classroom->students as $classStudent) {
            $attempts = $allAttempts->get($classStudent->id, collect());

            $totalQuestions = $attempts->sum('total_questions');
            $totalScore = $attempts->sum('score');

            $studentRankings->push([
                'student'       => $classStudent,
                'best_score'    => $attempts->max('score') ?? 0,
                'avg_score'     => $totalQuestions > 0 ? round(($totalScore / $totalQuestions) * 100, 1) : 0,
                'best_time'     => $attempts->min('time_taken') ?? 0,
                'total_time'    => $attempts->sum('time_taken') ?? 0,
                'attempt_count' => $attempts->count(),
            ]);
        }

        return $studentRankings->sortBy([
            ['best_score', 'desc'],
            ['best_time', 'asc'],
        ])->values();
    }

    /**
     * Attempt to enroll a student in a class using a code.
     * Returns an array with success status and a message.
     */
    public function enrollByCode(User $student, string $code): array
    {
        $classroom = Classroom::where('code', $code)->firstOrFail();

        if ($classroom->students()->where('student_id', $student->id)->exists()) {
            return ['success' => false, 'message' => 'You are already enrolled in this class.'];
        }

        $classroom->students()->attach($student->id);

        return ['success' => true, 'message' => 'You have joined ' . $classroom->name . '!'];
    }
}
