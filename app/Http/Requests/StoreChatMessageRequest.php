<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreChatMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'body' => ['nullable', 'string', 'max:2000'],
            'guest_name' => ['nullable', 'string', 'max:60'],
            'attachment' => ['nullable', 'file', 'mimetypes:image/jpeg,image/png,image/webp,image/gif,application/pdf', 'max:10240'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $body = trim((string) $this->input('body', ''));

            if ($body === '' && ! $this->hasFile('attachment')) {
                $validator->errors()->add('body', 'Please enter a message or attach an image or PDF.');
            }
        });
    }

    protected function prepareForValidation(): void
    {
        $body = (string) $this->input('body', '');
        $body = strip_tags($body);
        $body = preg_replace("/\r\n?/", "\n", $body) ?? $body;
        $body = preg_replace("/[ \t]+\n/", "\n", $body) ?? $body;
        $body = preg_replace("/\n{3,}/", "\n\n", $body) ?? $body;

        $this->merge([
            'body' => trim($body) ?: null,
            'guest_name' => trim(strip_tags((string) $this->input('guest_name', ''))) ?: null,
        ]);
    }
}
