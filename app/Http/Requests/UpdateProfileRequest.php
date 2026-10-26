<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'firstname'       => 'required|string|max:255',
            'lastname'        => 'required|string|max:255',
            'email'           => ['required', 'email', 'max:255', Rule::unique('users')->ignore($this->user()->id)],
            'department'      => 'nullable|string|max:255',
            'bio'             => 'nullable|string|max:1000',
            'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:7168', // 7MB allows phone photos
        ];
    }
}
