<?php

namespace App\Contracts\Repositories\Catalog;

use App\Models\SubSubCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface SubSubCategoryRepositoryContract
{
    public function paginateForList(int $perPage = 10): LengthAwarePaginator;

    public function create(array $attributes): SubSubCategory;

    public function update(SubSubCategory $subSubCategory, array $attributes): bool;

    public function delete(SubSubCategory $subSubCategory): bool;

    public function slugExists(string $slug, ?int $exceptId = null): bool;

    /**
     * @return Collection<int, SubSubCategory>
     */
    public function allForSelect(): Collection;
}
