<?php

namespace App\Models;

use App\Enums\Role;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['firstname', 'lastname', 'email', 'password', 'bio', 'profile_picture', 'role', 'department', 'session_token'])]
#[Hidden(['password', 'remember_token', 'session_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Matches the column default, so a freshly created user has a role before it's reloaded.
     */
    protected $attributes = [
        'role' => 'student',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'role' => Role::class,
        ];
    }

    public function hasRole(Role ...$roles): bool
    {
        return in_array($this->role, $roles, true);
    }

    /**
     * Check if the user is a teacher.
     */
    public function isTeacher(): bool
    {
        return $this->role === Role::Teacher;
    }

    /**
     * Check if the user is a student.
     */
    public function isStudent(): bool
    {
        return $this->role === Role::Student;
    }

    public function isAdmin(): bool
    {
        return $this->role === Role::Admin;
    }

    /**
     * Get the decks that belong to the user.
     */
    public function decks()
    {
        return $this->hasMany(Deck::class);
    }

    /**
     * Classrooms this teacher owns.
     */
    public function classroomsAsTeacher()
    {
        return $this->hasMany(Classroom::class, 'teacher_id');
    }

    /**
     * Classrooms this student is enrolled in.
     */
    public function enrolledClassrooms()
    {
        return $this->belongsToMany(Classroom::class, 'classroom_student', 'student_id', 'classroom_id')
                    ->withTimestamps();
    }

    /**
     * Quizzes created by this teacher.
     */
    public function quizzes()
    {
        return $this->hasMany(Quiz::class, 'teacher_id');
    }

    /**
     * Quiz attempts made by this student.
     */
    public function quizAttempts()
    {
        return $this->hasMany(QuizAttempt::class, 'student_id');
    }
}
