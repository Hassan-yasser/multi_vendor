<?php

namespace App\Repositories\Catalog;

use App\Contracts\Repositories\Catalog\CategoryRepositoryContract;
use App\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

final class CategoryRepository implements CategoryRepositoryContract
{
    public function paginateForList(int $perPage = 10): LengthAwarePaginator
    {
        return Category::query()->latest('id')->paginate($perPage);
    }

    public function find(int $id): ?Category
    {
        return Category::query()->find($id);
    }

    public function create(array $attributes): Category
    {
        return Category::query()->create($attributes);
    }

    public function update(Category $category, array $attributes): bool
    {
        return $category->update($attributes);
    }

    public function delete(Category $category): bool
    {
        return (bool) $category->delete();
    }

    public function allForSelect(): Collection
    {
        return Category::query()->orderBy('name')->get(['id', 'name']);
    }

    public function slugExists(string $slug, ?int $exceptCategoryId = null): bool
    {
        $query = Category::query()->where('slug', $slug);

        if ($exceptCategoryId !== null) {
            $query->whereKeyNot($exceptCategoryId);
        }

        return $query->exists();
    }
}
