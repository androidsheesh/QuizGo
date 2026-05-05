<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TeacherSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'teachers@quizgo.com'],
            [
                'firstname'  => 'Teacher',
                'lastname'   => 'Admin',
                'email'      => 'teachers@quizgo.com',
                'password'   => Hash::make('password123'),
                'role'       => 'teacher',
                'department' => 'Computer Science',
            ],
        );

        User::updateOrCreate(
            ['email' => 'chrishiandegaom@quizgo.com'],
            [
                'firstname'  => 'Chrishian',
                'lastname'   => 'Degaom',
                'email'      => 'chrishiandegaom@quizgo.com',
                'password'   => Hash::make('password123'),
                'role'       => 'teacher',
                'department' => 'Computer Science',
            ]
        );
    }
}
