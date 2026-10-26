<?php

namespace App\Http\Controllers;

use App\Http\Requests\GenerateQuizRequest;
use App\Services\QuizGenerationService;
use Illuminate\Support\Facades\Auth;

class TeacherAiQuizController extends Controller
{
    public function __construct(private QuizGenerationService $generationService)
    {
    }

    public function generate(GenerateQuizRequest $request)
    {
        $generation = $this->generationService->generate(
            Auth::id(),
            $request->sourceFile(),
            $request->validated('content'),
            (int) $request->validated('count', 10),
        );

        return redirect()->route('teacher.quiz.index')
            ->with('success', 'Your quiz is being processed in the background. You can move to another page while it finishes.')
            ->with('quiz_generation_id', $generation->uuid);
    }
}
