<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\User;
use App\Services\ClassRankingService;
use App\Services\TeacherClassroomService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class TeacherClassroomController extends Controller
{
    public function __construct(
        private TeacherClassroomService $teacherService,
        private ClassRankingService $rankingService,
    ) {
    }

    public function show(Classroom $classroom)
    {
        Gate::authorize('manage', $classroom);

        $classroom->load(['students', 'quizAssignments.quiz', 'quizAssignments.attempts']);
        $studentRankings = $this->rankingService->forClassroom($classroom);

        return view('teacher.classroom-detail', compact('classroom', 'studentRankings'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        $classroom = $this->teacherService->createClassroom(Auth::id(), $validated);

        return back()->with('success', 'Classroom "' . $classroom->name . '" created! Code: ' . $classroom->code);
    }

    public function addStudent(Request $request, Classroom $classroom)
    {
        Gate::authorize('manage', $classroom);

        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $student = $this->teacherService->addStudentByEmail($classroom, $validated['email']);

        return back()->with('success', "{$student->firstname} {$student->lastname} added to {$classroom->name}!");
    }

    public function removeStudent(Classroom $classroom, User $user)
    {
        Gate::authorize('manage', $classroom);

        $this->teacherService->removeStudent($classroom, $user);

        return back()->with('success', 'Student removed from class.');
    }

    public function destroy(Classroom $classroom)
    {
        Gate::authorize('manage', $classroom);

        $this->teacherService->deleteClassroom($classroom);

        return redirect()->route('teacher.dashboard')->with('success', 'Classroom deleted.');
    }
}
