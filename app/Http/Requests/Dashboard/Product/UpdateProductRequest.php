<?php

namespace App\Http\Requests\Dashboard\Product;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Product $product */
        $product = $this->route('product');

        return $this->user()?->can('update', $product) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:500'],
            'slug' => ['nullable', 'string', 'max:255'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:100'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp,gif', 'max:4096'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'sub_category_id' => ['nullable', 'integer', 'exists:sub_categories,id'],
            'sub_sub_category_id' => ['nullable', 'integer', 'exists:sub_sub_categories,id'],
            'price' => ['required', 'numeric', 'min:0'],
        ];
    }

    protected function prepareForValidation(): void
    {
        /** @var Product|null $product */
        $product = $this->route('product');

        $this->merge([
            'tags' => $this->input('tags', []),
            'sub_category_id' => $this->input('sub_category_id') ?: null,
            'sub_sub_category_id' => $this->input('sub_sub_category_id') ?: null,
            'status' => $product?->status ?? 'draft', // Keep existing status, managed by inventory
        ]);
    }
}
