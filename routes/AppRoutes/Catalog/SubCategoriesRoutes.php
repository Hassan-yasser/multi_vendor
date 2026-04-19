<?php

use App\Http\Controllers\Dashboard\Catalog\SubCategoriesController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::resource('sub_categories', SubCategoriesController::class);
});
