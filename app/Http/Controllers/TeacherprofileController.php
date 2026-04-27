<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TeacherprofileController extends Controller
{
    public function show()
    {
        return view('teacher.teacher-profile');
    }
}
