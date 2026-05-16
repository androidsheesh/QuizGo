<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('user.{userId}', function ($user, int $userId) {
    return (int) $user->id === $userId;
});

Broadcast::channel('teacher.{teacherId}', function ($user, int $teacherId) {
    return $user->isTeacher() && (int) $user->id === $teacherId;
});
