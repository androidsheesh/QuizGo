<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;

class GenerateQuizRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'files'   => 'nullable|array',
            'files.*' => 'nullable|file|mimes:pdf,txt|max:10240',
            'content' => 'nullable|string|max:50000',
            'count'   => 'nullable|integer|min:1|max:20',
        ];
    }

    /**
     * The form allows several files, but a quiz is generated from the first one.
     */
    public function sourceFile(): ?UploadedFile
    {
        return collect($this->file('files', []))->filter()->first();
    }
}
