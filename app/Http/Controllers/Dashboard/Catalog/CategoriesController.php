<?php

namespace App\Http\Controllers\Dashboard\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Catalog\Category\StoreCategoryRequest;
use App\Http\Requests\Dashboard\Catalog\Category\UpdateCategoryRequest;
use App\Models\Category;
use App\Services\Catalog\CategoryService;
use App\Support\Catalog\CatalogImageStorage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

final class CategoriesController extends Controller
{
    public function __construct(
        private readonly CategoryService $categories,
    ) {}

    public function index(): View
    {
        return view('dashboard.categories.index', [
            'categories' => $this->categories->paginateForList(10),
        ]);
    }

    public function create(): View
    {
        return view('dashboard.categories.create', [
            'statusOptions' => $this->categories->statusSelectOptions(),
        ]);
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $data = CatalogImageStorage::mergeStoredImage(
            $request->validated(),
            CatalogImageStorage::CATEGORY_DIRECTORY,
            null,
            false,
        );

        $this->categories->createCategory($data);

        return redirect()
            ->route('categories.index')
            ->with('success', __('Category created successfully.'));
    }   

    public function show(Category $category): View
    {
        $category->load(['subCategories' => fn ($q) => $q->orderBy('name')]);

        return view('dashboard.categories.view', compact('category'));
    }

    public function edit(Category $category): View
    {
        return view('dashboard.categories.update', [
            'category' => $category,
            'statusOptions' => $this->categories->statusSelectOptions(),
        ]);
    }

    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $data = CatalogImageStorage::mergeStoredImage(
            $request->validated(),
            CatalogImageStorage::CATEGORY_DIRECTORY,
            $category->image,
            true,
        );

        $this->categories->updateCategory($category, $data);

        return redirect()
            ->route('categories.index')
            ->with('success', __('Category updated successfully.'));
    }

    public function destroy(Category $category): RedirectResponse
    {
        $this->categories->deleteCategory($category);

        return redirect()
            ->route('categories.index')
            ->with('success', __('Category deleted successfully.'));
    }
}
