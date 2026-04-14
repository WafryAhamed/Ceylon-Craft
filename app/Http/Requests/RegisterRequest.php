<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

/**
 * Register Validation Request
 * 
 * Handles new user registration with comprehensive validation
 * and security checks.
 */
class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * 
     * Anyone can register, but not authenticated users.
     */
    public function authorize(): bool
    {
        // Prevent authenticated users from registering
        return !$this->user();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|min:2|max:255|regex:/^[\w\s\.\'-]+$/i',
            'email' => [
                'required',
                'email:rfc,dns',
                'unique:users,email',
                'max:255',
            ],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->mixedCase() // requires at least one uppercase and one lowercase
                    ->numbers()   // requires at least one number
                    ->symbols(),  // requires at least one symbol
            ],
            'phone' => 'nullable|phone_number|max:20',
            'address' => 'nullable|string|min:5|max:255',
            'city' => 'nullable|string|min:2|max:100',
            'postal_code' => 'nullable|regex:/^[0-9A-Z\s]{3,20}$/i|max:20',
            'country' => 'nullable|string|max:100',
        ];
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Full name is required',
            'name.min' => 'Name must be at least 2 characters',
            'name.regex' => 'Name contains invalid characters',
            'email.required' => 'Email address is required',
            'email.email' => 'Please provide a valid email address',
            'email.unique' => 'This email is already registered. Please login or use a different email.',
            'password.required' => 'Password is required',
            'password.confirmed' => 'Password confirmation does not match',
            'password.min' => 'Password must be at least 8 characters',
            'password.mixed_case' => 'Password must include uppercase and lowercase letters',
            'password.numbers' => 'Password must include at least one number',
            'password.symbols' => 'Password must include at least one special character (!@#$%^&*)',
            'postal_code.regex' => 'Postal code format is invalid',
        ];
    }

    /**
     * Prepare the data for validation.
     * 
     * Sanitize and normalize input data.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim($this->string('name')),
            'email' => strtolower(trim($this->string('email'))),
            'address' => $this->filled('address') ? trim($this->string('address')) : null,
            'city' => $this->filled('city') ? trim($this->string('city')) : null,
        ]);
    }
}
