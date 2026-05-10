<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Services\GeminiServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TeacherAiQuizController extends Controller
{
    public function showUpload()
    {
        return view('teacher.ai-quiz');
    }

    private function saveQuizAndQuestions($title, $questionsData)
    {
        $quiz = Quiz::create([
            'teacher_id' => Auth::id(),
            'title' => $title,
            'description' => 'AI Generated Quiz',
            'is_active' => true,
        ]);

        foreach ($questionsData as $index => $q) {
            QuizQuestion::create([
                'quiz_id' => $quiz->id,
                'type' => $q['type'] ?? 'multiple_choice',
                'question' => $q['question'] ?? 'No Question',
                'correct_answer' => $q['correct_answer'] ?? '',
                'choices' => $q['choices'] ?? [],
                'order' => $index,
            ]);
        }

        return $quiz;
    }

    public function generate(Request $request, GeminiServices $gemini)
    {
        $request->validate([
            'files' => 'nullable|array',
            'files.*' => 'nullable|file|mimes:pdf,txt|max:10240',
            'content' => 'nullable|string',
            'count' => 'nullable|integer|min:1|max:20',
        ]);

        try {
            $count = $request->input('count', 10);
            $uploadedFile = collect($request->file('files', []))->filter()->first();

            if ($uploadedFile && $uploadedFile->getClientOriginalExtension() === 'pdf') {
                $questions = $gemini->generateQuizFromPdf($uploadedFile->getPathname(), $count);
                $title = str_replace('.pdf', '', $uploadedFile->getClientOriginalName()).' Quiz';
            } elseif ($uploadedFile && $uploadedFile->getClientOriginalExtension() === 'txt') {
                $questions = $gemini->generateQuizFromText(file_get_contents($uploadedFile->getPathname()) ?: '', $count);
                $title = str_replace('.txt', '', $uploadedFile->getClientOriginalName()).' Quiz';
            } elseif (filled($request->input('content'))) {
                $questions = $gemini->generateQuizFromText($request->input('content'), $count);
                $title = Str::words($request->input('content'), 5, '...').' Quiz';
            } else {
                return back()->withErrors(['ai' => 'Upload a PDF/TXT file or paste content before generating a quiz.'])->withInput();
            }

            $quiz = $this->saveQuizAndQuestions(ucfirst($title), $questions);

            return redirect()->route('teacher.quiz.show', $quiz)->with('success', 'AI Quiz generated successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['ai' => $e->getMessage()])->withInput();
        }
    }

    public function generateFromTopic(Request $request, GeminiServices $gemini)
    {
        $request->validate([
            'topic' => 'required|string|max:255',
            'count' => 'nullable|integer|min:1|max:20',
        ]);

        try {
            $count = $request->input('count', 10);
            $questions = $gemini->generateQuizFromTopic($request->topic, $count);
            $quiz = $this->saveQuizAndQuestions(ucfirst($request->topic).' Quiz', $questions);

            return redirect()->route('teacher.quiz.show', $quiz)->with('success', 'AI Quiz generated successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['ai' => $e->getMessage()])->withInput();
        }
    }

    public function generateFromText(Request $request, GeminiServices $gemini)
    {
        $request->validate([
            'text' => 'required|string',
            'count' => 'nullable|integer|min:1|max:20',
        ]);

        try {
            $count = $request->input('count', 10);
            $questions = $gemini->generateQuizFromText($request->text, $count);

            $title = Str::words($request->text, 5, '...').' Quiz';
            $quiz = $this->saveQuizAndQuestions(ucfirst($title), $questions);

            return redirect()->route('teacher.quiz.show', $quiz)->with('success', 'AI Quiz generated successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['ai' => $e->getMessage()])->withInput();
        }
    }

    public function generateFromPdf(Request $request, GeminiServices $gemini)
    {
        $request->validate([
            'pdf' => 'required|file|mimes:pdf|max:10240',
            'count' => 'nullable|integer|min:1|max:20',
        ]);

        try {
            $count = $request->input('count', 10);
            $path = $request->file('pdf')->getPathname();
            $questions = $gemini->generateQuizFromPdf($path, $count);

            $title = str_replace('.pdf', '', $request->file('pdf')->getClientOriginalName()).' Quiz';
            $quiz = $this->saveQuizAndQuestions($title, $questions);

            return redirect()->route('teacher.quiz.show', $quiz)->with('success', 'AI Quiz generated successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['ai' => $e->getMessage()])->withInput();
        }
    }
}
