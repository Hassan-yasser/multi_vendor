<?php

namespace App\Http\Controllers\Dashboard\Store;

use App\Contracts\Repositories\Catalog\CategoryRepositoryContract;
use App\Contracts\Repositories\Catalog\SubCategoryRepositoryContract;
use App\Contracts\Repositories\Catalog\SubSubCategoryRepositoryContract;
use App\Contracts\Repositories\ProductRepositoryContract;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Product\StoreProductRequest;
use App\Http\Requests\Dashboard\Product\UpdateProductRequest;
use App\Models\Product;
use App\Models\Tag;
use App\Services\Catalog\ProductService;
use App\Support\Catalog\CatalogImageStorage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class StoreProductController extends Controller
{
    public function __construct(
        private readonly ProductService $products,
        private readonly ProductRepositoryContract $productRepository,
        private readonly CategoryRepositoryContract $categories,
        private readonly SubCategoryRepositoryContract $subCategories,
        private readonly SubSubCategoryRepositoryContract $subSubCategories,
    ) {
        $this->authorizeResource(Product::class, 'product');
    }

    public function index(Request $request): View
    {
        $storeId = (int) $request->user()->store_id;

        /** @var array<string, mixed> $repoFilters */
        $repoFilters = [];
        $tag = trim((string) $request->query('tag', ''));
        if ($tag !== '') {
            $repoFilters['tag'] = $tag;
        }
        if ($request->filled('category_id')) {
            $repoFilters['category_id'] = (int) $request->query('category_id');
        }
        if ($request->filled('status')) {
            $st = (string) $request->query('status');
            if (in_array($st, ['draft', 'active', 'disactive'], true)) {
                $repoFilters['status'] = $st;
            }
        }
        $search = trim((string) $request->query('search', ''));
        if ($search !== '') {
            $repoFilters['search'] = $search;
        }

        $categoryFilterOptions = array_merge([
            ['value' => '', 'label' => __('All categories')],
        ], $this->categories->allForSelect()->map(fn ($c) => [
            'value' => (string) $c->getKey(),
            'label' => $c->name,
        ])->all());

        $statusFilterOptions = array_merge([
            ['value' => '', 'label' => __('All statuses')],
        ], $this->products->statusSelectOptions());

        $tagFilterOptions = array_merge(
            [['value' => '', 'label' => __('All tags')]],
            Tag::pluck('name')->map(fn ($name) => ['value' => $name, 'label' => $name])->all()
        );

        return view('dashboard.store.products.index', [
            'products' => $this->productRepository
                ->paginateForStore($storeId, 10, $repoFilters)
                ->withQueryString(),
            'filters' => [
                'tag' => $tag,
                'category_id' => $request->query('category_id'),
                'status' => $request->query('status'),
                'search' => $search,
            ],
            'categoryFilterOptions' => $categoryFilterOptions,
            'statusFilterOptions' => $statusFilterOptions,
            'tagFilterOptions' => $tagFilterOptions,
        ]);
    }

    public function create(): View
    {
        return view('dashboard.store.products.create', $this->formOptions());
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $storeId = (int) $request->user()->store_id;
        $data = CatalogImageStorage::mergeStoredImage(
            $request->validated(),
            CatalogImageStorage::PRODUCT_DIRECTORY,
            null,
            false,
        );
        $data['store_id'] = $storeId;

        $this->products->createProduct($data);

        return redirect()
            ->route('products.index')
            ->with('success', __('Product created successfully.'));
    }

    public function show(Product $product): View
    {
        $product->load(['category', 'subCategory', 'subSubCategory', 'store', 'tags']);

        return view('dashboard.store.products.show', compact('product'));
    }

    public function edit(Product $product): View
    {
        $product->loadMissing('tags');

        return view('dashboard.store.products.edit', array_merge(
            $this->formOptions(),
            ['product' => $product],
        ));
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $data = CatalogImageStorage::mergeStoredImage(
            $request->validated(),
            CatalogImageStorage::PRODUCT_DIRECTORY,
            $product->image,
            true,
        );
        $data['store_id'] = (int) $product->store_id;

        $this->products->updateProduct($product, $data);

        return redirect()
            ->route('products.index')
            ->with('success', __('Product updated successfully.'));
    }

    public function destroy(Product $product): RedirectResponse
    {
        CatalogImageStorage::delete($product->image);
        $this->products->deleteProduct($product);

        return redirect()
            ->route('products.index')
            ->with('success', __('Product deleted successfully.'));
    }

    public function bulkArchive(Request $request): RedirectResponse
    {
        $productIds = $request->input('product_ids', []);
        $storeId = (int) $request->user()->store_id;

        if (empty($productIds)) {
            return redirect()
                ->route('products.index')
                ->with('error', __('No products selected.'));
        }

        // Update only products belonging to this store
        Product::where('store_id', $storeId)
            ->whereIn('id', $productIds)
            ->update(['status' => 'draft']);

        $count = count($productIds);
        return redirect()
            ->route('products.index', ['status' => 'draft'])
            ->with('success', __(':count products archived successfully.', ['count' => $count]));
    }

    /**
     * @return array<string, mixed>
     */
    private function formOptions(): array
    {
        return [
            'categoryOptions' => $this->categories->allForSelect()->map(fn ($c) => [
                'value' => (string) $c->getKey(),
                'label' => $c->name,
            ])->all(),
            'subCategoryOptions' => $this->subCategories->allForSelect()->map(fn ($s) => [
                'value' => (string) $s->getKey(),
                'label' => ($s->category?->name ?? '').' / '.$s->name,
            ])->all(),
            'subSubCategoryOptions' => $this->subSubCategories->allForSelect()->map(fn ($s) => [
                'value' => (string) $s->getKey(),
                'label' => trim(
                    ($s->subCategory?->category?->name ?? '').' / '.
                    ($s->subCategory?->name ?? '').' / '.$s->name,
                    ' /',
                ),
            ])->all(),
            'tagOptions' => Tag::pluck('name')->all(),
        ];
    }
}
