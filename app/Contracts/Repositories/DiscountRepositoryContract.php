<?php

namespace App\Contracts\Repositories;

use App\Models\Discount;
use App\Models\Product;

interface DiscountRepositoryContract
{
    public function create(array $attributes): Discount;

    public function update(Discount $discount, array $attributes): bool;

    public function delete(Discount $discount): bool;

    public function findByProduct(Product $product): ?Discount;

    public function findActiveByProduct(Product $product): ?Discount;
}
