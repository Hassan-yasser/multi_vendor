<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Inventory extends Model
{
    protected $fillable = ['store_id', 'name', 'address'];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'inventory_product')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function productsCount(): int
    {
        return $this->products()->count();
    }
}
