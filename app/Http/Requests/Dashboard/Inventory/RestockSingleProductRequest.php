<?php

namespace App\Http\Requests\Dashboard\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class RestockSingleProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'quantity' => 'required|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'quantity.required' => 'Please provide a quantity.',
            'quantity.integer' => 'Quantity must be a whole number.',
            'quantity.min' => 'Quantity cannot be negative.',
        ];
    }
}
