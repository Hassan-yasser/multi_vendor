<?php

namespace App\Services\Catalog;

use App\Contracts\Repositories\ProductRepositoryContract;
use App\Models\Product;
use App\Models\Tag;
use Illuminate\Support\Str;

final class ProductService
{
    public function __construct(
        private readonly ProductRepositoryContract $products,
    ) {}

    /**
     * @param  array<string, mixed>  $data  Normalized payload including store_id and scalar fields (no UploadedFile).
     */
    public function createProduct(array $data): Product
    {
        $payload = $this->normalizedPayload($data);
        $payload['slug'] = $this->ensureUniqueSlugForStore(
            $payload['slug'],
            (int) $payload['store_id'],
            null,
        );

        $product = $this->products->create($payload);
        $this->products->syncTags($product, $this->normalizedTags($data));

        return $product->fresh(['tags']) ?? $product;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateProduct(Product $product, array $data): Product
    {
        $payload = $this->normalizedPayload($data);
        $payload['slug'] = $this->ensureUniqueSlugForStore(
            $payload['slug'],
            (int) $product->store_id,
            $product->getKey(),
        );

        // Keep original rating and featured values (don't change on update)
        unset($payload['rating']);
        unset($payload['featured']);

        $this->products->update($product, $payload);
        $this->products->syncTags($product, $this->normalizedTags($data));

        return $product->fresh(['tags']) ?? $product;
    }

    public function deleteProduct(Product $product): void
    {
        $this->products->delete($product);
    }

    /**
     * @return list<array{value: string, label: string}>
     */
    public function statusSelectOptions(): array
    {
        return [
            ['value' => 'draft', 'label' => 'Draft'],
            ['value' => 'active', 'label' => 'Active'],
            ['value' => 'disactive', 'label' => 'Inactive'],
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function normalizedPayload(array $data): array
    {
        $name = (string) $data['name'];
        $slugInput = isset($data['slug']) && $data['slug'] !== '' && $data['slug'] !== null
            ? (string) $data['slug']
            : $name;

        $slug = Str::slug($slugInput);
        if ($slug === '') {
            $slug = Str::slug($name) ?: 'product';
        }

        return [
            'category_id' => isset($data['category_id']) ? (int) $data['category_id'] : null,
            'sub_category_id' => ! empty($data['sub_category_id']) ? (int) $data['sub_category_id'] : null,
            'sub_sub_category_id' => ! empty($data['sub_sub_category_id']) ? (int) $data['sub_sub_category_id'] : null,
            'store_id' => (int) $data['store_id'],
            'name' => $name,
            'slug' => $slug,
            'description' => $data['description'] ?? null,
            'status' => (string) $data['status'],
            'price' => (float) $data['price'],
            'rating' => 0.0,
            'featured' => true, // Always true when creating, determined by date accessor when reading
        ] + $this->optionalImageKey($data);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function optionalImageKey(array $data): array
    {
        if (! array_key_exists('image', $data)) {
            return [];
        }

        return ['image' => $data['image']];
    }

    private function ensureUniqueSlugForStore(string $baseSlug, int $storeId, ?int $exceptProductId): string
    {
        $slug = $baseSlug;
        $suffix = 1;

        while ($this->products->slugExistsForStore($slug, $storeId, $exceptProductId)) {
            $slug = $baseSlug.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return list<string>
     */
    private function normalizedTags(array $data): array
    {
        $tags = $data['tags'] ?? [];
        if (!is_array($tags)) {
            return [];
        }

        // Auto-create tags that don't exist
        foreach ($tags as $tagName) {
            $tagName = trim($tagName);
            if ($tagName === '') {
                continue;
            }
            
            // Find or create tag
            Tag::firstOrCreate(
                ['slug' => Str::slug($tagName)],
                ['name' => $tagName]
            );
        }

        return collect($tags)
            ->map(fn (string $tag) => trim($tag))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}
