<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Quiz;
use App\Models\QuizAssignment;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherdashboardController extends Controller
{
    public function show()
    {
        /** @var \App\Models\User $teacher */
        $teacher = Auth::user();

        // Active quizzes count
        $activeQuizzes = Quiz::where('teacher_id', $teacher->id)
            ->where('is_active', true)
            ->count();

        // Classrooms with student counts
        // FIX: Removed the duplicated 'teacher_id', $teacher->id arguments
        $classrooms = Classroom::where('teacher_id', $teacher->id)
            ->withCount('students')
            ->with(['quizAssignments.quiz', 'quizAssignments.attempts'])
            ->latest()
            ->get();

        // Total students across all classes
        $totalStudents = $classrooms->sum('students_count');

        // Recent quizzes
        // FIX: Removed the duplicated 'teacher_id', $teacher->id arguments
        $recentQuizzes = Quiz::where('teacher_id', $teacher->id)
            ->withCount('questions')
            ->with(['assignments.classroom'])
            ->latest()
            ->take(5)
            ->get();

        // Average score across all attempts
        $avgScore = QuizAttempt::whereHas('quizAssignment', function ($q) use ($teacher) {
            $q->whereHas('quiz', fn($qq) => $qq->where('teacher_id', $teacher->id));
        })->avg('score');

        return view('teacher.teacher-dashboard', [
            'activeQuizzes' => $activeQuizzes,
            'classrooms'    => $classrooms,
            'totalStudents' => $totalStudents,
            'recentQuizzes' => $recentQuizzes,
            'avgScore'      => round($avgScore ?? 0),
            'totalClasses'  => $classrooms->count(),
        ]);
    }
}
