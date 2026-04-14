<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Checkout Validation Request
 * 
 * Comprehensive validation for order checkout including shipping,
 * payment method, and custom business logic validation.
 */
class CheckoutRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * 
     * Must be authenticated user.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'shipping_address' => [
                'required',
                'string',
                'min:10',
                'max:255',
                'regex:/^[a-z0-9\s,.\'-]*$/i',
            ],
            'shipping_city' => [
                'required',
                'string',
                'min:2',
                'max:100',
                'regex:/^[a-z\s\'-]*$/i',
            ],
            'shipping_postal_code' => [
                'required',
                'regex:/^[0-9]{5,10}$/i',
            ],
            'shipping_country' => [
                'required',
                'string',
                'in:lk', // Sri Lanka context - can be expanded
            ],
            'shipping_phone' => [
                'required',
                'phone_number',
                'max:20',
            ],
            'payment_method' => [
                'required',
                'string',
                Rule::in(['stripe', 'payhere', 'bank_transfer']),
            ],
            'payment_intent_id' => [
                'nullable',
                'string',
                'required_if:payment_method,stripe',
            ],
            'coupon_code' => [
                'nullable',
                'string',
                'max:50',
                'regex:/^[A-Z0-9]{3,20}$/',
            ],
            'notes' => [
                'nullable',
                'string',
                'max:500',
                'regex:/^[a-z0-9\s,.\'-]*$/i',
            ],
            'terms_agreed' => [
                'required',
                'accepted',
            ],
        ];
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'shipping_address.required' => 'Shipping address is required',
            'shipping_address.min' => 'Shipping address must be at least 10 characters',
            'shipping_address.regex' => 'Shipping address contains invalid characters',
            'shipping_city.required' => 'City is required',
            'shipping_city.regex' => 'City name contains invalid characters',
            'shipping_postal_code.required' => 'Postal code is required',
            'shipping_postal_code.regex' => 'Postal code must be 5-10 digits',
            'shipping_country.required' => 'Country is required',
            'shipping_country.in' => 'We currently only ship to Sri Lanka',
            'shipping_phone.required' => 'Phone number is required',
            'shipping_phone.phone_number' => 'Please provide a valid phone number',
            'payment_method.required' => 'Payment method is required',
            'payment_method.in' => 'Invalid payment method selected',
            'payment_intent_id.required_if' => 'Payment confirmation is required for Stripe payments',
            'coupon_code.regex' => 'Invalid coupon code format',
            'terms_agreed.accepted' => 'You must agree to the terms and conditions',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'shipping_address' => trim($this->string('shipping_address')),
            'shipping_city' => trim($this->string('shipping_city')),
            'shipping_postal_code' => trim($this->string('shipping_postal_code')),
            'shipping_country' => strtolower($this->string('shipping_country', 'lk')),
            'payment_method' => strtolower($this->string('payment_method')),
            'coupon_code' => $this->filled('coupon_code') ? strtoupper(trim($this->string('coupon_code'))) : null,
        ]);
    }
}
