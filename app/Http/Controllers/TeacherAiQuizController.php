<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TeacherAiQuizController extends Controller
{
    /**
     * Show the AI quiz generation upload page.
     * Front-end only — no AI logic implemented yet.
     */
    public function showUpload()
    {
        return view('teacher.ai-quiz');
    }

    /**
     * Placeholder for AI quiz generation.
     * Returns a "Coming Soon" message.
     */
    public function generate(Request $request)
    {
        // Validate uploaded files exist (front-end validation)
        $request->validate([
            'files'   => 'nullable|array',
            'files.*' => 'nullable|file|max:10240', // 10MB max per file
            'content' => 'nullable|string',
        ]);

        return back()->with('info', '🤖 AI Quiz Generation is coming soon! Stay tuned.');
    }
}
