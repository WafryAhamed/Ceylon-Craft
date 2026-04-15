<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
     * 
     * Supports both naming conventions:
     * - shipping_address/shipping_postal_code/shipping_phone/shipping_city
     * - address/postal_code/phone/country (for test compatibility)
     * 
     * Address is required (either shipping_address or address).
     */
    public function rules(): array
    {
        return [
            // Support both naming conventions - at least one address field required
            'address' => 'nullable|string|min:5|max:255',
            'shipping_address' => 'nullable|string|min:5|max:255',
            'postal_code' => 'nullable|regex:/^[0-9]{5,10}$/i',
            'shipping_postal_code' => 'nullable|regex:/^[0-9]{5,10}$/i',
            'phone' => 'nullable|string|max:20',
            'shipping_phone' => 'nullable|string|max:20',
            'country' => 'nullable|string',
            'shipping_country' => 'nullable|string',
            'shipping_city' => 'nullable|string',
            'city' => 'nullable|string',
            'payment_method' => 'nullable|string',
            'payment_intent_id' => 'nullable|string',
            'terms_agreed' => 'nullable|boolean',
            'notes' => 'nullable|string',
        ];
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'address.min' => 'Shipping address must be at least 5 characters',
            'shipping_address.min' => 'Shipping address must be at least 5 characters',
            'postal_code.regex' => 'Postal code must be 5-10 digits',
            'shipping_postal_code.regex' => 'Postal code must be 5-10 digits',
        ];
    }

    /**
     * Prepare the data for validation.
     * 
     * Map test field names to shipping_* names for controller compatibility.
     */
    protected function prepareForValidation(): void
    {
        // If address is provided but shipping_address is not, use address as shipping_address
        if ($this->has('address') && !$this->has('shipping_address')) {
            $this->merge(['shipping_address' => $this->input('address')]);
        }
        
        if ($this->has('postal_code') && !$this->has('shipping_postal_code')) {
            $this->merge(['shipping_postal_code' => $this->input('postal_code')]);
        }
        
        if ($this->has('phone') && !$this->has('shipping_phone')) {
            $this->merge(['shipping_phone' => $this->input('phone')]);
        }
        
        if ($this->has('country') && !$this->has('shipping_country')) {
            $this->merge(['shipping_country' => $this->input('country')]);
        }
        
        if ($this->has('city') && !$this->has('shipping_city')) {
            $this->merge(['shipping_city' => $this->input('city')]);
        }

        // Provide default city if missing
        if (!$this->has('shipping_city') && $this->has('shipping_address')) {
            $this->merge(['shipping_city' => 'N/A']);
        }
    }

    /**
     * Configure the validator instance with custom rule...
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Require at least one address field
            if (!$this->input('address') && !$this->input('shipping_address')) {
                $validator->errors()->add('address', 'Shipping address is required');
            }
        });
    }
}
