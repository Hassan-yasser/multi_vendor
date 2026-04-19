<?php

use App\Http\Controllers\Dashboard\Catalog\SubSubCategoriesController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::resource('sub_sub_categories', SubSubCategoriesController::class);
});
