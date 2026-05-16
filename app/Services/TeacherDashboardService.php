<?php

namespace App\Services;

use App\Models\Classroom;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class TeacherDashboardService
{
    /**
     * Get the top-level metrics for the dashboard.
     */
    public function getDashboardStats(User $teacher): array
    {
        $activeQuizzes = Quiz::where('teacher_id', $teacher->id)
            ->where('is_active', true)
            ->count();

        $totalClasses = Classroom::where('teacher_id', $teacher->id)->count();

        $totalStudents = DB::table('classroom_student')
            ->join('classrooms', 'classroom_student.classroom_id', '=', 'classrooms.id')
            ->where('classrooms.teacher_id', $teacher->id)
            ->count();

        $avgScore = QuizAttempt::whereHas('quizAssignment', function ($q) use ($teacher) {
            $q->whereHas('quiz', fn($qq) => $qq->where('teacher_id', $teacher->id));
        })->avg('score');

        return [
            'activeQuizzes' => $activeQuizzes,
            'totalClasses'  => $totalClasses,
            'totalStudents' => $totalStudents,
            'avgScore'      => round($avgScore ?? 0),
        ];
    }

    /**
     * Get paginated classrooms, optionally filtered by a search query.
     */
    public function getPaginatedClassrooms(User $teacher, ?string $searchClass): LengthAwarePaginator
    {
        $query = Classroom::where('teacher_id', $teacher->id)
            ->withCount('students')
            ->with(['quizAssignments.quiz', 'quizAssignments.attempts'])
            ->latest();

        if (!empty($searchClass)) {
            $query->where(function($q) use ($searchClass) {
                $q->where('name', 'like', '%' . $searchClass . '%')
                  ->orWhere('code', 'like', '%' . $searchClass . '%');
            });
        }

        return $query->paginate(9, ['*'], 'classesPage')->appends(['search_class' => $searchClass]);
    }

    /**
     * Get paginated recent quizzes.
     */
    public function getRecentQuizzes(User $teacher): LengthAwarePaginator
    {
        return Quiz::where('teacher_id', $teacher->id)
            ->withCount('questions')
            ->with(['assignments.classroom'])
            ->latest()
            ->paginate(9, ['*'], 'quizzesPage');
    }
}
