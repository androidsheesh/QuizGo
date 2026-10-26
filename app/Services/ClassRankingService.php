<?php

namespace App\Services;

use App\Models\Classroom;
use App\Models\QuizAttempt;
use Illuminate\Support\Collection;

class ClassRankingService
{
    /**
     * Calculate the leaderboard for a classroom: best score first, fastest best time as the tie-breaker.
     */
    public function forClassroom(Classroom $classroom): Collection
    {
        $classroom->loadMissing('students');

        $allAttempts = QuizAttempt::completed()
            ->whereIn('student_id', $classroom->students->pluck('id'))
            ->whereIn('quiz_assignment_id', $classroom->quizAssignments()->select('id'))
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
}
