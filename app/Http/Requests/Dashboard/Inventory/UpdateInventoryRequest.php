<?php

namespace App\Http\Requests\Dashboard\Inventory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateInventoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $storeId = auth()->user()->store_id;
        $inventoryId = $this->route('inventory')?->id;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('inventories', 'name')
                    ->where('store_id', $storeId)
                    ->ignore($inventoryId),
            ],
            'address' => 'nullable|string',
            'products' => 'nullable|array',
            'products.*' => 'exists:products,id',
            'quantities' => 'nullable|array',
            'quantities.*' => 'integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Inventory name is required.',
            'name.unique' => 'This inventory name already exists.',
            'name.max' => 'Inventory name cannot exceed 255 characters.',
        ];
    }

    /**
     * Get the validated data excluding products and quantities.
     *
     * @return array<string, mixed>
     */
    public function getInventoryAttributes(): array
    {
        return $this->only(['name', 'address']);
    }

    /**
     * Map products with their quantities.
     *
     * @return array<int, int>
     */
    public function getProductsWithQuantities(): array
    {
        $products = $this->input('products', []);
        $quantities = $this->input('quantities', []);

        $result = [];
        foreach ($products as $productId) {
            $result[$productId] = $quantities[$productId] ?? 0;
        }

        return $result;
    }

    /**
     * Check if products should be detached (empty products array sent).
     */
    public function shouldDetachProducts(): bool
    {
        return $this->has('products') && empty($this->input('products'));
    }
}
