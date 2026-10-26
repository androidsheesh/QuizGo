<?php

namespace Tests\Feature;

use App\Models\Classroom;
use App\Models\Deck;
use App\Models\Quiz;
use App\Models\QuizAssignment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_students_cannot_open_teacher_or_admin_pages(): void
    {
        $student = User::factory()->create(['role' => 'student']);

        $this->signIn($student)->get(route('teacher.dashboard'))->assertForbidden();
        $this->get(route('teacher.quiz.index'))->assertForbidden();
        $this->get(route('admin.dashboard'))->assertForbidden();
    }

    public function test_teachers_cannot_open_student_or_admin_pages(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);

        $this->signIn($teacher)->get(route('teacher.dashboard'))->assertOk();
        $this->get(route('home'))->assertForbidden();
        $this->get(route('admin.dashboard'))->assertForbidden();
    }

    public function test_admins_can_open_the_admin_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->signIn($admin)->get(route('admin.dashboard'))->assertOk();
    }

    public function test_a_student_cannot_touch_another_students_deck_or_cards(): void
    {
        $owner = User::factory()->create(['role' => 'student']);
        $other = User::factory()->create(['role' => 'student']);
        $deck = Deck::create(['user_id' => $owner->id, 'title' => 'Mine']);
        $card = $deck->flashcards()->create(['question' => 'Q', 'answer' => 'A']);

        $this->signIn($other);
        $this->get(route('decks.show', $deck))->assertForbidden();
        $this->get(route('study.flipcards', $deck))->assertForbidden();
        $this->delete(route('decks.destroy', $deck))->assertForbidden();
        $this->put(route('flashcards.update', $card), ['question' => 'X', 'answer' => 'Y'])->assertForbidden();
        $this->delete(route('flashcards.destroy', $card))->assertForbidden();

        $this->assertDatabaseHas('flashcards', ['id' => $card->id, 'question' => 'Q']);
    }

    public function test_a_teacher_cannot_manage_another_teachers_quiz_or_classroom(): void
    {
        $owner = User::factory()->create(['role' => 'teacher']);
        $other = User::factory()->create(['role' => 'teacher']);
        $quiz = Quiz::create(['teacher_id' => $owner->id, 'title' => 'Cells', 'is_active' => true]);
        $classroom = Classroom::create(['teacher_id' => $owner->id, 'name' => 'Bio', 'code' => 'BIO123']);
        $assignment = QuizAssignment::create(['quiz_id' => $quiz->id, 'classroom_id' => $classroom->id]);
        $otherClassroom = Classroom::create(['teacher_id' => $other->id, 'name' => 'Chem', 'code' => 'CHE123']);

        $this->signIn($other);
        $this->get(route('teacher.quiz.show', $quiz))->assertForbidden();
        $this->patch(route('teacher.quiz.toggle', $quiz))->assertForbidden();
        $this->delete(route('teacher.quiz.destroy', $quiz))->assertForbidden();
        $this->delete(route('teacher.quiz.unassign', $assignment))->assertForbidden();
        $this->get(route('teacher.classroom.show', $classroom))->assertForbidden();
        $this->post(route('teacher.quiz.assign'), ['quiz_id' => $quiz->id, 'classroom_id' => $otherClassroom->id])->assertForbidden();

        $this->assertDatabaseCount('quiz_assignments', 1);
    }

    public function test_a_student_cannot_see_a_classroom_they_are_not_in(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $classroom = Classroom::create(['teacher_id' => $teacher->id, 'name' => 'Bio', 'code' => 'BIO123']);

        $this->signIn(User::factory()->create(['role' => 'student']))
            ->get(route('student.classroom.show', $classroom))
            ->assertForbidden();
    }
}
