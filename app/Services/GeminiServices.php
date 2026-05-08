<?php

namespace App\Services;

use Gemini\Data\GenerationConfig;
use Gemini\Enums\ResponseMimeType;
use Gemini\Exceptions\ErrorException;
use Gemini\Exceptions\TransporterException;
use Gemini\Laravel\Facades\Gemini;
use RuntimeException;
use Smalot\PdfParser\Parser as PdfParser;
use Throwable;

class GeminiServices
{
    private string $model;

    public function __construct()
    {
        $this->model = (string) config('gemini.model', 'gemini-2.5-flash-lite');
    }

    private function generateContent(string $prompt): array
    {
        if (blank(config('gemini.api_key'))) {
            throw new RuntimeException('Gemini API key is missing. Add GEMINI_API_KEY to your .env file and clear the config cache.');
        }

        try {
            $result = Gemini::generativeModel(model: $this->model)
                ->withGenerationConfig(new GenerationConfig(
                    maxOutputTokens: (int) config('gemini.max_output_tokens', 2048),
                    temperature: (float) config('gemini.temperature', 0.3),
                    responseMimeType: ResponseMimeType::APPLICATION_JSON,
                ))
                ->generateContent($prompt);
        } catch (ErrorException $exception) {
            throw new RuntimeException($this->friendlyGeminiError($exception), 0, $exception);
        } catch (TransporterException $exception) {
            throw new RuntimeException('Could not connect to Gemini. Check your internet connection and API key configuration.', 0, $exception);
        } catch (Throwable $exception) {
            throw new RuntimeException('Gemini request failed: '.$exception->getMessage(), 0, $exception);
        }

        $text = $this->extractJsonText($result->text());
        $decoded = json_decode($text, true);

        if (! is_array($decoded)) {
            throw new RuntimeException('Gemini returned invalid JSON. Please try again with a smaller prompt.');
        }

        return $decoded;
    }

    private function friendlyGeminiError(ErrorException $exception): string
    {
        $status = $exception->getErrorStatus();
        $code = $exception->getErrorCode();
        $message = $exception->getErrorMessage();

        if ($code === 429 || $status === 'RESOURCE_EXHAUSTED') {
            return 'Gemini free-tier quota or rate limit was reached. Wait a minute and try again, or check the project quota in Google AI Studio. Quota is per project, so creating another API key in the same project does not reset it.';
        }

        if (in_array($status, ['UNAUTHENTICATED', 'PERMISSION_DENIED'], true)) {
            return 'Gemini rejected the API key. Check that GEMINI_API_KEY is valid and belongs to a Google AI Studio project with Gemini API access.';
        }

        if ($status === 'NOT_FOUND') {
            return "Gemini model '{$this->model}' is unavailable. Set GEMINI_MODEL to a current model such as gemini-2.5-flash-lite.";
        }

        return 'Gemini API error: '.$message;
    }

    private function extractJsonText(string $text): string
    {
        $text = preg_replace('/```(?:json)?|```/', '', $text) ?? $text;
        $text = trim($text);

        if (preg_match('/\[.*\]/s', $text, $matches)) {
            return $matches[0];
        }

        return $text;
    }

    private function normalizeCount(int $count): int
    {
        return max(1, min($count, 10));
    }

    private function trimSourceText(string $text): string
    {
        return substr(trim($text), 0, 8000);
    }

    private function extractPdfText(string $pdfPath): string
    {
        $parser = new PdfParser;
        $pdf = $parser->parseFile($pdfPath);
        $text = trim($pdf->getText());

        if (strlen($text) < 50) {
            throw new RuntimeException('Could not extract readable text from this PDF.');
        }

        return $text;
    }

    public function generateFlashcardsFromTopic(string $topic, int $count = 10): array
    {
        $count = $this->normalizeCount($count);

        return $this->generateContent(
            "Generate exactly {$count} flashcards about this topic: {$topic}.
Return only a JSON array. Each item must have exactly these string keys: question, answer."
        );
    }

    public function generateFlashcardsFromText(string $text, int $count = 10): array
    {
        $count = $this->normalizeCount($count);
        $text = $this->trimSourceText($text);

        return $this->generateContent(
            "Based on the following text, generate exactly {$count} flashcards.
Return only a JSON array. Each item must have exactly these string keys: question, answer.
Text:
{$text}"
        );
    }

    public function generateFlashcardsFromPdf(string $pdfPath, int $count = 10): array
    {
        return $this->generateFlashcardsFromText($this->extractPdfText($pdfPath), $count);
    }

    public function generateQuizFromTopic(string $topic, int $count = 10): array
    {
        $count = $this->normalizeCount($count);

        return $this->generateContent(
            "Generate exactly {$count} multiple-choice quiz questions about this topic: {$topic}.
Return only a JSON array. Each item must have exactly these keys:
question as a string, type as the string multiple_choice, choices as an array of exactly 4 strings, correct_answer as one exact value from choices."
        );
    }

    public function generateQuizFromText(string $text, int $count = 10): array
    {
        $count = $this->normalizeCount($count);
        $text = $this->trimSourceText($text);

        return $this->generateContent(
            "Based on the following text, generate exactly {$count} multiple-choice quiz questions.
Return only a JSON array. Each item must have exactly these keys:
question as a string, type as the string multiple_choice, choices as an array of exactly 4 strings, correct_answer as one exact value from choices.
Text:
{$text}"
        );
    }

    public function generateQuizFromPdf(string $pdfPath, int $count = 10): array
    {
        return $this->generateQuizFromText($this->extractPdfText($pdfPath), $count);
    }
}
