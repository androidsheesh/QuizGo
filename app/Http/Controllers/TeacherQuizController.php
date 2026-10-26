<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Quiz;
use App\Models\QuizAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TeacherQuizController extends Controller
{
    /**
     * List all quizzes for this teacher.
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $teacher */
        $teacher = Auth::user();

        // FIX: Changed find() to where()
        $query = Quiz::where('teacher_id', $teacher->id)
            ->withCount('questions')
            ->with(['assignments.classroom'])
            ->latest();

        if ($request->has('search') && $request->search != '') {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // IMPLEMENTED: Swapped ->get() with ->paginate(9)
        // Also used appends() so the search query parameter doesn't disappear when changing pages
        $quizzes = $query->paginate(9)->appends($request->query());

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
        return redirect()->route('teacher.quiz.index');
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
            'questions.*.question'     => 'required|string|max:2000',
            'questions.*.correct_answer' => 'nullable|string|max:255',
            'questions.*.correct_choice_index' => 'nullable|integer|min:0',
            'questions.*.choices'      => 'nullable|array|max:10',
            'questions.*.choices.*'    => 'nullable|string|max:255',
        ]);

        // Resolve every question's answer before saving anything, so a quiz is never
        // stored with a question that no student can get right
        $questions = [];
        $errors = [];

        foreach ($validated['questions'] as $index => $questionData) {
            $correctAnswer = trim($questionData['correct_answer'] ?? '');
            $choices = null;

            if ($questionData['type'] === 'multiple_choice') {
                $rawChoices = $questionData['choices'] ?? [];

                // Resolve from the selected index before blank choices are dropped and indexes shift
                if (isset($questionData['correct_choice_index'])) {
                    $correctAnswer = trim($rawChoices[$questionData['correct_choice_index']] ?? '');
                }

                $choices = array_values(array_filter(
                    array_map(fn ($choice) => trim((string) $choice), $rawChoices),
                    fn ($choice) => $choice !== ''
                ));

                if (count($choices) < 2) {
                    $errors["questions.$index.choices"] = 'Question '.($index + 1).' needs at least two choices.';
                } elseif ($correctAnswer === '' || ! in_array($correctAnswer, $choices, true)) {
                    $errors["questions.$index.correct_choice_index"] = 'Question '.($index + 1).' needs a correct answer selected from its choices.';
                }
            } elseif ($correctAnswer === '') {
                $errors["questions.$index.correct_answer"] = 'Question '.($index + 1).' needs a correct answer.';
            }

            $questions[] = [
                'type'           => $questionData['type'],
                'question'       => $questionData['question'],
                'correct_answer' => $correctAnswer,
                'choices'        => $choices,
                'order'          => $index,
            ];
        }

        if ($errors) {
            throw ValidationException::withMessages($errors);
        }

        DB::transaction(function () use ($validated, $questions) {
            $quiz = Quiz::create([
                'teacher_id'  => Auth::id(),
                'title'       => $validated['title'],
                'description' => $validated['description'] ?? null,
                'time_limit'  => $validated['time_limit'] ?? null,
                'is_active'   => true,
            ]);

            $quiz->questions()->createMany($questions);
        });

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
        $classrooms = Classroom::where('teacher_id', Auth::id())->get();

        return view('teacher.quiz.show', [
            'quiz'       => $quiz,
            'classrooms' => $classrooms,
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
