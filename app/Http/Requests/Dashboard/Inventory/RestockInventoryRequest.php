<?php

namespace App\Http\Requests\Dashboard\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class RestockInventoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'quantities' => 'required|array',
            'quantities.*' => 'integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'quantities.required' => 'Please provide quantities to update.',
            'quantities.*.integer' => 'Quantity must be a whole number.',
            'quantities.*.min' => 'Quantity cannot be negative.',
        ];
    }
}
