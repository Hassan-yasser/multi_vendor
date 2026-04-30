<?php

namespace App\Http\Controllers\Dashboard\Store;

use App\Contracts\Repositories\ProductRepositoryContract;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Inventory\AddProductsToInventoryRequest;
use App\Http\Requests\Dashboard\Inventory\MultiInventoryRestockRequest;
use App\Http\Requests\Dashboard\Inventory\RestockInventoryRequest;
use App\Http\Requests\Dashboard\Inventory\RestockSingleProductRequest;
use App\Http\Requests\Dashboard\Inventory\StoreInventoryRequest;
use App\Http\Requests\Dashboard\Inventory\UpdateInventoryRequest;
use App\Models\Inventory;
use App\Models\Product;
use App\Services\Inventory\InventoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventoryController extends Controller
{
  
    public function __construct(
        private readonly InventoryService $inventoryService,
        private readonly ProductRepositoryContract $productRepository
    ) {
    }

    public function index(Request $request): View|JsonResponse
    {
        $storeId = auth()->user()->store_id;

        if ($request->ajax() || $request->wantsJson()) {
            $products = $this->productRepository->getByStoreId($storeId)->map(function ($p) {
                $activeDiscount = $p->active_discount;
                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'price' => number_format($p->price, 2),
                    'image' => $p->image ? asset('storage/' . $p->image) : null,
                    'category' => $p->category?->name,
                    'tags' => $p->tags->pluck('name')->toArray(),
                    'has_discount' => $activeDiscount !== null,
                    'discount_percent' => $activeDiscount ? round((($p->price - $activeDiscount->discount_price) / $p->price) * 100) : 0,
                    'original_price' => $activeDiscount ? number_format($p->price, 2) : null,
                    'discounted_price' => $activeDiscount ? number_format($activeDiscount->discount_price, 2) : null,
                ];
            });
            return response()->json(['products' => $products]);
        }

        $inventories = $this->inventoryService->getStoreInventories($storeId);
        return view('dashboard.store.inventory.index', compact('inventories'));
    }

    public function create(): View
    {
        $storeId = auth()->user()->store_id;
        $products = $this->productRepository->getByStoreId($storeId);
        return view('dashboard.store.inventory.create', compact('products'));
    }

    public function store(StoreInventoryRequest $request): RedirectResponse
    {
        $storeId = auth()->user()->store_id;
        $this->inventoryService->createInventory($storeId, $request->validated(), $request->getProductsWithQuantities());

        return redirect()->route('inventory.index')->with('success', 'Inventory created successfully.');
    }

    public function show(Inventory $inventory): View
    {
        $storeId = auth()->user()->store_id;
        $inventory = $this->inventoryService->getInventory($inventory->id, $storeId);

        if ($inventory === null) {
            abort(403);
        }

        $products = $this->inventoryService->getInventoryProducts($inventory);

        return view('dashboard.store.inventory.show', compact('inventory', 'products'));
    }

    public function edit(Inventory $inventory): View
    {
        $storeId = auth()->user()->store_id;
        $inventory = $this->inventoryService->getInventory($inventory->id, $storeId);

        if ($inventory === null) {
            abort(403);
        }

        $products = $this->productRepository->getByStoreId($storeId);
        $inventoryProducts = $inventory->products->pluck('pivot.quantity', 'id')->toArray();

        return view('dashboard.store.inventory.edit', compact('inventory', 'products', 'inventoryProducts'));
    }

    public function update(UpdateInventoryRequest $request, Inventory $inventory): RedirectResponse
    {
        $storeId = auth()->user()->store_id;
        $inventory = $this->inventoryService->getInventory($inventory->id, $storeId);

        if ($inventory === null) {
            abort(403);
        }

        $this->inventoryService->updateInventory($inventory, $request->getInventoryAttributes(), $request->getProductsWithQuantities(), $request->shouldDetachProducts());

        return redirect()->route('inventory.index')->with('success', 'Inventory updated successfully.');
    }

    public function destroy(Inventory $inventory): RedirectResponse
    {
        $storeId = auth()->user()->store_id;
        $inventory = $this->inventoryService->getInventory($inventory->id, $storeId);

        if ($inventory === null) {
            abort(403);
        }

        $this->inventoryService->deleteInventory($inventory);

        return redirect()->route('inventory.index')->with('success', 'Inventory deleted successfully.');
    }

    public function getProducts(Inventory $inventory): JsonResponse
    {
        $storeId = auth()->user()->store_id;
        $inventory = $this->inventoryService->getInventory($inventory->id, $storeId);

        if ($inventory === null) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $products = $this->inventoryService->getProductsWithQuantity($inventory);

        return response()->json(['success' => true, 'products' => $products]);
    }

    public function restock(RestockInventoryRequest $request, Inventory $inventory): JsonResponse
    {
        $storeId = auth()->user()->store_id;
        $inventory = $this->inventoryService->getInventory($inventory->id, $storeId);

        if ($inventory === null) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $this->inventoryService->restockProducts($inventory, $request->input('quantities', []));

        return response()->json(['success' => true, 'message' => 'Stock updated successfully.']);
    }

    public function addProducts(AddProductsToInventoryRequest $request, Inventory $inventory): JsonResponse
    {
        $storeId = auth()->user()->store_id;
        $inventory = $this->inventoryService->getInventory($inventory->id, $storeId);

        if ($inventory === null) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $this->inventoryService->addProductsToInventory($inventory, $request->getProductsWithQuantities());

        return response()->json(['success' => true, 'message' => 'Products added successfully.']);
    }

    public function restockProduct(RestockSingleProductRequest $request, Inventory $inventory, Product $product): JsonResponse
    {
        $storeId = auth()->user()->store_id;
        $inventory = $this->inventoryService->getInventory($inventory->id, $storeId);

        if ($inventory === null) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $this->inventoryService->restockSingleProduct($inventory, $product->id, $request->input('quantity'));

        return response()->json(['success' => true, 'message' => 'Stock updated successfully.']);
    }

    public function multiRestock(MultiInventoryRestockRequest $request): JsonResponse
    {
        $product = Product::findOrFail($request->getProductId());

        // Verify all inventories belong to user
        foreach ($request->getInventoriesWithQuantities() as $inventoryData) {
            $inventory = $this->inventoryService->getInventory($inventoryData['inventory_id'], auth()->user()->store_id);
            if ($inventory === null) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
        }

        $this->inventoryService->multiInventoryRestock($product, $request->getInventoriesWithQuantities());

        return response()->json(['success' => true, 'message' => 'Stock updated successfully.']);
    }
}

