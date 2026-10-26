<?php

namespace App\Policies;

use App\Models\Deck;
use App\Models\User;

class DeckPolicy
{
    /**
     * Determine if the user can view, edit, study or delete the deck.
     */
    public function manage(User $user, Deck $deck): bool
    {
        return $user->id === $deck->user_id;
    }
}
