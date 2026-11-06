<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'title' => ['nullable', 'string', 'max:120'],
            'note_text' => ['nullable', 'string', 'min:80', 'max:30000'],
            'document' => ['nullable', 'file', 'mimes:pdf,txt', 'max:10240'], // max 10MB
            'use_sample' => ['nullable', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'note_text.min' => 'Nota terlalu pendek. Masukkan sekurang-kurangnya 80 aksara.',
            'note_text.max' => 'Nota melebihi had maksimum 30,000 aksara.',
            'document.mimes' => 'Format fail mestilah PDF atau TXT sahaja.',
            'document.max' => 'Saiz fail tidak boleh melebihi 10MB.',
        ];
    }
}
