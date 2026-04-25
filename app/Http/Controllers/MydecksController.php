<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MydecksController extends Controller
{
    public function show()
    {
        // We create a "static" collection of objects to mimic database records.
        // this is just a dummy data
        // represenation only
        $decks = collect([
            (object) [
                'id' => 1,
                'name' => 'Laravel Framework',
                'cards_count' => 121,
                'color_class' => 'bg-red-500'
            ],
            (object) [
                'id' => 2,
                'name' => 'UI/UX Design',
                'cards_count' => 45,
                'color_class' => 'bg-blue-500'
            ],
            (object) [
                'id' => 3,
                'name' => 'Untitled Deck',
                'cards_count' => 12,
                'color_class' => 'bg-yellow-400'
            ],
            (object) [
                'id' => 4,
                'name' => 'React Basics',
                'cards_count' => 89,
                'color_class' => 'bg-emerald-500'
            ],
        ]);

        return view('mydecks', compact('decks'));
    }
}
