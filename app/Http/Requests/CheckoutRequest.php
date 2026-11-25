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
        $mapDelivery = app(\App\Services\DeliveryFeeService::class)->isEnabled();

        return [
            'delivery_lat' => [$mapDelivery ? 'required' : 'nullable', 'numeric', 'between:-90,90'],
            'delivery_lng' => [$mapDelivery ? 'required' : 'nullable', 'numeric', 'between:-180,180'],
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            // With a map pin the address is just an optional landmark/house-number hint.
            'address' => [$mapDelivery ? 'nullable' : 'required', 'string', 'max:1000'],
            'delivery_date' => ['nullable', 'date', 'after_or_equal:today'],
            'delivery_time' => ['nullable', 'date_format:H:i'],
            'special_note' => ['nullable', 'string', 'max:1000'],
            'district' => $mapDelivery
                ? ['nullable', 'string', 'max:100']
                : ['required', 'string', 'max:100', Rule::in(config('shop.allowed_districts', ['Colombo']))],
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
            'delivery_lat.required' => 'Please pin your delivery location on the map.',
            'delivery_lng.required' => 'Please pin your delivery location on the map.',
            'district.in' => "We currently deliver to {$list} only. Please enter a valid district.",
        ];
    }
}
