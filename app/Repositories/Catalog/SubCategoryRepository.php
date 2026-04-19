<?php

namespace App\Repositories\Catalog;

use App\Contracts\Repositories\Catalog\SubCategoryRepositoryContract;
use App\Models\SubCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

final class SubCategoryRepository implements SubCategoryRepositoryContract
{
    public function paginateForList(int $perPage = 10): LengthAwarePaginator
    {
        return SubCategory::query()
            ->with(['category:id,name'])
            ->latest('id')
            ->paginate($perPage);
    }

    public function create(array $attributes): SubCategory
    {
        return SubCategory::query()->create($attributes);
    }

    public function update(SubCategory $subCategory, array $attributes): bool
    {
        return $subCategory->update($attributes);
    }

    public function delete(SubCategory $subCategory): bool
    {
        return (bool) $subCategory->delete();
    }

    public function slugExists(string $slug, ?int $exceptId = null): bool
    {
        $query = SubCategory::query()->where('slug', $slug);

        if ($exceptId !== null) {
            $query->whereKeyNot($exceptId);
        }

        return $query->exists();
    }

    public function allForSelect(): Collection
    {
        return SubCategory::query()
            ->with(['category:id,name'])
            ->orderBy('name')
            ->get(['id', 'category_id', 'name']);
    }
}
