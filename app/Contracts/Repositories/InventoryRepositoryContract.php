<?php

namespace App\Contracts\Repositories;

use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

interface InventoryRepositoryContract
{
    public function getByStoreId(int $storeId): Collection;

    public function getWithProductCount(int $storeId): Collection;

    public function findById(int $id): ?Inventory;

    public function create(array $attributes): Inventory;

    public function update(Inventory $inventory, array $attributes): bool;

    public function delete(Inventory $inventory): bool;

    public function nameExistsForStore(string $name, int $storeId, ?int $exceptId = null): bool;

    public function paginateProducts(Inventory $inventory, int $perPage = 10): LengthAwarePaginator;

    public function getProductsWithQuantity(Inventory $inventory): SupportCollection;

    public function syncProducts(Inventory $inventory, array $productsWithQuantities): void;

    public function updateProductQuantity(Inventory $inventory, int $productId, int $quantity): bool;

    public function attachProductToMultipleInventories(Product $product, array $inventoriesWithQuantities): void;
}
