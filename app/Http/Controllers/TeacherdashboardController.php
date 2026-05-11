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
    public function show(Request $request)
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
        $classesQuery = Classroom::where('teacher_id', $teacher->id)
            ->withCount('students')
            ->with(['quizAssignments.quiz', 'quizAssignments.attempts'])
            ->latest();

        if ($request->has('search_class') && $request->search_class != '') {
            $classesQuery->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search_class . '%')
                  ->orWhere('code', 'like', '%' . $request->search_class . '%');
            });
        }

        $classrooms = $classesQuery->paginate(9, ['*'], 'classesPage')->appends(['search_class' => $request->search_class]);

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
