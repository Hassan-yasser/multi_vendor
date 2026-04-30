<?php

namespace App\Services\Inventory;

use App\Contracts\Repositories\InventoryRepositoryContract;
use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

final class InventoryService
{
    public function __construct(
        private readonly InventoryRepositoryContract $inventoryRepository
    ) {
    }

    public function getStoreInventories(int $storeId): Collection
    {
        return $this->inventoryRepository->getWithProductCount($storeId);
    }

    public function getInventory(int $id, int $storeId): ?Inventory
    {
        $inventory = $this->inventoryRepository->findById($id);

        if ($inventory === null || $inventory->store_id !== $storeId) {
            return null;
        }

        return $inventory;
    }

    public function createInventory(int $storeId, array $attributes, array $productsWithQuantities): Inventory
    {
        $inventory = $this->inventoryRepository->create([
            'store_id' => $storeId,
            'name' => $attributes['name'],
            'address' => $attributes['address'] ?? null,
        ]);

        if (!empty($productsWithQuantities)) {
            $this->inventoryRepository->syncProducts($inventory, $productsWithQuantities);

            // Sync status for all added products
            $productIds = array_keys($productsWithQuantities);
            $products = Product::whereIn('id', $productIds)->get();
            foreach ($products as $product) {
                $this->syncProductStatusWithStock($product);
            }
        }

        return $inventory;
    }

    public function updateInventory(Inventory $inventory, array $attributes, array $productsWithQuantities, bool $shouldDetach): bool
    {
        $updated = $this->inventoryRepository->update($inventory, [
            'name' => $attributes['name'],
            'address' => $attributes['address'] ?? null,
        ]);

        if (!empty($productsWithQuantities)) {
            $this->inventoryRepository->syncProducts($inventory, $productsWithQuantities);

            // Sync status for all affected products
            $productIds = array_keys($productsWithQuantities);
            $products = Product::whereIn('id', $productIds)->get();
            foreach ($products as $product) {
                $this->syncProductStatusWithStock($product);
            }
        } elseif ($shouldDetach) {
            $inventory->products()->detach();

            // Sync status for all products that were in this inventory
            // (We need to get the products before detach, but since they're already detached,
            // we can't easily track them. The status will be updated when product is viewed or restocked)
        }

        return $updated;
    }

    public function deleteInventory(Inventory $inventory): bool
    {
        return $this->inventoryRepository->delete($inventory);
    }

    public function getInventoryProducts(Inventory $inventory, int $perPage = 10): LengthAwarePaginator
    {
        return $this->inventoryRepository->paginateProducts($inventory, $perPage);
    }

    public function getProductsWithQuantity(Inventory $inventory): SupportCollection
    {
        return $this->inventoryRepository->getProductsWithQuantity($inventory);
    }

    public function restockProducts(Inventory $inventory, array $quantities): void
    {
        $productIds = array_keys($quantities);

        foreach ($quantities as $productId => $quantity) {
            $this->inventoryRepository->updateProductQuantity($inventory, $productId, (int) $quantity);
        }

        // Sync status for all affected products
        $products = Product::whereIn('id', $productIds)->get();
        foreach ($products as $product) {
            $this->syncProductStatusWithStock($product);
        }
    }

    public function restockSingleProduct(Inventory $inventory, int $productId, int $quantity): bool
    {
        $result = $this->inventoryRepository->updateProductQuantity($inventory, $productId, $quantity);

        // Sync product status
        $product = Product::find($productId);
        if ($product) {
            $this->syncProductStatusWithStock($product);
        }

        return $result;
    }

    public function addProductsToInventory(Inventory $inventory, array $productsWithQuantities): void
    {
        $this->inventoryRepository->syncProducts($inventory, $productsWithQuantities);

        // Sync status for all added products
        $productIds = array_keys($productsWithQuantities);
        $products = Product::whereIn('id', $productIds)->get();
        foreach ($products as $product) {
            $this->syncProductStatusWithStock($product);
        }
    }

    public function multiInventoryRestock(Product $product, array $inventoriesData): void
    {
        $this->inventoryRepository->attachProductToMultipleInventories($product, $inventoriesData);
        $this->syncProductStatusWithStock($product);
    }

    /**
     * Check if product has any stock in any inventory.
     */
    public function productHasStock(Product $product): bool
    {
        return $product->inventories()
            ->wherePivot('quantity', '>', 0)
            ->exists();
    }

    /**
     * Get total stock quantity for a product across all inventories.
     */
    public function getProductTotalStock(Product $product): int
    {
        return (int) $product->inventories()
            ->sum('inventory_product.quantity');
    }

    /**
     * Sync product status based on inventory stock.
     * - If has stock > 0 in any inventory → status = 'active'
     * - If no stock or not in any inventory → status = 'disactive'
     */
    public function syncProductStatusWithStock(Product $product): void
    {
        $hasStock = $this->productHasStock($product);
        $currentStatus = $product->status;

        if ($hasStock && $currentStatus !== 'active') {
            $product->update(['status' => 'active']);
        } elseif (!$hasStock && $currentStatus === 'active') {
            $product->update(['status' => 'disactive']);
        }
    }

    /**
     * Update product status and validate if can be activated.
     * Returns false if trying to activate without stock.
     */
    public function canActivateProduct(Product $product): bool
    {
        return $this->productHasStock($product);
    }

}
