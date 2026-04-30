<?php

namespace Database\Factories;

use App\Models\SubCategory;
use App\Models\SubSubCategory;
use Database\Factories\Support\StorageDemoImages;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<SubSubCategory>
 */
class SubSubCategoryFactory extends Factory
{
    protected $model = SubSubCategory::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        StorageDemoImages::ensure();

        $name = fake()->unique()->sentence(rand(2, 4), true);
        $name = Str::limit($name, 450, '');

        return [
            'sub_category_id' => SubCategory::factory(),
            'name' => $name,
            'slug' => fake()->unique()->slug().'-'.fake()->numerify('###'),
            'description' => fake()->optional(0.85)->paragraphs(rand(1, 3), true),
            'image' => StorageDemoImages::subSubCategoryImagePath(),
            'status' => fake()->randomElement(['0', '1']),
            'meta_title' => fake()->optional(0.7)->sentence(rand(3, 8)),
            'meta_description' => fake()->optional(0.7)->text(120),
            'meta_keywords' => fake()->optional(0.5)->words(rand(4, 10), true),
        ];
    }

    public function forSubCategory(SubCategory $subCategory): static
    {
        return $this->state(fn () => [
            'sub_category_id' => $subCategory->getKey(),
        ]);
    }
}
