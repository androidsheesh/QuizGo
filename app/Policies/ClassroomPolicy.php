<?php

namespace App\Policies;

use App\Models\Classroom;
use App\Models\User;

class ClassroomPolicy
{
    /**
     * Determine if the user can manage the classroom.
     */
    public function manage(User $user, Classroom $classroom): bool
    {
        // Only return true if the user is the teacher who created the class
        return $user->id === $classroom->teacher_id && $user->role === 'teacher';
    }
}
