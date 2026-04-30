<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use App\Models\SubCategory;
use Database\Factories\Support\StorageDemoImages;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        StorageDemoImages::ensure();

        $price = fake()->randomFloat(2, 9.99, 999.99);
        $hasDiscount = fake()->boolean(35);
        $discountPct = $hasDiscount ? fake()->randomFloat(2, 5, 60) : 0.0;
        $discountPrice = $hasDiscount ? round($price * (1 - $discountPct / 100), 2) : $price;

        $start = fake()->optional(0.5)->dateTimeBetween('-1 month', '+1 month');
        $end = $start ? fake()->dateTimeBetween($start, '+3 months') : null;

        return [
            'category_id' => Category::factory(),
            'sub_category_id' => null,
            'sub_sub_category_id' => null,
            'store_id' => Store::factory(),
            'name' => fake()->words(rand(2, 5), true).' '.fake()->numerify('###'),
            'description' => fake()->optional(0.9)->paragraphs(rand(2, 5), true),
            'image' => StorageDemoImages::productImagePath(fake()->boolean()),
            'slug' => fake()->unique()->slug().'-'.fake()->numerify('#####'),
            'price' => $price,
            'discount_price' => $discountPrice,
            'rating' => fake()->randomFloat(2, 0, 5),
            'discount_percentage' => $discountPct,
            'discount_start_date' => $start ? $start->format('Y-m-d') : null,
            'discount_end_date' => $end ? $end->format('Y-m-d') : null,
            'featured' => fake()->boolean(25),
            'status' => fake()->randomElement(['draft', 'active', 'disactive']),
        ];
    }

    /**
     * Same catalog branch: category → sub → sub-sub (IDs aligned).
     */
    public function withCatalogTree(): static
    {
        return $this->state(function () {
            $category = Category::factory()->create();
            $sub = SubCategory::factory()->forCategory($category)->create();
            $subSub = SubSubCategoryFactory::new()->forSubCategory($sub)->create();

            return [
                'category_id' => $category->getKey(),
                'sub_category_id' => $sub->getKey(),
                'sub_sub_category_id' => $subSub->getKey(),
            ];
        });
    }

    /**
     * Attach product to existing store without creating another.
     */
    public function forStore(Store $store): static
    {
        return $this->state(fn () => [
            'store_id' => $store->getKey(),
        ]);
    }
}
