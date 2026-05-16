<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Services\StudentClassroomService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentClassroomController extends Controller
{
    protected $classroomService;

    public function __construct(StudentClassroomService $classroomService)
    {
        $this->classroomService = $classroomService;
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
        $student = Auth::user();

        // Security check: Ensure student is enrolled in this classroom
        abort_unless(
            $classroom->students()->where('student_id', $student->id)->exists(),
            403,
            'You are not enrolled in this class.'
        );

        // Fetch data via the service
        [$pendingQuizzes, $completedQuizzes] = $this->classroomService->getStudentAssignments($classroom, $student);

        $studentRankings = $this->classroomService->calculateClassRankings($classroom);

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

        $enrollment = $this->classroomService->enrollByCode(Auth::user(), $validated['code']);

        if (!$enrollment['success']) {
            return back()->withErrors(['code' => $enrollment['message']]);
        }

        return back()->with('success', $enrollment['message']);
    }
}
