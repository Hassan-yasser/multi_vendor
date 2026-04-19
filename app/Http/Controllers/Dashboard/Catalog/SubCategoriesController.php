<?php

namespace App\Http\Controllers\Dashboard\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Catalog\SubCategory\StoreSubCategoryRequest;
use App\Http\Requests\Dashboard\Catalog\SubCategory\UpdateSubCategoryRequest;
use App\Models\SubCategory;
use App\Services\Catalog\SubCategoryService;
use App\Support\Catalog\CatalogImageStorage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

final class SubCategoriesController extends Controller
{
    public function __construct(
        private readonly SubCategoryService $subCategories,
    ) {}

    public function index(): View
    {
        return view('dashboard.sub_categories.index', [
            'subCategories' => $this->subCategories->paginateForList(10),
        ]);
    }

    public function create(): View
    {
        return view('dashboard.sub_categories.create', [
            'categoryOptions' => $this->subCategories->categorySelectOptions(),
            'statusOptions' => $this->subCategories->statusSelectOptions(),
        ]);
    }

    public function store(StoreSubCategoryRequest $request): RedirectResponse
    {
        $data = CatalogImageStorage::mergeStoredImage(
            $request->validated(),
            CatalogImageStorage::SUB_CATEGORY_DIRECTORY,
            null,
            false,
        );

        $this->subCategories->createSubCategory($data);

        return redirect()
            ->route('sub_categories.index')
            ->with('success', __('Sub-category created successfully.'));
    }

    public function show(SubCategory $sub_category): View
    {
        $sub_category->load([
            'category',
            'subSubCategories' => fn ($q) => $q->orderBy('name'),
        ]);

        return view('dashboard.sub_categories.view', ['subCategory' => $sub_category]);
    }

    public function edit(SubCategory $sub_category): View
    {
        return view('dashboard.sub_categories.update', [
            'subCategory' => $sub_category,
            'categoryOptions' => $this->subCategories->categorySelectOptions(),
            'statusOptions' => $this->subCategories->statusSelectOptions(),
        ]);
    }

    public function update(UpdateSubCategoryRequest $request, SubCategory $sub_category): RedirectResponse
    {
        $data = CatalogImageStorage::mergeStoredImage(
            $request->validated(),
            CatalogImageStorage::SUB_CATEGORY_DIRECTORY,
            $sub_category->image,
            true,
        );

        $this->subCategories->updateSubCategory($sub_category, $data);

        return redirect()
            ->route('sub_categories.index')
            ->with('success', __('Sub-category updated successfully.'));
    }

    public function destroy(SubCategory $sub_category): RedirectResponse
    {
        $this->subCategories->deleteSubCategory($sub_category);

        return redirect()
            ->route('sub_categories.index')
            ->with('success', __('Sub-category deleted successfully.'));
    }
}
