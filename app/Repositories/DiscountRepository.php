<?php

namespace App\Repositories;

use App\Contracts\Repositories\DiscountRepositoryContract;
use App\Models\Discount;
use App\Models\Product;

final class DiscountRepository implements DiscountRepositoryContract
{
    public function create(array $attributes): Discount
    {
        return Discount::query()->create($attributes);
    }

    public function update(Discount $discount, array $attributes): bool
    {
        return $discount->update($attributes);
    }

    public function delete(Discount $discount): bool
    {
        return (bool) $discount->delete();
    }

    public function findByProduct(Product $product): ?Discount
    {
        return $product->discount()->first();
    }

    public function findActiveByProduct(Product $product): ?Discount
    {
        return $product->discount()
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();
    }
}
