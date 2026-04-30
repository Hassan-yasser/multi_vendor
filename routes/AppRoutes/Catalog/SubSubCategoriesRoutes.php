<?php

use App\Http\Controllers\Dashboard\Catalog\SubSubCategoriesController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'catalog.admin'])->group(function () {
    Route::resource('sub_sub_categories', SubSubCategoriesController::class);
});
