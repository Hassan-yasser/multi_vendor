<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'sub_category_id',
        'sub_sub_category_id',
        'store_id',
        'name',
        'description',
        'image',
        'slug',
        'price',
        'rating',
        'featured',
        'status',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'featured' => 'boolean',
            'price' => 'decimal:2',
            'rating' => 'float',
        ];
    }

    /**
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return BelongsTo<SubCategory, $this>
     */
    public function subCategory(): BelongsTo
    {
        return $this->belongsTo(SubCategory::class);
    }

    /**
     * @return BelongsTo<SubSubCategory, $this>
     */
    public function subSubCategory(): BelongsTo
    {
        return $this->belongsTo(SubSubCategory::class);
    }

    /**
     * @return BelongsTo<Store, $this>
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * @return BelongsToMany<Tag, $this>
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function getTagListAttribute(): string
    {
        return $this->tags->pluck('name')->implode(', ');
    }

    /**
     * @return HasOne<Discount, $this>
     */
    public function discount(): HasOne
    {
        return $this->hasOne(Discount::class);
    }

    public function inventories(): BelongsToMany
    {
        return $this->belongsToMany(Inventory::class, 'inventory_product')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    /**
     * Get the active discount for this product
     */
    public function getActiveDiscountAttribute(): ?Discount
    {
        return $this->discount()->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();
    }

    /**
     * Get the current price (with discount if active)
     */
    public function getCurrentPriceAttribute(): float
    {
        $activeDiscount = $this->active_discount;
        if ($activeDiscount) {
            return (float) $activeDiscount->discount_price;
        }
        return (float) $this->price;
    }

    /**
     * Get featured status based on creation date
     * True for first week after creation, then false
     */
    public function getFeaturedAttribute($value): bool
    {
        // If explicitly set to false in database, respect that
        if ($value === false || $value === 0) {
            return false;
        }

        // Check if within first week of creation
        $createdAt = $this->created_at ?? now();
        $oneWeekLater = $createdAt->copy()->addWeek();

        return now()->lessThanOrEqualTo($oneWeekLater);
    }
}
