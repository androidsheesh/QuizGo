<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SigninTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_student_can_sign_in(): void
    {
        $user = User::factory()->create(['role' => 'student']);

        $this->post('/signin', ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect(url('home'));

        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user->fresh()->session_token);
    }

    public function test_sign_in_is_throttled_after_five_failed_attempts_for_the_same_email(): void
    {
        $user = User::factory()->create(['role' => 'student']);

        for ($i = 0; $i < 5; $i++) {
            $this->post('/signin', ['email' => $user->email, 'password' => 'wrong'])->assertRedirect();
        }

        $this->post('/signin', ['email' => $user->email, 'password' => 'wrong'])->assertStatus(429);
    }

    public function test_one_students_failed_attempts_do_not_lock_out_classmates_on_the_same_ip(): void
    {
        $user = User::factory()->create(['role' => 'student']);
        $classmate = User::factory()->create(['role' => 'student']);

        for ($i = 0; $i < 6; $i++) {
            $this->post('/signin', ['email' => $user->email, 'password' => 'wrong']);
        }

        $this->post('/signin', ['email' => $classmate->email, 'password' => 'password'])
            ->assertRedirect(url('home'));
    }

    public function test_teacher_sign_in_is_throttled(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);

        for ($i = 0; $i < 5; $i++) {
            $this->post('/teacher/signin', ['email' => $teacher->email, 'password' => 'wrong']);
        }

        $this->post('/teacher/signin', ['email' => $teacher->email, 'password' => 'wrong'])->assertStatus(429);
    }

    public function test_confirming_another_device_logs_in_within_five_minutes(): void
    {
        $user = $this->userSignedInElsewhere();

        $this->post('/signin', ['email' => $user->email, 'password' => 'password'])
            ->assertSessionHas('confirm_other_device');
        $this->assertGuest();

        $this->post('/signin', ['email' => $user->email, 'confirm_other_device' => 1])
            ->assertRedirect(url('home'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_an_abandoned_device_confirmation_expires(): void
    {
        $user = $this->userSignedInElsewhere();

        $this->post('/signin', ['email' => $user->email, 'password' => 'password']);

        $this->travel(6)->minutes();

        $this->post('/signin', ['email' => $user->email, 'confirm_other_device' => 1])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_reloading_the_sign_in_page_cancels_a_pending_confirmation(): void
    {
        $user = $this->userSignedInElsewhere();

        $this->post('/signin', ['email' => $user->email, 'password' => 'password']);
        $this->get('/signin'); // flash is consumed here, as when the prompt is shown
        $this->get('/signin'); // a later visit to the sign-in page

        $this->post('/signin', ['email' => $user->email, 'confirm_other_device' => 1])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_confirmation_without_a_password_check_first_is_rejected(): void
    {
        $user = $this->userSignedInElsewhere();

        $this->post('/signin', ['email' => $user->email, 'confirm_other_device' => 1])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_the_leftover_breeze_routes_are_gone(): void
    {
        $this->get('/register')->assertNotFound();
        $this->get('/login')->assertNotFound();
    }

    public function test_guests_are_sent_to_the_matching_sign_in_page(): void
    {
        $this->get('/home')->assertRedirect(route('signin'));
        $this->get('/teacher/quizzes')->assertRedirect(route('teacher.signin'));
    }

    private function userSignedInElsewhere(): User
    {
        return User::factory()->create(['role' => 'student', 'session_token' => 'token-from-another-device']);
    }
}
