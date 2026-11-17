<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string'],
            'delivery_date' => ['nullable', 'date', 'after_or_equal:today'],
            'delivery_time' => ['nullable', 'date_format:H:i'],
            'special_note' => ['nullable', 'string', 'max:1000'],
            'district' => ['required', 'string', 'max:100', Rule::in(config('shop.allowed_districts', ['Colombo']))],
            'payment_method' => ['required', 'in:cash_on_delivery,online'],
            'checkout_token' => [
                'required',
                'string',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if ($value === '' || $value !== session('checkout_token')) {
                        $fail('This form has expired. Please refresh the checkout page and try again.');
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        $districts = config('shop.allowed_districts', ['Colombo']);
        $list = implode(', ', $districts);

        return [
            'district.in' => "We currently deliver to {$list} only. Please enter a valid district.",
        ];
    }
}
