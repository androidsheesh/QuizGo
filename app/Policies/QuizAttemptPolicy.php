<?php

namespace App\Policies;

use App\Models\QuizAttempt;
use App\Models\User;

class QuizAttemptPolicy
{
    /**
     * Determine if the user is the student who made the attempt.
     */
    public function view(User $user, QuizAttempt $attempt): bool
    {
        return $user->id === $attempt->student_id;
    }
}
