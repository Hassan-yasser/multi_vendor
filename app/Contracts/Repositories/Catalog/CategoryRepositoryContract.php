<?php

namespace App\Contracts\Repositories\Catalog;

use App\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface CategoryRepositoryContract
{
    public function paginateForList(int $perPage = 10): LengthAwarePaginator;

    public function find(int $id): ?Category;

    public function create(array $attributes): Category;

    public function update(Category $category, array $attributes): bool;

    public function delete(Category $category): bool;

    /**
     * @return Collection<int, Category>
     */
    public function allForSelect(): Collection;

    public function slugExists(string $slug, ?int $exceptCategoryId = null): bool;
}
