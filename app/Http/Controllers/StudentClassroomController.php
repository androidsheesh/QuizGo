<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Services\ClassRankingService;
use App\Services\StudentClassroomService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class StudentClassroomController extends Controller
{
    public function __construct(
        private StudentClassroomService $classroomService,
        private ClassRankingService $rankingService,
    ) {
    }

    /**
     * Show all classrooms the student is enrolled in.
     */
    public function index(Request $request)
    {
        $classrooms = $this->classroomService->getEnrolledClassrooms(
            Auth::user(),
            $request->input('search')
        );

        return view('student.assignments', compact('classrooms'));
    }

    /**
     * Show details of a specific classroom.
     */
    public function show(Classroom $classroom)
    {
        Gate::authorize('view', $classroom);

        [$pendingQuizzes, $completedQuizzes] = $this->classroomService->getStudentAssignments($classroom, Auth::user());

        $studentRankings = $this->rankingService->forClassroom($classroom);

        return view('student.classroom-detail', compact(
            'classroom',
            'pendingQuizzes',
            'completedQuizzes',
            'studentRankings'
        ));
    }

    /**
     * Join a classroom using a class code.
     */
    public function join(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|exists:classrooms,code',
        ]);

        $classroom = $this->classroomService->enrollByCode(Auth::user(), $validated['code']);

        return back()->with('success', 'You have joined ' . $classroom->name . '!');
    }
}
