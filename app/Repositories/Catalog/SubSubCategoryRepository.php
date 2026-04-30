<?php

namespace App\Repositories\Catalog;

use App\Contracts\Repositories\Catalog\SubSubCategoryRepositoryContract;
use App\Models\SubSubCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

final class SubSubCategoryRepository implements SubSubCategoryRepositoryContract
{
    public function paginateForList(int $perPage = 10): LengthAwarePaginator
    {
        return SubSubCategory::query()
            ->with([
                'subCategory:id,name,category_id',
                'subCategory.category:id,name',
            ])
            ->latest('id')
            ->paginate($perPage);
    }

    public function create(array $attributes): SubSubCategory
    {
        return SubSubCategory::query()->create($attributes);
    }

    public function update(SubSubCategory $subSubCategory, array $attributes): bool
    {
        return $subSubCategory->update($attributes);
    }

    public function delete(SubSubCategory $subSubCategory): bool
    {
        return (bool) $subSubCategory->delete();
    }

    public function slugExists(string $slug, ?int $exceptId = null): bool
    {
        $query = SubSubCategory::query()->where('slug', $slug);

        if ($exceptId !== null) {
            $query->whereKeyNot($exceptId);
        }

        return $query->exists();
    }

    public function allForSelect(): Collection
    {
        return SubSubCategory::query()
            ->with([
                'subCategory:id,name,category_id',
                'subCategory.category:id,name',
            ])
            ->orderBy('name')
            ->get();
    }
}
