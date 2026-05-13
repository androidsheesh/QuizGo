<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessAiQuiz;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TeacherAiQuizController extends Controller
{
    public function showUpload()
    {
        return view('teacher.ai-quiz');
    }

    public function generate(Request $request)
    {
        $request->validate([
            'files' => 'nullable|array',
            'files.*' => 'nullable|file|mimes:pdf,txt|max:10240',
            'content' => 'nullable|string',
            'count' => 'nullable|integer|min:1|max:20',
        ]);

        try {
            $latestQuizId = Quiz::where('teacher_id', Auth::id())->latest()->first()?->id ?? 0;
            $count = $request->input('count', 10);
            $uploadedFile = collect($request->file('files', []))->filter()->first();

            if ($uploadedFile && $uploadedFile->getClientOriginalExtension() === 'pdf') {
                $title = str_replace('.pdf', '', $uploadedFile->getClientOriginalName()).' Quiz';
                $sourceType = 'pdf';
                $source = $this->storeUploadedAiFile($uploadedFile);
            } elseif ($uploadedFile && $uploadedFile->getClientOriginalExtension() === 'txt') {
                $title = str_replace('.txt', '', $uploadedFile->getClientOriginalName()).' Quiz';
                $sourceType = 'txt';
                $source = $this->storeUploadedAiFile($uploadedFile);
            } elseif (filled($request->input('content'))) {
                $title = Str::words($request->input('content'), 5, '...').' Quiz';
                $sourceType = 'text';
                $source = $request->input('content');
            } else {
                return back()->withErrors(['ai' => 'Upload a PDF/TXT file or paste content before generating a quiz.'])->withInput();
            }

            ProcessAiQuiz::dispatch(Auth::id(), $sourceType, $source, $count, ucfirst($title));

            return redirect()->route('teacher.quiz.index')
                ->with('success', 'Your quiz is being processed in the background. You can move to another page while it finishes.')
                ->with('waiting_for_quiz', $latestQuizId);
        } catch (\Exception $e) {
            return back()->withErrors(['ai' => $e->getMessage()])->withInput();
        }
    }

    public function generateFromTopic(Request $request)
    {
        $request->validate([
            'topic' => 'required|string|max:255',
            'count' => 'nullable|integer|min:1|max:20',
        ]);

        try {
            $latestQuizId = Quiz::where('teacher_id', Auth::id())->latest()->first()?->id ?? 0;
            $count = $request->input('count', 10);

            ProcessAiQuiz::dispatch(Auth::id(), 'topic', $request->topic, $count, ucfirst($request->topic).' Quiz');

            return redirect()->route('teacher.quiz.index')
                ->with('success', 'Your quiz is being processed in the background. You can move to another page while it finishes.')
                ->with('waiting_for_quiz', $latestQuizId);
        } catch (\Exception $e) {
            return back()->withErrors(['ai' => $e->getMessage()])->withInput();
        }
    }

    public function generateFromText(Request $request)
    {
        $request->validate([
            'text' => 'required|string',
            'count' => 'nullable|integer|min:1|max:20',
        ]);

        try {
            $latestQuizId = Quiz::where('teacher_id', Auth::id())->latest()->first()?->id ?? 0;
            $count = $request->input('count', 10);
            $title = Str::words($request->text, 5, '...').' Quiz';

            ProcessAiQuiz::dispatch(Auth::id(), 'text', $request->text, $count, ucfirst($title));

            return redirect()->route('teacher.quiz.index')
                ->with('success', 'Your quiz is being processed in the background. You can move to another page while it finishes.')
                ->with('waiting_for_quiz', $latestQuizId);
        } catch (\Exception $e) {
            return back()->withErrors(['ai' => $e->getMessage()])->withInput();
        }
    }

    public function generateFromPdf(Request $request)
    {
        $request->validate([
            'pdf' => 'required|file|mimes:pdf|max:10240',
            'count' => 'nullable|integer|min:1|max:20',
        ]);

        try {
            $latestQuizId = Quiz::where('teacher_id', Auth::id())->latest()->first()?->id ?? 0;
            $count = $request->input('count', 10);
            $title = str_replace('.pdf', '', $request->file('pdf')->getClientOriginalName()).' Quiz';
            $path = $this->storeUploadedAiFile($request->file('pdf'));

            ProcessAiQuiz::dispatch(Auth::id(), 'pdf', $path, $count, $title);

            return redirect()->route('teacher.quiz.index')
                ->with('success', 'Your PDF quiz is being processed in the background. You can move to another page while it finishes.')
                ->with('waiting_for_quiz', $latestQuizId);
        } catch (\Exception $e) {
            return back()->withErrors(['ai' => $e->getMessage()])->withInput();
        }
    }

    private function storeUploadedAiFile($file): string
    {
        $fileName = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
        $path = $file->storeAs('ai_quiz_uploads', $fileName, 'local');

        return Storage::disk('local')->path($path);
    }
}
