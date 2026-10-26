<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Pages load assets through @vite, which needs a built manifest; tests don't build assets
        $this->withoutVite();
    }

    /**
     * Act as $user with a matching session token, so the session.token middleware lets requests through.
     */
    protected function signIn(User $user): static
    {
        $user->forceFill(['session_token' => 'test-session-token'])->save();

        return $this->actingAs($user)->withSession(['session_token' => 'test-session-token']);
    }
}
