<?php

namespace App\Services\Catalog;

use App\Contracts\Repositories\Catalog\CategoryRepositoryContract;
use App\Models\Category;
use App\Support\Catalog\CatalogImageStorage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

final class CategoryService
{
    public function __construct(
        private readonly CategoryRepositoryContract $categories,
    ) {}

    public function paginateForList(int $perPage = 10): LengthAwarePaginator
    {
        return $this->categories->paginateForList($perPage);
    }

    public function statusSelectOptions(): array
    {
        return [
            ['value' => '1', 'label' => 'Active'],
            ['value' => '0', 'label' => 'Inactive'],
        ];
    }

    public function createCategory(array $data): Category
    {
        $payload = $this->normalizedAttributes($data);
        $payload['slug'] = $this->ensureUniqueSlug($payload['slug'], null);

        return $this->categories->create($payload);
    }

    public function updateCategory(Category $category, array $data): Category
    {
        $payload = $this->normalizedAttributes($data);
        $payload['slug'] = $this->ensureUniqueSlug($payload['slug'], $category->getKey());

        $this->categories->update($category, $payload);

        return $category->fresh() ?? $category;
    }

    public function deleteCategory(Category $category): void
    {
        CatalogImageStorage::delete($category->image);
        $this->categories->delete($category);
    }


    private function normalizedAttributes(array $data): array
    {
        $name = (string) $data['name'];
        $slugInput = isset($data['slug']) && $data['slug'] !== '' && $data['slug'] !== null
            ? (string) $data['slug']
            : $name;

        $slug = Str::slug($slugInput);

        if ($slug === '') {
            $slug = Str::slug($name) ?: 'category';
        }

        $attributes = [
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

    private function ensureUniqueSlug(string $baseSlug, ?int $exceptCategoryId): string
    {
        $slug = $baseSlug;
        $suffix = 1;

        while ($this->categories->slugExists($slug, $exceptCategoryId)) {
            $slug = $baseSlug.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }
}
