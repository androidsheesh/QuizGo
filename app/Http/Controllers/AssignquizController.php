<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AssignquizController extends Controller
{
    public function show()
    {
        return view('teacher.assign-quiz');
    }
}
