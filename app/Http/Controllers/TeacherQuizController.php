<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Quiz;
use App\Models\QuizAssignment;
use App\Models\QuizQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherQuizController extends Controller
{
    /**
     * List all quizzes for this teacher.
     */
    public function index()
    {
        /** @var \App\Models\User $teacher */
        $teacher = Auth::user();

        // FIX: Changed find() to where()
        $quizzes = Quiz::where('teacher_id', $teacher->id)
            ->withCount('questions')
            ->with(['assignments.classroom'])
            ->latest()
            ->get();

        // FIX: Changed find() to where()
        $classrooms = Classroom::where('teacher_id', $teacher->id)->get();

        return view('teacher.assign-quiz', [
            'quizzes'    => $quizzes,
            'classrooms' => $classrooms,
        ]);
    }

    /**
     * Show the create quiz form.
     */
    public function create()
    {
        // FIX: Changed find() to where()
        $classrooms = Classroom::where('teacher_id', Auth::id())->get();

        return view('teacher.assign.quiz', [
            'classrooms' => $classrooms,
        ]);
    }

    /**
     * Store a new quiz with questions.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'                    => 'required|string|max:255',
            'description'              => 'nullable|string|max:1000',
            'time_limit'               => 'nullable|integer|min:1|max:180',
            'questions'                => 'required|array|min:1',
            'questions.*.type'         => 'required|in:multiple_choice,identification',
            'questions.*.question'     => 'required|string',
            'questions.*.correct_answer' => 'nullable|string',
            'questions.*.correct_choice_index' => 'nullable|integer',
            'questions.*.choices'      => 'nullable|array',
            'questions.*.choices.*'    => 'nullable|string',
        ]);

        $quiz = Quiz::create([
            'teacher_id'  => Auth::id(),
            'title'       => $validated['title'],
            'description' => $validated['description'] ?? null,
            'time_limit'  => $validated['time_limit'] ?? null,
            'is_active'   => true,
        ]);

        foreach ($validated['questions'] as $index => $questionData) {
            // For MC questions, resolve the correct answer from the selected choice index
            $correctAnswer = $questionData['correct_answer'] ?? '';
            if ($questionData['type'] === 'multiple_choice' && isset($questionData['correct_choice_index'])) {
                $choices = $questionData['choices'] ?? [];
                $correctAnswer = $choices[$questionData['correct_choice_index']] ?? $correctAnswer;
            }

            QuizQuestion::create([
                'quiz_id'        => $quiz->id,
                'type'           => $questionData['type'],
                'question'       => $questionData['question'],
                'correct_answer' => $correctAnswer,
                'choices'        => $questionData['type'] === 'multiple_choice'
                    ? ($questionData['choices'] ?? [])
                    : null,
                'order'          => $index,
            ]);
        }

        return redirect()->route('teacher.quiz.index')
            ->with('success', 'Quiz created successfully!');
    }

    /**
     * View quiz details.
     */
    public function show(Quiz $quiz)
    {
        $this->authorizeTeacher($quiz);

        $quiz->load(['questions', 'assignments.classroom', 'assignments.attempts.student']);

        return view('teacher.quiz-detail', [
            'quiz' => $quiz,
        ]);
    }

    /**
     * Delete a quiz.
     */
    public function destroy(Quiz $quiz)
    {
        $this->authorizeTeacher($quiz);

        // FIX: Removed the model variable from inside the delete method
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

        // Ensure teacher owns both the quiz and the classroom
        // FIX: Changed find() to where()
        $quiz = Quiz::where('teacher_id', Auth::id())->findOrFail($validated['quiz_id']);
        $classroom = Classroom::where('teacher_id', Auth::id())->findOrFail($validated['classroom_id']);

        // Prevent duplicate assignments
        // FIX: Changed find() to where()
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
        // Ensure teacher owns the quiz
        if ($assignment->quiz->teacher_id !== Auth::id()) {
            abort(403);
        }

        // FIX: Removed the model variable from inside the delete method
        $assignment->delete();

        return back()->with('success', 'Assignment removed.');
    }

    /**
     * Ensure the authenticated teacher owns this quiz.
     */
    private function authorizeTeacher(Quiz $quiz): void
    {
        if ($quiz->teacher_id !== Auth::id()) {
            abort(403);
        }
    }
}
