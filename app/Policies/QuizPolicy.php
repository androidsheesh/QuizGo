<?php

namespace App\Policies;

use App\Models\Quiz;
use App\Models\User;

class QuizPolicy
{
    /**
     * Determine if the user is the teacher who owns the quiz.
     */
    public function manage(User $user, Quiz $quiz): bool
    {
        return $user->id === $quiz->teacher_id;
    }
}
