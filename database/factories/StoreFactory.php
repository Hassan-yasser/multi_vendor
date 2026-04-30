<?php

namespace Database\Factories;

use App\Models\Store;
use Database\Factories\Support\StorageDemoImages;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Store>
 */
class StoreFactory extends Factory
{
    protected $model = Store::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        StorageDemoImages::ensure();

        $name = fake()->company().' '.fake()->numerify('###');

        return [
            'name' => Str::limit($name, 500, ''),
            'slug' => fake()->unique()->slug().'-'.fake()->numerify('#####'),
            'description' => fake()->optional(0.9)->paragraphs(rand(1, 2), true),
            'logo' => StorageDemoImages::storeLogoPath(),
            'cover_image' => StorageDemoImages::storeCoverPath(),
            'status' => fake()->randomElement(['0', '1']),
        ];
    }
}
