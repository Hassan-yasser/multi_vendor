<?php

namespace App\Repositories;

use App\Contracts\Repositories\InventoryRepositoryContract;
use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

final class InventoryRepository implements InventoryRepositoryContract
{
    public function getByStoreId(int $storeId): Collection
    {
        return Inventory::query()
            ->where('store_id', $storeId)
            ->get();
    }

    public function getWithProductCount(int $storeId): Collection
    {
        return Inventory::query()
            ->withCount('products')
            ->where('store_id', $storeId)
            ->get();
    }

    public function findById(int $id): ?Inventory
    {
        return Inventory::query()->find($id);
    }

    public function create(array $attributes): Inventory
    {
        return Inventory::query()->create($attributes);
    }

    public function update(Inventory $inventory, array $attributes): bool
    {
        return $inventory->update($attributes);
    }

    public function delete(Inventory $inventory): bool
    {
        return (bool) $inventory->delete();
    }

    public function nameExistsForStore(string $name, int $storeId, ?int $exceptId = null): bool
    {
        $query = Inventory::query()
            ->where('store_id', $storeId)
            ->where('name', $name);

        if ($exceptId !== null) {
            $query->whereKeyNot($exceptId);
        }

        return $query->exists();
    }

    public function paginateProducts(Inventory $inventory, int $perPage = 10): LengthAwarePaginator
    {
        return $inventory->products()
            ->with(['category', 'tags', 'discount'])
            ->paginate($perPage);
    }

    public function getProductsWithQuantity(Inventory $inventory): SupportCollection
    {
        return $inventory->products()
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'image' => $product->image ? asset('storage/' . $product->image) : null,
                    'current_quantity' => $product->pivot->quantity ?? 0,
                ];
            });
    }

    public function syncProducts(Inventory $inventory, array $productsWithQuantities): void
    {
        $syncData = [];
        foreach ($productsWithQuantities as $productId => $quantity) {
            $syncData[$productId] = ['quantity' => $quantity];
        }
        $inventory->products()->sync($syncData);
    }

    public function updateProductQuantity(Inventory $inventory, int $productId, int $quantity): bool
    {
        return (bool) $inventory->products()->updateExistingPivot($productId, ['quantity' => $quantity]);
    }

    public function attachProductToMultipleInventories(Product $product, array $inventoriesWithQuantities): void
    {
        foreach ($inventoriesWithQuantities as $inventoryData) {
            $inventory = $this->findById($inventoryData['inventory_id']);
            if ($inventory) {
                $inventory->products()->syncWithoutDetaching([
                    $product->id => ['quantity' => $inventoryData['quantity']],
                ]);
            }
        }
    }
}
