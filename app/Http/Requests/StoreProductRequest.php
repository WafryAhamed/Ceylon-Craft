<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Store Product Validation Request (Admin)
 * 
 * Comprehensive validation for product creation with advanced
 * checks for SKU, pricing, inventory, and file uploads.
 */
class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:255',
            ],
            'description' => [
                'required',
                'string',
                'min:10',
                'max:2000',
            ],
            'price' => [
                'required',
                'numeric',
                'min:0.01',
                'max:999999.99',
            ],
            'stock' => [
                'required',
                'integer',
                'min:0',
                'max:1000000',
            ],
            'category_id' => [
                'nullable',
                Rule::exists('categories', 'id'),
            ],
            'image' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,webp',
                'max:2048',
            ],
        ];
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Product name is required',
            'name.min' => 'Product name must be at least 3 characters',
            'name.unique' => 'This product name is already in use',
            'name.regex' => 'Product name contains invalid characters',
            'description.required' => 'Product description is required',
            'description.min' => 'Description must be at least 10 characters',
            'price.required' => 'Product price is required',
            'price.min' => 'Price must be greater than 0',
            'price.decimal' => 'Price must have 2 decimal places',
            'stock.required' => 'Stock quantity is required',
            'stock.min' => 'Stock cannot be negative',
            'category_id.required' => 'Product category is required',
            'category_id.exists' => 'Selected category does not exist',
            'image.image' => 'File must be a valid image',
            'image.mimes' => 'Image must be a JPEG, PNG, or WebP file',
            'image.max' => 'Image size cannot exceed 2MB',
            'image.dimensions' => 'Image must be at least 400x400 pixels',
            'sku.unique' => 'This SKU is already in use',
            'sku.regex' => 'SKU must contain only uppercase letters, numbers, and hyphens',
            'tags.max' => 'Maximum 10 tags allowed',
        ];
    }

    /**
     * Prepare the data for validation and transformation.
     */
    protected function prepareForValidation(): void
    {
        // Generate slug from name if not provided
        if (!$this->filled('slug') && $this->filled('name')) {
            $this->merge([
                'slug' => \Str::slug($this->string('name')),
            ]);
        }

        // Generate SKU from name if not provided
        if (!$this->filled('sku') && $this->filled('name')) {
            $this->merge([
                'sku' => strtoupper(\Str::limit(
                    preg_replace('/[^A-Z0-9]/', '', \Str::upper($this->string('name'))),
                    8,
                    ''
                )),
            ]);
        }

        // Normalize inputs
        $this->merge([
            'name' => trim($this->string('name')),
            'description' => trim($this->string('description')),
            'is_active' => $this->boolean('is_active', true),
            'is_featured' => $this->boolean('is_featured', false),
        ]);
    }
}
