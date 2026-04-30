<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MydecksController extends Controller
{
    public function show()
    {
        return view('mydecks', compact('decks'));
    }
}
