<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Classroom;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AccountTest extends TestCase
{
    use RefreshDatabase;

    public function test_signup_requires_a_matching_password_confirmation(): void
    {
        $this->post('/signup', [
            'firstname' => 'Ana', 'lastname' => 'Cruz', 'email' => 'ana@example.com',
            'password' => 'password123', 'password_confirmation' => 'different',
        ])->assertSessionHasErrors('password');

        $this->assertDatabaseMissing('users', ['email' => 'ana@example.com']);
    }

    public function test_signup_greets_the_student_by_name(): void
    {
        $this->post('/signup', [
            'firstname' => 'Ana', 'lastname' => 'Cruz', 'email' => 'ana@example.com',
            'password' => 'password123', 'password_confirmation' => 'password123',
        ])->assertSessionHas('status', 'Welcome to QuizGo, Ana!');

        $this->assertSame(Role::Student, User::firstWhere('email', 'ana@example.com')->role);
    }

    public function test_an_admin_creates_a_teacher_without_storing_the_plain_password(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->signIn($admin)->post(route('admin.teachers.store'), [
            'firstname' => 'Tess', 'lastname' => 'Reyes', 'email' => 'tess@example.com', 'password' => 'secret-pass',
        ])->assertSessionHas('new_teacher_credentials');

        $teacher = User::firstWhere('email', 'tess@example.com');
        $this->assertSame(Role::Teacher, $teacher->role);
        $this->assertTrue(Hash::check('secret-pass', $teacher->password));
        $this->assertNotSame('secret-pass', $teacher->getRawOriginal('password'));
    }

    public function test_an_admin_can_only_delete_teachers(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create(['role' => 'student']);

        $this->signIn($admin)->delete(route('admin.teachers.destroy', $student))->assertStatus(400);
        $this->assertNotNull($student->fresh());
    }

    public function test_students_and_teachers_share_one_profile_flow(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);

        $this->signIn($teacher)->put(route('teacher.profile.update'), [
            'firstname' => 'New', 'lastname' => 'Name', 'email' => $teacher->email, 'department' => 'Science',
        ])->assertSessionHasNoErrors();
        $this->assertSame('Science', $teacher->fresh()->department);

        $student = User::factory()->create(['role' => 'student']);

        $this->signIn($student)->put(route('myprofile.update'), [
            'firstname' => 'Stu', 'lastname' => 'Dent', 'email' => $student->email, 'department' => 'Ignored',
        ])->assertSessionHasNoErrors();
        $this->assertNull($student->fresh()->department);
        $this->get(route('myprofile'))->assertOk();
    }

    public function test_deleting_an_account_requires_typing_confirm(): void
    {
        $student = User::factory()->create(['role' => 'student']);

        $this->signIn($student)->delete(route('myprofile.destroy'), ['confirmation' => 'yes'])
            ->assertSessionHasErrors('confirmation');
        $this->assertNotNull($student->fresh());

        $this->delete(route('myprofile.destroy'), ['confirmation' => 'CONFIRM'])->assertRedirect('/');
        $this->assertNull($student->fresh());
    }

    public function test_joining_a_class_twice_shows_an_error(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $student = User::factory()->create(['role' => 'student']);
        Classroom::create(['teacher_id' => $teacher->id, 'name' => 'Bio', 'code' => 'BIO123']);

        $this->signIn($student)->post(route('classroom.join'), ['code' => 'BIO123'])->assertSessionHas('success');
        $this->post(route('classroom.join'), ['code' => 'BIO123'])->assertSessionHasErrors('code');
    }

    public function test_adding_a_teacher_as_a_student_shows_an_error(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $otherTeacher = User::factory()->create(['role' => 'teacher']);
        $classroom = Classroom::create(['teacher_id' => $teacher->id, 'name' => 'Bio', 'code' => 'BIO123']);

        $this->signIn($teacher)
            ->post(route('teacher.classroom.addStudent', $classroom), ['email' => $otherTeacher->email])
            ->assertSessionHasErrors('email');
    }

    public function test_an_expired_database_session_does_not_count_as_signed_in_elsewhere(): void
    {
        config(['session.driver' => 'database']);
        $user = User::factory()->create(['role' => 'student', 'session_token' => 'left-over-token']);
        $auth = app(AuthService::class);

        $this->assertFalse($auth->hasActiveSession($user));

        DB::table('sessions')->insert([
            'id' => 'live-session', 'user_id' => $user->id, 'payload' => '', 'last_activity' => now()->timestamp,
        ]);

        $this->assertTrue($auth->hasActiveSession($user));
    }
}
