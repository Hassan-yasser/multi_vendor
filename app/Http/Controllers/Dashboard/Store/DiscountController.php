<?php

namespace App\Http\Controllers\Dashboard\Store;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Discount\StoreDiscountRequest;
use App\Http\Requests\Dashboard\Discount\UpdateDiscountRequest;
use App\Models\Discount;
use App\Models\Product;
use App\Services\DiscountService;
use App\Services\Inventory\InventoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

final class DiscountController extends Controller
{
    public function __construct(
        private readonly DiscountService $discounts,
        private readonly InventoryService $inventoryService,
    ) {}

    public function store(Product $product, StoreDiscountRequest $request): JsonResponse
    {
        try {
            // Check if user owns the product
            if (auth()->user()->store_id != $product->store_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to modify this product.',
                ], 403);
            }

            $discount = $this->discounts->createDiscount($product, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Discount created successfully.',
                'discount' => $discount,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function update(Discount $discount, UpdateDiscountRequest $request): JsonResponse
    {
        try {
            // Check if user owns the product
            if (auth()->user()->store_id != $discount->product->store_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to modify this discount.',
                ], 403);
            }

            $this->discounts->updateDiscount($discount, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Discount updated successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Discount $discount): JsonResponse
    {
        try {
            // Check if user owns the product
            if (auth()->user()->store_id != $discount->product->store_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to delete this discount.',
                ], 403);
            }

            $this->discounts->deleteDiscount($discount);

            return response()->json([
                'success' => true,
                'message' => 'Discount deleted successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function toggleStatus(Product $product): JsonResponse
    {
        try {
            if (auth()->user()->store_id != $product->store_id) {
                return response()->json(['success' => false, 'message' => 'You do not have permission to modify this product.'], 403);
            }

            $newStatus = request('status');
            if (!in_array($newStatus, ['active', 'disactive', 'draft'])) {
                return response()->json(['success' => false, 'message' => 'Invalid status.'], 400);
            }

            // Prevent activation if product has no stock
            if ($newStatus === 'active' && !$this->inventoryService->productHasStock($product)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot activate product. Product has no stock in any inventory. Please restock first.',
                ], 400);
            }

            $product->update(['status' => $newStatus]);

            return response()->json(['success' => true, 'message' => 'Product status updated successfully.', 'status' => $newStatus]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}
