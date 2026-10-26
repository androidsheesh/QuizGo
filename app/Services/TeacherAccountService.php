<?php

namespace App\Services;

use App\Enums\Role;
use App\Models\User;

class TeacherAccountService
{
    /**
     * Create a teacher account. The plain password is only used here to hash it, never stored.
     */
    public function create(array $data): User
    {
        return User::create([
            'firstname' => $data['firstname'],
            'lastname'  => $data['lastname'],
            'email'     => $data['email'],
            'password'  => $data['password'],
            'role'      => Role::Teacher,
        ]);
    }
}
