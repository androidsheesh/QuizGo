<?php

namespace App\Policies;

use App\Models\QuizAssignment;
use App\Models\User;

class QuizAssignmentPolicy
{
    /**
     * Determine if the user is the teacher who owns the assigned quiz.
     */
    public function manage(User $user, QuizAssignment $assignment): bool
    {
        return $user->id === $assignment->quiz->teacher_id;
    }

    /**
     * Determine if the student is enrolled in the class the quiz is assigned to.
     */
    public function take(User $user, QuizAssignment $assignment): bool
    {
        return $assignment->classroom->students()->where('student_id', $user->id)->exists();
    }
}
