<?php

namespace App\Services;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Stores uploads that a queued job will process later. Jobs receive the disk-relative
 * path, never an absolute one, so the worker can run in a different container.
 */
class UploadStorage
{
    public function store(UploadedFile $file, string $directory): string
    {
        $fileName = time().'_'.Str::random(8).'_'.str_replace(' ', '_', $file->getClientOriginalName());

        return $file->storeAs($directory, $fileName, $this->diskName());
    }

    public function exists(string $path): bool
    {
        return $this->disk()->exists($path);
    }

    public function delete(string $path): void
    {
        $this->disk()->delete($path);
    }

    /**
     * Run $callback with an absolute local path to the file. Tools like pdftoppm and
     * Tesseract need a real file, so files on a remote disk are copied to a temp file first.
     */
    public function withLocalPath(string $path, callable $callback): mixed
    {
        if (! $this->exists($path)) {
            throw new RuntimeException('Uploaded file was not found.');
        }

        if (config("filesystems.disks.{$this->diskName()}.driver") === 'local') {
            return $callback($this->disk()->path($path));
        }

        $tempPath = tempnam(sys_get_temp_dir(), 'quizgo_upload_');

        try {
            file_put_contents($tempPath, $this->disk()->get($path));

            return $callback($tempPath);
        } finally {
            @unlink($tempPath);
        }
    }

    private function diskName(): string
    {
        return config('filesystems.uploads_disk', 'local');
    }

    private function disk(): Filesystem
    {
        return Storage::disk($this->diskName());
    }
}
