<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_admin || $user->store_id !== null;
    }

    public function view(User $user, Product $product): bool
    {
        if ($user->is_admin) {
            return true;
        }

        return $user->store_id !== null && (int) $user->store_id === (int) $product->store_id;
    }

    public function create(User $user): bool
    {
        return $user->store_id !== null && ! $user->is_admin;
    }

    public function update(User $user, Product $product): bool
    {
        return ! $user->is_admin
            && $user->store_id !== null
            && (int) $user->store_id === (int) $product->store_id;
    }

    public function delete(User $user, Product $product): bool
    {
        return $this->update($user, $product);
    }
}
