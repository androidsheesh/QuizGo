<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeleteAccountRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'confirmation' => ['required', 'string', 'in:CONFIRM'],
        ];
    }

    public function messages(): array
    {
        return [
            'confirmation.in' => 'You must type CONFIRM to delete your account.',
        ];
    }
}
