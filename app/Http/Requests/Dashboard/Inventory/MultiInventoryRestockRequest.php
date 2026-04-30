<?php

namespace App\Http\Requests\Dashboard\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class MultiInventoryRestockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => 'required|exists:products,id',
            'inventories' => 'required|array',
            'inventories.*.inventory_id' => 'required|exists:inventories,id',
            'inventories.*.quantity' => 'required|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'Please select a product.',
            'product_id.exists' => 'Selected product is invalid.',
            'inventories.required' => 'Please select at least one inventory.',
            'inventories.*.inventory_id.required' => 'Inventory ID is required.',
            'inventories.*.inventory_id.exists' => 'One or more inventories are invalid.',
            'inventories.*.quantity.required' => 'Quantity is required for each inventory.',
            'inventories.*.quantity.min' => 'Quantity cannot be negative.',
        ];
    }

    /**
     * Get inventories with their quantities.
     *
     * @return array<int, array{inventory_id: int, quantity: int}>
     */
    public function getInventoriesWithQuantities(): array
    {
        return $this->input('inventories', []);
    }

    public function getProductId(): int
    {
        return (int) $this->input('product_id');
    }
}
