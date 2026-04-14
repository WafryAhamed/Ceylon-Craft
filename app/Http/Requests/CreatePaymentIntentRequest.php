<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Create Payment Intent Request (Stripe)
 * 
 * Validates payment intent creation before calling Stripe API.
 */
class CreatePaymentIntentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth('api')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'amount' => [
                'required',
                'numeric',
                'min:0.50', // $0.50 minimum per Stripe
                'max:999999.99',
            ],
            'currency' => [
                'required',
                'string',
                'size:3',
                'in:usd,gbp,eur',
            ],
            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'metadata' => [
                'nullable',
                'array',
            ],
        ];
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'amount.required' => 'Amount is required',
            'amount.numeric' => 'Amount must be a numberr',
            'amount.min' => 'Minimum amount is $0.50',
            'currency.required' => 'Currency is required',
            'currency.in' => 'Unsupported currency',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'currency' => strtolower($this->string('currency', 'usd')),
        ]);
    }
}
