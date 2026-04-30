<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\SubCategory;
use Database\Factories\Support\StorageDemoImages;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<SubCategory>
 */
class SubCategoryFactory extends Factory
{
    protected $model = SubCategory::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        StorageDemoImages::ensure();

        $name = fake()->unique()->sentence(rand(2, 4), true);
        $name = Str::limit($name, 450, '');

        return [
            'category_id' => Category::factory(),
            'name' => $name,
            'slug' => fake()->unique()->slug().'-'.fake()->numerify('###'),
            'description' => fake()->optional(0.85)->paragraphs(rand(1, 3), true),
            'image' => StorageDemoImages::subCategoryImagePath(),
            'status' => fake()->randomElement(['0', '1']),
            'meta_title' => fake()->optional(0.7)->sentence(rand(3, 8)),
            'meta_description' => fake()->optional(0.7)->text(120),
            'meta_keywords' => fake()->optional(0.5)->words(rand(4, 10), true),
        ];
    }

    /**
     * Sub-category under an existing category (same catalog tree).
     */
    public function forCategory(Category $category): static
    {
        return $this->state(fn () => [
            'category_id' => $category->getKey(),
        ]);
    }
}
