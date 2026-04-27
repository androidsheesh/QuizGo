<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TeacherdashboardController extends Controller
{
    public function show()
    {
        return view('teacher.teacher-dashboard');
    }
}
