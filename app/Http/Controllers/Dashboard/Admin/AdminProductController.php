<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Contracts\Repositories\Catalog\CategoryRepositoryContract;
use App\Contracts\Repositories\ProductRepositoryContract;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\Catalog\ProductService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

final class AdminProductController extends Controller
{
    public function __construct(
        private readonly ProductRepositoryContract $productRepository,
        private readonly CategoryRepositoryContract $categories,
        private readonly ProductService $catalog,
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Product::class);

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

        $categoryFilterOptions = array_merge(
            [['value' => '', 'label' => __('All categories')]],
            $this->categories->allForSelect()->map(fn ($c) => [
                'value' => (string) $c->getKey(),
                'label' => $c->name,
            ])->all(),
        );

        $statusFilterOptions = array_merge(
            [['value' => '', 'label' => __('All statuses')]],
            $this->catalog->statusSelectOptions(),
        );

        return view('dashboard.admin.products.index', [
            'products' => $this->productRepository
                ->paginateForAdmin(15, $repoFilters)
                ->withQueryString(),
            'filters' => [
                'tag' => $tag,
                'category_id' => $request->query('category_id'),
                'status' => $request->query('status'),
                'search' => $search,
            ],
            'categoryFilterOptions' => $categoryFilterOptions,
            'statusFilterOptions' => $statusFilterOptions,
        ]);
    }

    public function show(Product $product): View
    {
        $this->authorize('view', $product);

        $product->load(['category', 'subCategory', 'subSubCategory', 'store', 'tags']);

        return view('dashboard.admin.products.show', compact('product'));
    }
}
