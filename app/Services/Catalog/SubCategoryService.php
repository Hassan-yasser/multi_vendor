<?php

namespace App\Services\Catalog;

use App\Contracts\Repositories\Catalog\CategoryRepositoryContract;
use App\Contracts\Repositories\Catalog\SubCategoryRepositoryContract;
use App\Models\Category;
use App\Models\SubCategory;
use App\Support\Catalog\CatalogImageStorage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

final class SubCategoryService
{
    public function __construct(
        private readonly SubCategoryRepositoryContract $subCategories,
        private readonly CategoryRepositoryContract $categories,
    ) {}

    public function paginateForList(int $perPage = 10): LengthAwarePaginator
    {
        return $this->subCategories->paginateForList($perPage);
    }

    /**
     * @return list<array{value: string, label: string}>
     */
    public function categorySelectOptions(): array
    {
        $options = $this->categories->allForSelect()->map(fn (Category $c): array => [
            'value' => (string) $c->getKey(),
            'label' => $c->name,
        ])->all();

        array_unshift($options, [
            'value' => '',
            'label' => '— Select category —',
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

    public function createSubCategory(array $data): SubCategory
    {
        $payload = $this->normalizedAttributes($data);
        $payload['slug'] = $this->ensureUniqueSlug($payload['slug'], null);

        return $this->subCategories->create($payload);
    }

    public function updateSubCategory(SubCategory $subCategory, array $data): SubCategory
    {
        $payload = $this->normalizedAttributes($data);
        $payload['slug'] = $this->ensureUniqueSlug($payload['slug'], $subCategory->getKey());

        $this->subCategories->update($subCategory, $payload);

        return $subCategory->fresh() ?? $subCategory;
    }

    public function deleteSubCategory(SubCategory $subCategory): void
    {
        CatalogImageStorage::delete($subCategory->image);
        $this->subCategories->delete($subCategory);
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
            $slug = Str::slug($name) ?: 'sub-category';
        }

        $attributes = [
            'category_id' => (int) $data['category_id'],
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

        while ($this->subCategories->slugExists($slug, $exceptId)) {
            $slug = $baseSlug.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }
}
