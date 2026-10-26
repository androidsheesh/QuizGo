<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreQuizRequest;
use App\Models\Classroom;
use App\Models\Quiz;
use App\Models\QuizAssignment;
use App\Services\QuizCreationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class TeacherQuizController extends Controller
{
    /**
     * List all quizzes for this teacher.
     */
    public function index(Request $request)
    {
        $query = Quiz::where('teacher_id', Auth::id())
            ->withCount('questions')
            ->with(['assignments.classroom'])
            ->latest();

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // appends() keeps the search term when changing pages
        $quizzes = $query->paginate(9)->appends($request->query());

        $classrooms = Classroom::where('teacher_id', Auth::id())->get();

        return view('teacher.assign-quiz', [
            'quizzes'    => $quizzes,
            'classrooms' => $classrooms,
        ]);
    }

    /**
     * Store a new quiz with questions.
     */
    public function store(StoreQuizRequest $request, QuizCreationService $quizCreation)
    {
        $quizCreation->create(Auth::id(), $request->validated(), $request->questions());

        return redirect()->route('teacher.quiz.index')
            ->with('success', 'Quiz created successfully!');
    }

    /**
     * View quiz details.
     */
    public function show(Quiz $quiz)
    {
        Gate::authorize('manage', $quiz);

        $quiz->load(['questions', 'assignments.classroom', 'assignments.attempts.student']);
        $classrooms = Classroom::where('teacher_id', Auth::id())->get();

        return view('teacher.quiz.show', [
            'quiz'       => $quiz,
            'classrooms' => $classrooms,
        ]);
    }

    /**
     * Open or close a quiz. Students can't start a closed quiz.
     */
    public function toggleActive(Quiz $quiz)
    {
        Gate::authorize('manage', $quiz);

        $quiz->update(['is_active' => ! $quiz->is_active]);

        return back()->with('success', $quiz->is_active ? 'Quiz opened.' : 'Quiz closed. Students can no longer start it.');
    }

    /**
     * Delete a quiz.
     */
    public function destroy(Quiz $quiz)
    {
        Gate::authorize('manage', $quiz);

        $quiz->delete();

        return redirect()->route('teacher.quiz.index')
            ->with('success', 'Quiz deleted successfully.');
    }

    /**
     * Assign a quiz to a classroom.
     */
    public function assign(Request $request)
    {
        $validated = $request->validate([
            'quiz_id'      => 'required|exists:quizzes,id',
            'classroom_id' => 'required|exists:classrooms,id',
            'due_at'       => 'nullable|date|after:now',
        ]);

        $quiz = Quiz::findOrFail($validated['quiz_id']);
        $classroom = Classroom::findOrFail($validated['classroom_id']);

        Gate::authorize('manage', $quiz);
        Gate::authorize('manage', $classroom);

        $exists = QuizAssignment::where('quiz_id', $quiz->id)
            ->where('classroom_id', $classroom->id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'This quiz is already assigned to that class.');
        }

        QuizAssignment::create([
            'quiz_id'      => $quiz->id,
            'classroom_id' => $classroom->id,
            'due_at'       => $validated['due_at'] ?? null,
        ]);

        return back()->with('success', 'Quiz assigned to ' . $classroom->name . '!');
    }

    /**
     * Remove a quiz assignment.
     */
    public function unassign(QuizAssignment $assignment)
    {
        Gate::authorize('manage', $assignment);

        $assignment->delete();

        return back()->with('success', 'Assignment removed.');
    }
}
