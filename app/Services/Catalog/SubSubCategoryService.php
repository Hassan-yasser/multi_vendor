<?php

namespace App\Services\Catalog;

use App\Contracts\Repositories\Catalog\SubCategoryRepositoryContract;
use App\Contracts\Repositories\Catalog\SubSubCategoryRepositoryContract;
use App\Models\SubCategory;
use App\Models\SubSubCategory;
use App\Support\Catalog\CatalogImageStorage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

final class SubSubCategoryService
{
    public function __construct(
        private readonly SubSubCategoryRepositoryContract $subSubCategories,
        private readonly SubCategoryRepositoryContract $subCategories,
    ) {}

    public function paginateForList(int $perPage = 10): LengthAwarePaginator
    {
        return $this->subSubCategories->paginateForList($perPage);
    }

    /**
     * @return list<array{value: string, label: string}>
     */
    public function subCategorySelectOptions(): array
    {
        $options = $this->subCategories->allForSelect()->map(function (SubCategory $s): array {
            $categoryName = $s->relationLoaded('category') && $s->category
                ? $s->category->name
                : '';

            return [
                'value' => (string) $s->getKey(),
                'label' => trim($categoryName.' / '.$s->name, ' /'),
            ];
        })->all();

        array_unshift($options, [
            'value' => '',
            'label' => '— Select sub-category —',
        ]);

        return $options;
    }

    /**
     * @return list<array{value: string, label: string}>
     */
    public function statusSelectOptions(): array
    {
        return [
            ['value' => '1', 'label' => 'Active'],
            ['value' => '0', 'label' => 'Inactive'],
        ];
    }

    public function createSubSubCategory(array $data): SubSubCategory
    {
        $payload = $this->normalizedAttributes($data);
        $payload['slug'] = $this->ensureUniqueSlug($payload['slug'], null);

        return $this->subSubCategories->create($payload);
    }

    public function updateSubSubCategory(SubSubCategory $subSubCategory, array $data): SubSubCategory
    {
        $payload = $this->normalizedAttributes($data);
        $payload['slug'] = $this->ensureUniqueSlug($payload['slug'], $subSubCategory->getKey());

        $this->subSubCategories->update($subSubCategory, $payload);

        return $subSubCategory->fresh() ?? $subSubCategory;
    }

    public function deleteSubSubCategory(SubSubCategory $subSubCategory): void
    {
        CatalogImageStorage::delete($subSubCategory->image);
        $this->subSubCategories->delete($subSubCategory);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function normalizedAttributes(array $data): array
    {
        $name = (string) $data['name'];
        $slugInput = isset($data['slug']) && $data['slug'] !== '' && $data['slug'] !== null
            ? (string) $data['slug']
            : $name;

        $slug = Str::slug($slugInput);

        if ($slug === '') {
            $slug = Str::slug($name) ?: 'sub-sub-category';
        }

        $attributes = [
            'sub_category_id' => (int) $data['sub_category_id'],
            'name' => $name,
            'slug' => $slug,
            'description' => $data['description'] ?? null,
            'status' => (string) $data['status'],
            'meta_title' => $data['meta_title'] ?? null,
            'meta_description' => $data['meta_description'] ?? null,
            'meta_keywords' => $data['meta_keywords'] ?? null,
        ];

        if (array_key_exists('image', $data)) {
            $attributes['image'] = $data['image'];
        }

        return $attributes;
    }

    private function ensureUniqueSlug(string $baseSlug, ?int $exceptId): string
    {
        $slug = $baseSlug;
        $suffix = 1;

        while ($this->subSubCategories->slugExists($slug, $exceptId)) {
            $slug = $baseSlug.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }
}
