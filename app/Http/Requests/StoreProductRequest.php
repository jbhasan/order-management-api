<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isVendor());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sku' => 'required|string|unique:products,sku',
            'price' => 'required|numeric|min:0',
            'is_active' => 'sometimes|boolean',
            'attributes' => 'nullable|array',
            'image_url' => 'nullable|url',
            'quantity' => 'sometimes|integer|min:0',
            'low_stock_threshold' => 'sometimes|integer|min:0',
            'variants' => 'sometimes|array',
            'variants.*.name' => 'required_with:variants|string',
            'variants.*.sku' => 'required_with:variants|string|unique:product_variants,sku',
            'variants.*.price' => 'nullable|numeric|min:0',
            'variants.*.attributes' => 'nullable|array',
        ];
    }
}
