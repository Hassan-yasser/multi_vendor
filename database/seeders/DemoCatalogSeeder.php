<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Store;
use App\Models\SubCategory;
use App\Models\SubSubCategory;
use Illuminate\Database\Seeder;

class DemoCatalogSeeder extends Seeder
{
    /**
     * Seed catalog + stores + sample products (uses factories & demo images on public disk).
     */
    public function run(): void
    {
        Category::factory()->count(5)->create();

        SubCategory::factory()->count(8)->create();

        SubSubCategory::factory()->count(10)->create();

        $stores = Store::factory()->count(4)->create();
        $categories = Category::query()->inRandomOrder()->limit(8)->get();
        $storeDefault = $stores->random();

        Product::factory()
            ->count(12)
            ->state(fn () => [
                'category_id' => $categories->random()->getKey(),
            ])
            ->forStore($storeDefault)
            ->create();

        Product::factory()
            ->count(5)
            ->withCatalogTree()
            ->forStore($stores->random())
            ->create();

        foreach (Store::query()->get() as $store) {
            Order::factory()
                ->count(random_int(4, 14))
                ->create(['store_id' => $store->getKey()]);
        }
    }
}
