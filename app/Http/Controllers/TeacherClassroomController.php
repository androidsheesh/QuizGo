<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\User;
use App\Services\TeacherClassroomService;
use App\Services\StudentClassroomService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class TeacherClassroomController extends Controller
{
    protected $teacherService;
    protected $studentService;

    // Inject BOTH services here
    public function __construct(
        TeacherClassroomService $teacherService,
        StudentClassroomService $studentService
    ) {
        $this->teacherService = $teacherService;
        $this->studentService = $studentService;
    }

    public function show(Classroom $classroom)
    {
        // 1. Check the Policy (Throws a 403 automatically if they aren't the owner)
        Gate::authorize('manage', $classroom);

        // 2. Reuse the ranking logic from your Student Service!
        $studentRankings = $this->studentService->calculateClassRankings($classroom);

        return view('teacher.classroom-detail', compact('classroom', 'studentRankings'));
    }

    public function store(Request $request)
    {
        // Note: No policy check needed here since they are creating a NEW classroom
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        $result = $this->teacherService->createClassroom(Auth::id(), $validated);

        return back()->with('success', 'Classroom "' . $validated['name'] . '" created! Code: ' . $result['code']);
    }

    public function addStudent(Request $request, Classroom $classroom)
    {
        Gate::authorize('manage', $classroom);

        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $result = $this->teacherService->addStudentByEmail($classroom, $validated['email']);

        if (!$result['success']) {
            return back()->withErrors(['email' => $result['error']]);
        }

        return back()->with('success', $result['message']);
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
