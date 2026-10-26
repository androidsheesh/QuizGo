<?php

namespace App\Enums;

enum Role: string
{
    case Student = 'student';
    case Teacher = 'teacher';
    case Admin = 'admin';
}
