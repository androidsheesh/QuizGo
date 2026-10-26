<?php

namespace App\Services;

use App\Enums\Role;
use App\Exceptions\ActionFailedException;
use App\Models\Classroom;
use App\Models\User;
use Illuminate\Support\Str;

class TeacherClassroomService
{
    /**
     * Generate a unique code and create the classroom.
     */
    public function createClassroom(int $teacherId, array $data): Classroom
    {
        do {
            $code = strtoupper(Str::random(6));
        } while (Classroom::where('code', $code)->exists());

        return Classroom::create([
            'teacher_id'  => $teacherId,
            'name'        => $data['name'],
            'code'        => $code,
            'description' => $data['description'] ?? null,
        ]);
    }

    /**
     * Add a student to a classroom via their email.
     *
     * @throws ActionFailedException when there's no such student or they're already in the class
     */
    public function addStudentByEmail(Classroom $classroom, string $email): User
    {
        $student = User::firstWhere([
            'email' => $email,
            'role'  => Role::Student->value,
        ]);

        if (!$student) {
            throw new ActionFailedException('No student found with that email.', 'email');
        }

        if ($classroom->students()->where('student_id', $student->id)->exists()) {
            throw new ActionFailedException('This student is already in this class.', 'email');
        }

        $classroom->students()->attach($student->id);

        return $student;
    }

    /**
     * Remove a student from a classroom.
     */
    public function removeStudent(Classroom $classroom, User $user): void
    {
        $classroom->students()->detach($user->id);
    }

    /**
     * Delete a classroom.
     */
    public function deleteClassroom(Classroom $classroom): void
    {
        $classroom->delete();
    }
}
