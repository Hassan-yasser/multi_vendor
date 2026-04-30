<?php

namespace Database\Factories;

use App\Models\Category;
use Database\Factories\Support\StorageDemoImages;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    protected $model = Category::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        StorageDemoImages::ensure();

        $name = fake()->unique()->sentence(rand(2, 4), true);
        $name = Str::limit($name, 450, '');

        return [
            'name' => $name,
            'slug' => fake()->unique()->slug().'-'.fake()->numerify('###'),
            'description' => fake()->optional(0.85)->paragraphs(rand(1, 3), true),
            'image' => StorageDemoImages::categoryImagePath(),
            'status' => fake()->randomElement(['0', '1']),
            'meta_title' => fake()->optional(0.7)->sentence(rand(3, 8)),
            'meta_description' => fake()->optional(0.7)->text(120),
            'meta_keywords' => fake()->optional(0.5)->words(rand(4, 10), true),
        ];
    }
}
