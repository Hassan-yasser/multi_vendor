<?php

namespace App\Http\Requests\Dashboard\Inventory;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;

class AddProductsToInventoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'products' => 'required|array',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $products = $this->input('products', []);

            foreach ($products as $productId => $quantity) {
                // Validate product ID exists
                if (!is_numeric($productId) || !Product::where('id', $productId)->exists()) {
                    $validator->errors()->add('products.'.$productId, 'Product ID '.$productId.' is invalid.');
                }
                // Validate quantity is integer >= 0
                if (!is_numeric($quantity) || (int)$quantity < 0) {
                    $validator->errors()->add('products.'.$productId, 'Quantity for product '.$productId.' must be a positive number.');
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'products.required' => 'Please select products to add.',
            'products.*.exists' => 'One or more selected products are invalid.',
        ];
    }

    /**
     * Get products with their quantities.
     *
     * @return array<int, int>
     */
    public function getProductsWithQuantities(): array
    {
        $products = $this->input('products', []);
        $result = [];

        foreach ($products as $productId => $quantity) {
            $result[$productId] = (int) $quantity;
        }

        return $result;
    }
}
