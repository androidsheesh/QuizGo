<?php

namespace App\Services;

use App\Exceptions\ActionFailedException;
use App\Models\Classroom;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class StudentClassroomService
{
    /**
     * Get paginated classrooms with optional search filtering.
     */
    public function getEnrolledClassrooms(User $student, ?string $search): LengthAwarePaginator
    {
        $query = $student->enrolledClassrooms()
            ->withCount('quizAssignments')
            ->with('teacher');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('code', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%')
                  ->orWhereHas('teacher', function($q2) use ($search) {
                      $q2->where('firstname', 'like', '%' . $search . '%')
                         ->orWhere('lastname', 'like', '%' . $search . '%');
                  });
            });
        }

        return $query->paginate(6)->appends(['search' => $search]);
    }

    /**
     * Sort a student's assignments into pending and completed lists.
     */
    public function getStudentAssignments(Classroom $classroom, User $student): array
    {
        $classroom->load([
            'teacher',
            'quizAssignments.quiz' => fn ($q) => $q->withCount('questions'),
            'quizAssignments.attempts' => function($q) use ($student) {
                $q->where('student_id', $student->id);
            }
        ]);

        $pendingQuizzes = collect();
        $completedQuizzes = collect();

        foreach ($classroom->quizAssignments as $assignment) {
            $attempt = $assignment->attempts->first();

            if ($attempt) {
                $completedQuizzes->push(['assignment' => $assignment, 'attempt' => $attempt]);
            } else {
                $pendingQuizzes->push($assignment);
            }
        }

        return [$pendingQuizzes, $completedQuizzes];
    }

    /**
     * Enroll a student in a class using its code.
     *
     * @throws ActionFailedException when the student is already enrolled
     */
    public function enrollByCode(User $student, string $code): Classroom
    {
        $classroom = Classroom::where('code', $code)->firstOrFail();

        if ($classroom->students()->where('student_id', $student->id)->exists()) {
            throw new ActionFailedException('You are already enrolled in this class.', 'code');
        }

        $classroom->students()->attach($student->id);

        return $classroom;
    }
}
