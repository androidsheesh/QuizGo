<?php

namespace App\Services;

use App\Exceptions\ActionFailedException;
use App\Jobs\GenerateQuiz;
use App\Models\Generation;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class QuizGenerationService
{
    public function __construct(private UploadStorage $uploads)
    {
    }

    /**
     * Queue an AI quiz built from an uploaded PDF/TXT file, or else from pasted text.
     *
     * @throws ActionFailedException when there is neither a file nor text
     */
    public function generate(int $teacherId, ?UploadedFile $file, ?string $content, int $count): Generation
    {
        if ($file) {
            $sourceType = strtolower($file->getClientOriginalExtension()) === 'pdf' ? 'pdf' : 'txt';
            $title = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME).' Quiz';
        } elseif (filled($content)) {
            $sourceType = 'text';
            $title = ucfirst(Str::words($content, 5, '...')).' Quiz';
        } else {
            throw new ActionFailedException('Upload a PDF/TXT file or paste content before generating a quiz.', 'ai');
        }

        $generation = Generation::create([
            'kind' => Generation::KIND_QUIZ,
            'user_id' => $teacherId,
            'source_type' => $sourceType,
            'status' => Generation::STATUS_PENDING,
        ]);

        $source = $file ? $this->uploads->store($file, 'ai_quiz_uploads') : $content;

        GenerateQuiz::dispatch($generation->id, $sourceType, $source, $count, $title);

        return $generation;
    }
}
