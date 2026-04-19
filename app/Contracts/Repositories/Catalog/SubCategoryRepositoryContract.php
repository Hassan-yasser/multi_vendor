<?php

namespace App\Contracts\Repositories\Catalog;

use App\Models\SubCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface SubCategoryRepositoryContract
{
    public function paginateForList(int $perPage = 10): LengthAwarePaginator;

    public function create(array $attributes): SubCategory;

    public function update(SubCategory $subCategory, array $attributes): bool;

    public function delete(SubCategory $subCategory): bool;

    public function slugExists(string $slug, ?int $exceptId = null): bool;

    /**
     * @return Collection<int, SubCategory>
     */
    public function allForSelect(): Collection;
}
