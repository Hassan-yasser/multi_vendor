<?php

namespace App\Services;

use App\Contracts\Repositories\DiscountRepositoryContract;
use App\Models\Discount;
use App\Models\Product;

final class DiscountService
{
    public function __construct(
        private readonly DiscountRepositoryContract $discounts,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function createDiscount(Product $product, array $data): Discount
    {
        // Delete any existing discount for this product
        $existing = $this->discounts->findByProduct($product);
        if ($existing) {
            $this->discounts->delete($existing);
        }

        $payload = [
            'product_id' => $product->id,
            'discount_price' => (float) $data['discount_price'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
        ];

        return $this->discounts->create($payload);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateDiscount(Discount $discount, array $data): Discount
    {
        $payload = [
            'discount_price' => (float) $data['discount_price'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
        ];

        $this->discounts->update($discount, $payload);

        return $discount->fresh();
    }

    public function deleteDiscount(Discount $discount): void
    {
        $this->discounts->delete($discount);
    }

    public function getProductDiscount(Product $product): ?Discount
    {
        return $this->discounts->findByProduct($product);
    }
}
