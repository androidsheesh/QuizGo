<?php

namespace Tests\Feature;

use App\Models\Quiz;
use App\Models\User;
use App\Services\GeminiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class TeacherQuizTest extends TestCase
{
    use RefreshDatabase;

    private User $teacher;

    protected function setUp(): void
    {
        parent::setUp();

        $this->teacher = User::factory()->create(['role' => 'teacher']);
    }

    public function test_a_valid_quiz_is_saved(): void
    {
        $this->signIn($this->teacher)->post(route('teacher.quiz.store'), [
            'title' => 'Cells',
            'questions' => [
                ['type' => 'multiple_choice', 'question' => 'Powerhouse?', 'choices' => ['Nucleus', 'Mitochondria'], 'correct_choice_index' => 1],
                ['type' => 'identification', 'question' => 'Basic unit of life?', 'correct_answer' => 'Cell'],
            ],
        ])->assertSessionHasNoErrors();

        $quiz = Quiz::sole();
        $this->assertSame(['Mitochondria', 'Cell'], $quiz->questions->pluck('correct_answer')->all());
    }

    public function test_a_correct_choice_that_points_at_a_blank_choice_is_rejected(): void
    {
        $this->signIn($this->teacher)->post(route('teacher.quiz.store'), [
            'title' => 'Cells',
            'questions' => [
                ['type' => 'multiple_choice', 'question' => 'Powerhouse?', 'choices' => ['Nucleus', 'Mitochondria', ''], 'correct_choice_index' => 2],
            ],
        ])->assertSessionHasErrors('questions.0.correct_choice_index');

        $this->assertSame(0, Quiz::count());
    }

    public function test_a_multiple_choice_question_needs_two_choices(): void
    {
        $this->signIn($this->teacher)->post(route('teacher.quiz.store'), [
            'title' => 'Cells',
            'questions' => [
                ['type' => 'multiple_choice', 'question' => 'Powerhouse?', 'choices' => ['Mitochondria'], 'correct_choice_index' => 0],
            ],
        ])->assertSessionHasErrors('questions.0.choices');
    }

    public function test_an_identification_question_needs_an_answer(): void
    {
        $this->signIn($this->teacher)->post(route('teacher.quiz.store'), [
            'title' => 'Cells',
            'questions' => [
                ['type' => 'identification', 'question' => 'Basic unit of life?', 'correct_answer' => '  '],
            ],
        ])->assertSessionHasErrors('questions.0.correct_answer');
    }

    public function test_a_teacher_can_close_and_reopen_a_quiz(): void
    {
        $quiz = Quiz::create(['teacher_id' => $this->teacher->id, 'title' => 'Cells', 'is_active' => true]);

        $this->signIn($this->teacher)->patch(route('teacher.quiz.toggle', $quiz));
        $this->assertFalse($quiz->fresh()->is_active);

        $this->patch(route('teacher.quiz.toggle', $quiz));
        $this->assertTrue($quiz->fresh()->is_active);
    }

    public function test_ai_questions_without_a_matching_correct_answer_are_dropped(): void
    {
        $questions = app(GeminiService::class)->normalizeQuizQuestions([
            ['question' => 'Good', 'type' => 'multiple_choice', 'choices' => ['A', 'B'], 'correct_answer' => 'b'],
            ['question' => 'Answer not a choice', 'choices' => ['A', 'B'], 'correct_answer' => 'C'],
            ['question' => 'One choice', 'choices' => ['A'], 'correct_answer' => 'A'],
            ['question' => '', 'choices' => ['A', 'B'], 'correct_answer' => 'A'],
            'not an array',
        ]);

        $this->assertCount(1, $questions);
        $this->assertSame('B', $questions[0]['correct_answer']);
        $this->assertSame('multiple_choice', $questions[0]['type']);
    }

    public function test_ai_output_with_no_usable_questions_fails(): void
    {
        $this->expectException(RuntimeException::class);

        app(GeminiService::class)->normalizeQuizQuestions([
            ['question' => 'Bad', 'choices' => ['A', 'B'], 'correct_answer' => 'Z'],
        ]);
    }
}
