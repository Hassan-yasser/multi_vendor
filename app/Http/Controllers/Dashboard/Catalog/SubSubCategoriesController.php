<?php

namespace App\Http\Controllers\Dashboard\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Catalog\SubSubCategory\StoreSubSubCategoryRequest;
use App\Http\Requests\Dashboard\Catalog\SubSubCategory\UpdateSubSubCategoryRequest;
use App\Models\SubSubCategory;
use App\Services\Catalog\SubSubCategoryService;
use App\Support\Catalog\CatalogImageStorage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

final class SubSubCategoriesController extends Controller
{
    public function __construct(
        private readonly SubSubCategoryService $subSubCategories,
    ) {}

    public function index(): View
    {
        return view('dashboard.sub_sub_categories.index', [
            'subSubCategories' => $this->subSubCategories->paginateForList(10),
        ]);
    }

    public function create(): View
    {
        return view('dashboard.sub_sub_categories.create', [
            'subCategoryOptions' => $this->subSubCategories->subCategorySelectOptions(),
            'statusOptions' => $this->subSubCategories->statusSelectOptions(),
        ]);
    }

    public function store(StoreSubSubCategoryRequest $request): RedirectResponse
    {
        $data = CatalogImageStorage::mergeStoredImage(
            $request->validated(),
            CatalogImageStorage::SUB_SUB_CATEGORY_DIRECTORY,
            null,
            false,
        );

        $this->subSubCategories->createSubSubCategory($data);

        return redirect()
            ->route('sub_sub_categories.index')
            ->with('success', __('Sub-sub-category created successfully.'));
    }

    public function show(SubSubCategory $sub_sub_category): View
    {
        $sub_sub_category->load(['subCategory.category']);

        return view('dashboard.sub_sub_categories.view', ['subSubCategory' => $sub_sub_category]);
    }

    public function edit(SubSubCategory $sub_sub_category): View
    {
        return view('dashboard.sub_sub_categories.update', [
            'subSubCategory' => $sub_sub_category,
            'subCategoryOptions' => $this->subSubCategories->subCategorySelectOptions(),
            'statusOptions' => $this->subSubCategories->statusSelectOptions(),
        ]);
    }

    public function update(UpdateSubSubCategoryRequest $request, SubSubCategory $sub_sub_category): RedirectResponse
    {
        $data = CatalogImageStorage::mergeStoredImage(
            $request->validated(),
            CatalogImageStorage::SUB_SUB_CATEGORY_DIRECTORY,
            $sub_sub_category->image,
            true,
        );

        $this->subSubCategories->updateSubSubCategory($sub_sub_category, $data);

        return redirect()
            ->route('sub_sub_categories.index')
            ->with('success', __('Sub-sub-category updated successfully.'));
    }

    public function destroy(SubSubCategory $sub_sub_category): RedirectResponse
    {
        $this->subSubCategories->deleteSubSubCategory($sub_sub_category);

        return redirect()
            ->route('sub_sub_categories.index')
            ->with('success', __('Sub-sub-category deleted successfully.'));
    }
}
