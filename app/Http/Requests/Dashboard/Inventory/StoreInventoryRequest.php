<?php

namespace App\Http\Requests\Dashboard\Inventory;

use App\Contracts\Repositories\InventoryRepositoryContract;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInventoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $storeId = auth()->user()->store_id;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('inventories', 'name')
                    ->where('store_id', $storeId),
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
     * Get the validated data with store_id injected.
     *
     * @return array<string, mixed>
     */
    public function validatedInventoryData(): array
    {
        return array_merge($this->validated(), [
            'store_id' => auth()->user()->store_id,
        ]);
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
}
