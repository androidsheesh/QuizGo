<?php

namespace App\Policies;

use App\Models\Flashcard;
use App\Models\User;

class FlashcardPolicy
{
    /**
     * Determine if the user can edit or delete the flashcard (they must own its deck).
     */
    public function manage(User $user, Flashcard $flashcard): bool
    {
        return $user->id === $flashcard->deck->user_id;
    }
}
