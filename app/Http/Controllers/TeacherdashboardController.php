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

        // Total classes count
        $totalClasses = Classroom::where('teacher_id', $teacher->id)->count();

        // Total students across all classes
        $totalStudents = \Illuminate\Support\Facades\DB::table('classroom_student')
            ->join('classrooms', 'classroom_student.classroom_id', '=', 'classrooms.id')
            ->where('classrooms.teacher_id', $teacher->id)
            ->count();

        // Classrooms with student counts
        $classrooms = Classroom::where('teacher_id', $teacher->id)
            ->withCount('students')
            ->with(['quizAssignments.quiz', 'quizAssignments.attempts'])
            ->latest()
            ->paginate(9, ['*'], 'classesPage');

        // Recent quizzes
        // FIX: Removed the duplicated 'teacher_id', $teacher->id arguments
        $recentQuizzes = Quiz::where('teacher_id', $teacher->id)
            ->withCount('questions')
            ->with(['assignments.classroom'])
            ->latest()
            ->paginate(9, ['*'], 'quizzesPage');

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
            'totalClasses'  => $totalClasses,
        ]);
    }
}
