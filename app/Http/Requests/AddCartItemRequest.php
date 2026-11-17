<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddCartItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'quantity' => ['nullable', 'integer', 'min:1', 'max:99'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $qty = $this->input('quantity');
        if ($qty === null || $qty === '') {
            $this->merge(['quantity' => 1]);
        }
    }
}
