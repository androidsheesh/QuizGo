<?php

namespace App\Services;

use App\Models\Classroom;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class TeacherClassroomService
{
    /**
     * Calculate and return the leaderboard/rankings for a specific classroom.
     * (Note: If you already have this in StudentClassroomService, you can
     * just inject that service into the Teacher controller to share it!)
     */
    public function calculateClassRankings(Classroom $classroom): Collection
    {
        $classroom->load(['students', 'quizAssignments.quiz', 'quizAssignments.attempts.student']);

        $assignmentIds = $classroom->quizAssignments->pluck('id');
        $studentIds = $classroom->students->pluck('id');

        $allAttempts = QuizAttempt::whereIn('student_id', $studentIds)
            ->whereIn('quiz_assignment_id', $assignmentIds)
            ->get()
            ->groupBy('student_id');

        $studentRankings = collect();

        foreach ($classroom->students as $student) {
            $attempts = $allAttempts->get($student->id, collect());

            $totalQuestions = $attempts->sum('total_questions');
            $totalScore = $attempts->sum('score');

            $studentRankings->push([
                'student'       => $student,
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
     * Generate a unique code and create the classroom.
     */
    public function createClassroom(int $teacherId, array $data): array
    {
        do {
            $code = strtoupper(Str::random(6));
        } while (Classroom::where('code', $code)->exists());

        $classroom = Classroom::create([
            'teacher_id'  => $teacherId,
            'name'        => $data['name'],
            'code'        => $code,
            'description' => $data['description'] ?? null,
        ]);

        return ['success' => true, 'classroom' => $classroom, 'code' => $code];
    }

    /**
     * Attempt to add a student to a classroom via their email.
     */
    public function addStudentByEmail(Classroom $classroom, string $email): array
    {
        $student = User::firstWhere([
            'email' => $email,
            'role'  => 'student',
        ]);

        if (!$student) {
            return ['success' => false, 'error' => 'No student found with that email.'];
        }

        if ($classroom->students()->where('student_id', $student->id)->exists()) {
            return ['success' => false, 'error' => 'This student is already in this class.'];
        }

        $classroom->students()->attach($student->id);

        return ['success' => true, 'message' => "{$student->firstname} {$student->lastname} added to {$classroom->name}!"];
    }

    /**
     * Remove a student from a classroom.
     */
    public function removeStudent(Classroom $classroom, User $user): void
    {
        $classroom->students()->detach($user->id);
    }

    /**
     * Delete a classroom.
     */
    public function deleteClassroom(Classroom $classroom): void
    {
        $classroom->delete();
    }
}
