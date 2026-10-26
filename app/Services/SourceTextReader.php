<?php

namespace App\Services;

/**
 * Turns a generation source into the text sent to Gemini. "topic" and "text" sources are the
 * text itself; "pdf" and "txt" sources are paths on the uploads disk.
 */
class SourceTextReader
{
    public function __construct(
        private UploadStorage $uploads,
        private PdfTextExtractor $pdfExtractor,
    ) {
    }

    public function read(string $sourceType, string $source): string
    {
        return match ($sourceType) {
            'pdf' => $this->uploads->withLocalPath(
                $source,
                fn (string $path) => $this->pdfExtractor->extract($path, GeminiService::MAX_SOURCE_TEXT_LENGTH)
            ),
            'txt' => $this->uploads->withLocalPath($source, fn (string $path) => file_get_contents($path) ?: ''),
            default => $source,
        };
    }

    /**
     * Delete the uploaded file behind a source, if there is one.
     */
    public function discard(string $sourceType, string $source): void
    {
        if (in_array($sourceType, ['pdf', 'txt'], true)) {
            $this->uploads->delete($source);
        }
    }
}
