<?php

namespace App\Contracts\Repositories;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ProductRepositoryContract
{
    public function getByStoreId(int $storeId): Collection;
    public function paginateForStore(int $storeId, int $perPage = 10, array $filters = []): LengthAwarePaginator;

    public function paginateForAdmin(int $perPage = 10, array $filters = []): LengthAwarePaginator;

    public function create(array $attributes): Product;

    public function update(Product $product, array $attributes): bool;

    public function delete(Product $product): bool;

    public function slugExistsForStore(string $slug, int $storeId, ?int $exceptProductId = null): bool;

    /**
     * @param  list<string>  $tags
     */
    public function syncTags(Product $product, array $tags): void;
}
