<?php

namespace App\Http\Requests\Dashboard\Catalog\SubSubCategory;

use App\Http\Requests\Concerns\FormatsApiValidationErrors;
use App\Models\SubCategory;
use App\Models\SubSubCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSubSubCategoryRequest extends FormRequest
{
    use FormatsApiValidationErrors;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'sub_category_id' => ['required', 'integer', Rule::exists(SubCategory::class, 'id')],
            'name' => ['required', 'string', 'max:500', Rule::unique(SubSubCategory::class, 'name')],
            'slug' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp,gif', 'max:4096'],
            'status' => ['required', 'string', Rule::in(['0', '1'])],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords' => ['nullable', 'string', 'max:500'],
        ];
    }
}
