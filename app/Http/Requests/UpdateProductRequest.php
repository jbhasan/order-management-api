<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
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
		$productId = $this->route('product');

		return [
			'name' => 'sometimes|string|max:255',
			'description' => 'nullable|string',
			'sku' => ['sometimes', 'string', Rule::unique('products', 'sku')->ignore($productId)],
			'price' => 'sometimes|numeric|min:0',
			'is_active' => 'sometimes|boolean',
			'attributes' => 'nullable|array',
			'image_url' => 'nullable|url',
			'quantity' => 'sometimes|integer|min:0',
			'low_stock_threshold' => 'sometimes|integer|min:0',
			'variants' => 'sometimes|array',
			'variants.*.id' => 'required_with:variants|exists:product_variants,id',
			'variants.*.quantity' => 'required_with:variants|integer|min:0',
			'variants.*.low_stock_threshold' => 'sometimes|integer|min:0',
		];
	}
}
