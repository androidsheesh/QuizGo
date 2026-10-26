<?php

namespace App\Services;

use RuntimeException;
use thiagoalessio\TesseractOCR\TesseractOCR;

/**
 * Pulls readable text out of a PDF: the embedded text layer when there is one, OCR otherwise.
 */
class PdfTextExtractor
{
    private const MAX_PAGES = 20;

    // Below this, the text layer is missing or junk (e.g. a scanned PDF) and OCR is needed
    private const MIN_TEXT_LAYER_LENGTH = 200;

    /**
     * @param  int  $maxLength  OCR stops once it has this much text, since callers only use that much
     */
    public function extract(string $pdfPath, int $maxLength): string
    {
        // Most PDFs already carry a text layer; reading it takes milliseconds, while OCR takes seconds per page
        $text = $this->extractTextLayer($pdfPath);

        if (strlen($text) >= self::MIN_TEXT_LAYER_LENGTH) {
            return $text;
        }

        return $this->extractWithOcr($pdfPath, $maxLength);
    }

    private function extractTextLayer(string $pdfPath): string
    {
        $cmd = sprintf(
            'pdftotext -l %d -layout %s - 2>&1',
            self::MAX_PAGES,
            escapeshellarg($pdfPath)
        );
        exec($cmd, $output, $returnCode);

        return $returnCode === 0 ? trim(implode("\n", $output)) : '';
    }

    private function extractWithOcr(string $pdfPath, int $maxLength): string
    {
        // Create a temporary directory for the rasterized page images
        $tmpDir = sys_get_temp_dir().'/quizgo_ocr_'.uniqid();
        if (!mkdir($tmpDir, 0700, true)) {
            throw new RuntimeException('Could not create temporary directory for OCR processing.');
        }

        try {
            // Convert the PDF pages to PNG images using pdftoppm (from poppler-utils)
            $prefix = $tmpDir.'/page';
            $cmd = sprintf(
                'pdftoppm -png -r 200 -f 1 -l %d %s %s 2>&1',
                self::MAX_PAGES,
                escapeshellarg($pdfPath),
                escapeshellarg($prefix)
            );
            exec($cmd, $output, $returnCode);

            if ($returnCode !== 0) {
                throw new RuntimeException('Failed to convert PDF to images: '.implode(' ', $output));
            }

            // Gather all generated page images (pdftoppm names them page-1.png, page-2.png, …)
            $images = glob($tmpDir.'/page-*.png');
            if (empty($images)) {
                // Fallback: some versions omit the dash
                $images = glob($tmpDir.'/page*.png');
            }
            sort($images);

            if (empty($images)) {
                throw new RuntimeException('No page images were produced from the PDF.');
            }

            // Run Tesseract OCR on each page image and concatenate the text
            $fullText = '';
            foreach ($images as $imagePath) {
                $pageText = (new TesseractOCR($imagePath))
                    ->lang('eng')
                    ->run();
                $fullText .= "\n".$pageText;

                if (strlen($fullText) >= $maxLength) {
                    break;
                }
            }

            $fullText = trim($fullText);

            if (strlen($fullText) < 50) {
                throw new RuntimeException('Could not extract readable text from this PDF. The file may be empty or protected.');
            }

            return $fullText;
        } finally {
            // Clean up all temporary image files
            $files = glob($tmpDir.'/*');
            if ($files) {
                array_map('unlink', $files);
            }
            @rmdir($tmpDir);
        }
    }
}
