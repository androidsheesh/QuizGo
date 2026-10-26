<?php

namespace Tests\Feature;

use App\Models\Classroom;
use App\Models\Quiz;
use App\Models\QuizAssignment;
use App\Models\QuizAttempt;
use App\Models\QuizQuestion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuizSubmissionTest extends TestCase
{
    use RefreshDatabase;

    private User $student;

    private Quiz $quiz;

    private QuizAssignment $assignment;

    private QuizQuestion $multipleChoice;

    private QuizQuestion $identification;

    protected function setUp(): void
    {
        parent::setUp();

        $teacher = User::factory()->create(['role' => 'teacher']);
        $this->student = User::factory()->create(['role' => 'student']);

        $classroom = Classroom::create(['teacher_id' => $teacher->id, 'name' => 'Biology', 'code' => 'BIO123']);
        $classroom->students()->attach($this->student->id);

        $this->quiz = Quiz::create(['teacher_id' => $teacher->id, 'title' => 'Cells', 'is_active' => true]);
        $this->multipleChoice = $this->quiz->questions()->create([
            'type' => 'multiple_choice',
            'question' => 'Powerhouse of the cell?',
            'choices' => ['Nucleus', 'Mitochondria'],
            'correct_answer' => 'Mitochondria',
            'order' => 0,
        ]);
        $this->identification = $this->quiz->questions()->create([
            'type' => 'identification',
            'question' => 'Basic unit of life?',
            'correct_answer' => 'Cell',
            'order' => 1,
        ]);

        $this->assignment = QuizAssignment::create(['quiz_id' => $this->quiz->id, 'classroom_id' => $classroom->id]);
    }

    private function correctAnswers(): array
    {
        return [
            $this->multipleChoice->id => '1',
            $this->identification->id => 'cell',
        ];
    }

    public function test_opening_a_quiz_starts_the_attempt_on_the_server(): void
    {
        $this->signIn($this->student)
            ->get(route('student.quiz.take', $this->assignment))
            ->assertOk();

        $attempt = QuizAttempt::sole();
        $this->assertNotNull($attempt->started_at);
        $this->assertNull($attempt->completed_at);
    }

    public function test_submission_is_graded_and_timed_by_the_server_not_the_form(): void
    {
        $this->signIn($this->student)->get(route('student.quiz.take', $this->assignment));

        $this->travel(90)->seconds();

        $this->post(route('student.quiz.submit', $this->assignment), [
            'answers' => $this->correctAnswers(),
            'time_taken' => 1,
        ])->assertRedirect();

        $attempt = QuizAttempt::sole();
        $this->assertSame(2, $attempt->score);
        $this->assertNotNull($attempt->completed_at);
        $this->assertGreaterThanOrEqual(90, $attempt->time_taken);
    }

    public function test_a_second_submission_is_rejected(): void
    {
        $this->signIn($this->student)->get(route('student.quiz.take', $this->assignment));
        $this->post(route('student.quiz.submit', $this->assignment), ['answers' => $this->correctAnswers()]);

        $this->post(route('student.quiz.submit', $this->assignment), ['answers' => []])
            ->assertSessionHas('error');

        $this->assertSame(1, QuizAttempt::count());
        $this->assertSame(2, QuizAttempt::sole()->score);
        $this->assertSame(2, QuizAttempt::sole()->answers()->count());
    }

    public function test_submitting_without_opening_the_quiz_is_rejected(): void
    {
        $this->signIn($this->student)
            ->post(route('student.quiz.submit', $this->assignment), ['answers' => $this->correctAnswers()])
            ->assertSessionHas('error');

        $this->assertSame(0, QuizAttempt::count());
    }

    public function test_answers_submitted_after_the_time_limit_are_not_counted(): void
    {
        $this->quiz->update(['time_limit' => 1]);

        $this->signIn($this->student)->get(route('student.quiz.take', $this->assignment));

        $this->travel(3)->minutes();

        $this->post(route('student.quiz.submit', $this->assignment), ['answers' => $this->correctAnswers()])
            ->assertSessionHas('error');

        $this->assertSame(0, QuizAttempt::sole()->score);
    }

    public function test_answers_within_the_grace_period_still_count(): void
    {
        $this->quiz->update(['time_limit' => 1]);

        $this->signIn($this->student)->get(route('student.quiz.take', $this->assignment));

        $this->travel(80)->seconds();

        $this->post(route('student.quiz.submit', $this->assignment), ['answers' => $this->correctAnswers()]);

        $this->assertSame(2, QuizAttempt::sole()->score);
    }

    public function test_a_past_due_quiz_cannot_be_started(): void
    {
        $this->assignment->update(['due_at' => now()->subDay()]);

        $this->signIn($this->student)
            ->get(route('student.quiz.take', $this->assignment))
            ->assertRedirect(route('student.classroom.show', $this->assignment->classroom_id));

        $this->assertSame(0, QuizAttempt::count());
    }

    public function test_an_inactive_quiz_cannot_be_started(): void
    {
        $this->quiz->update(['is_active' => false]);

        $this->signIn($this->student)
            ->get(route('student.quiz.take', $this->assignment))
            ->assertRedirect(route('student.classroom.show', $this->assignment->classroom_id));
    }

    public function test_violations_are_capped(): void
    {
        $this->signIn($this->student)->get(route('student.quiz.take', $this->assignment));

        $this->post(route('student.quiz.submit', $this->assignment), [
            'answers' => $this->correctAnswers(),
            'violations' => 500,
        ]);

        $this->assertSame(3, QuizAttempt::sole()->violations);
    }

    public function test_a_student_outside_the_class_cannot_take_the_quiz(): void
    {
        $outsider = User::factory()->create(['role' => 'student']);

        $this->signIn($outsider)
            ->get(route('student.quiz.take', $this->assignment))
            ->assertForbidden();
    }

    public function test_an_in_progress_attempt_has_no_results_page(): void
    {
        $this->signIn($this->student)->get(route('student.quiz.take', $this->assignment));

        $this->get(route('student.quiz.results', QuizAttempt::sole()))->assertNotFound();
    }
}
