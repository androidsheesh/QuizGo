<?php

namespace Tests\Feature;

use App\Events\FlashcardGenerationFinished;
use App\Events\TeacherNotificationReceived;
use App\Jobs\GenerateFlashcards;
use App\Jobs\GenerateQuiz;
use App\Models\Generation;
use App\Models\Quiz;
use App\Models\User;
use App\Services\GeminiService;
use App\Services\SourceTextReader;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Tests\TestCase;

class GenerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_teacher_ai_quiz_request_is_tracked_and_queued(): void
    {
        Queue::fake();
        $teacher = User::factory()->create(['role' => 'teacher']);

        $this->signIn($teacher)
            ->post(route('teacher.quiz.ai.generate'), ['content' => 'Cells are the basic unit of life.', 'count' => 5])
            ->assertRedirect(route('teacher.quiz.index'))
            ->assertSessionHas('quiz_generation_id');

        $generation = Generation::sole();
        $this->assertSame(Generation::KIND_QUIZ, $generation->kind);
        $this->assertSame(Generation::STATUS_PENDING, $generation->status);
        Queue::assertPushed(GenerateQuiz::class, fn ($job) => $job->generationId === $generation->id && $job->sourceType === 'text');
    }

    public function test_an_uploaded_file_is_queued_by_its_disk_path(): void
    {
        Queue::fake();
        Storage::fake('local');
        $teacher = User::factory()->create(['role' => 'teacher']);

        $this->signIn($teacher)->post(route('teacher.quiz.ai.generate'), [
            'files' => [UploadedFile::fake()->create('cells.pdf', 10, 'application/pdf')],
        ]);

        Queue::assertPushed(GenerateQuiz::class, function ($job) {
            return $job->sourceType === 'pdf'
                && str_starts_with($job->source, 'ai_quiz_uploads/')
                && $job->title === 'cells Quiz';
        });
    }

    public function test_an_ai_quiz_request_needs_a_file_or_text(): void
    {
        Queue::fake();

        $this->signIn(User::factory()->create(['role' => 'teacher']))
            ->post(route('teacher.quiz.ai.generate'), [])
            ->assertSessionHasErrors('ai');

        Queue::assertNothingPushed();
        $this->assertSame(0, Generation::count());
    }

    public function test_a_student_flashcard_request_is_tracked_and_queued(): void
    {
        Queue::fake();
        $student = User::factory()->create(['role' => 'student']);

        $this->signIn($student)->post(route('generate.topic'), ['topic' => 'Photosynthesis']);

        $generation = Generation::sole();
        $this->assertSame(Generation::KIND_FLASHCARDS, $generation->kind);
        Queue::assertPushed(GenerateFlashcards::class, fn ($job) => $job->source === 'photosynthesis' && $job->title === 'Photosynthesis');
    }

    public function test_the_quiz_job_creates_the_quiz_and_completes_the_generation(): void
    {
        Event::fake([TeacherNotificationReceived::class]);
        $teacher = User::factory()->create(['role' => 'teacher']);
        $generation = Generation::create(['kind' => Generation::KIND_QUIZ, 'user_id' => $teacher->id, 'source_type' => 'text', 'status' => 'pending']);

        $this->mock(GeminiService::class, function ($mock) {
            $mock->shouldReceive('generateQuizFromText')->andReturn([
                ['question' => 'Powerhouse?', 'type' => 'multiple_choice', 'choices' => ['Nucleus', 'Mitochondria'], 'correct_answer' => 'Mitochondria'],
            ]);
            $mock->shouldReceive('normalizeQuizQuestions')->andReturnUsing(fn ($questions) => $questions);
        });

        (new GenerateQuiz($generation->id, 'text', 'Cells text', 1, 'Cells Quiz'))->handle(app(SourceTextReader::class));

        $quiz = Quiz::sole();
        $this->assertSame('Cells Quiz', $quiz->title);
        $this->assertSame(1, $quiz->questions()->count());
        $this->assertSame(Generation::STATUS_COMPLETED, $generation->fresh()->status);
        $this->assertSame($quiz->id, $generation->fresh()->quiz_id);
        Event::assertDispatched(TeacherNotificationReceived::class, fn ($event) => $event->actionUrl === route('teacher.quiz.show', $quiz));
    }

    public function test_a_failed_job_marks_the_generation_failed_and_deletes_the_upload(): void
    {
        Event::fake([FlashcardGenerationFinished::class]);
        Storage::fake('local');
        Storage::disk('local')->put('pdf_uploads/notes.pdf', 'not really a pdf');
        $student = User::factory()->create(['role' => 'student']);
        $generation = Generation::create(['kind' => Generation::KIND_FLASHCARDS, 'user_id' => $student->id, 'source_type' => 'pdf', 'status' => 'pending']);

        $this->mock(SourceTextReader::class, function ($mock) {
            $mock->shouldReceive('read')->andThrow(new RuntimeException('Could not read PDF'));
            $mock->shouldReceive('discard')->once()->andReturnUsing(fn () => Storage::disk('local')->delete('pdf_uploads/notes.pdf'));
        });

        $job = new GenerateFlashcards($generation->id, 'pdf', 'pdf_uploads/notes.pdf', 5, 'notes');

        try {
            $job->handle(app(SourceTextReader::class));
            $this->fail('The job should rethrow so the queue marks it failed.');
        } catch (RuntimeException) {
            $job->failed(new RuntimeException('Could not read PDF'));
        }

        $this->assertSame(Generation::STATUS_FAILED, $generation->fresh()->status);
        Storage::disk('local')->assertMissing('pdf_uploads/notes.pdf');
        Event::assertDispatched(FlashcardGenerationFinished::class);
    }

    public function test_the_status_endpoint_reports_progress_only_to_its_owner(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $generation = Generation::create(['kind' => Generation::KIND_QUIZ, 'user_id' => $teacher->id, 'source_type' => 'text', 'status' => 'pending']);

        $this->signIn($teacher)
            ->getJson(route('generations.status', $generation->uuid))
            ->assertOk()
            ->assertJson(['status' => 'pending', 'kind' => 'quiz', 'result_url' => null]);

        $this->signIn(User::factory()->create(['role' => 'teacher']))
            ->getJson(route('generations.status', $generation->uuid))
            ->assertNotFound();
    }
}
