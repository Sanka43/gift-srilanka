<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email:rfc,dns',
                'max:255',
                'unique:users,email',
            ],
            'phone' => ['nullable', 'regex:/^(07)[0-9]{8}$/'],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
        ];
    }

    public function messages(): array
    {
        return [
            'email.email' => 'Please enter a valid email address with an active domain.',
            'email.unique' => 'This email address is already registered. Please sign in instead.',
            'phone.regex' => 'Please enter a valid Sri Lankan mobile number (e.g. 07XXXXXXXX).',
        ];
    }
}
