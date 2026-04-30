<?php

namespace App\Repositories;

use App\Contracts\Repositories\ProductRepositoryContract;
use App\Models\Product;
use App\Models\Tag;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

final class ProductRepository implements ProductRepositoryContract
{
    public function getByStoreId(int $storeId): Collection
    {
        return Product::query()
            ->where('store_id', $storeId)
            ->with(['category', 'tags', 'discount'])
            ->get();
    }

    public function paginateForStore(int $storeId, int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        $query = Product::query()
            ->where('store_id', $storeId)
            ->with(['category', 'subCategory', 'subSubCategory', 'tags', 'discount']);

        $this->applyFilters($query, $filters);

        return $query->latest('id')->paginate($perPage);
    }

    public function paginateForAdmin(int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        $query = Product::query()
            ->with(['category', 'store', 'subCategory', 'subSubCategory', 'tags']);

        $this->applyFilters($query, $filters);

        return $query->latest('id')->paginate($perPage);
    }

    public function create(array $attributes): Product
    {
        return Product::query()->create($attributes);
    }

    public function update(Product $product, array $attributes): bool
    {
        return $product->update($attributes);
    }

    public function delete(Product $product): bool
    {
        return (bool) $product->delete();
    }

    public function slugExistsForStore(string $slug, int $storeId, ?int $exceptProductId = null): bool
    {
        $query = Product::query()
            ->where('store_id', $storeId)
            ->where('slug', $slug);

        if ($exceptProductId !== null) {
            $query->whereKeyNot($exceptProductId);
        }

        return $query->exists();
    }

    public function syncTags(Product $product, array $tags): void
    {
        if ($tags === []) {
            $product->tags()->sync([]);

            return;
        }

        $tagIds = collect($tags)
            ->map(fn (string $tagName) => trim($tagName))
            ->filter()
            ->map(function (string $tagName): int {
                $normalizedName = Str::of($tagName)->squish()->limit(50, '');
                $name = (string) $normalizedName;
                $slug = Str::slug($name);

                if ($slug === '') {
                    $slug = 'tag-'.Str::lower(Str::random(8));
                }

                /** @var Tag $tag */
                $tag = Tag::query()->firstOrCreate(
                    ['slug' => $slug],
                    ['name' => $name],
                );

                if ($tag->name !== $name) {
                    $tag->update(['name' => $name]);
                }

                return (int) $tag->getKey();
            })
            ->unique()
            ->values()
            ->all();

        $product->tags()->sync($tagIds);
    }

    /**
     * @param  array<string, mixed>  $filters Keys: tag, category_id, status, search
     */
    private function applyFilters(Builder $query, array $filters): void
    {
        $categoryId = $filters['category_id'] ?? null;
        if ($categoryId !== null && $categoryId !== '') {
            $query->where('category_id', (int) $categoryId);
        }

        $status = isset($filters['status']) ? trim((string) $filters['status']) : '';
        if ($status !== '' && in_array($status, ['draft', 'active', 'disactive'], true)) {
            $query->where('status', $status);
        }

        $search = isset($filters['search']) ? trim((string) $filters['search']) : '';
        if ($search !== '') {
            $this->applyProductTextSearch($query, $search);
        }

        $searchTag = isset($filters['tag']) ? trim((string) $filters['tag']) : '';
        if ($searchTag !== '') {
            $query->whereHas('tags', function (Builder $tagQuery) use ($searchTag): void {
                $tagQuery
                    ->where('tags.name', 'like', '%'.$searchTag.'%')
                    ->orWhere('tags.slug', 'like', '%'.Str::slug($searchTag).'%');
            });
        }
    }

    private function applyProductTextSearch(Builder $query, string $search): void
    {
        $driver = $query->getConnection()->getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            $query->whereRaw(
                'MATCH(products.`name`, products.`description`) AGAINST (? IN NATURAL LANGUAGE MODE)',
                [$search],
            );

            return;
        }

        $escaped = str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $search);
        $like = '%'.$escaped.'%';

        $query->where(function (Builder $inner) use ($like): void {
            $inner->where('products.name', 'like', $like)
                ->orWhere('products.description', 'like', $like);
        });
    }
}

