<?php

use App\Http\Controllers\Dashboard\Catalog\CategoriesController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'catalog.admin'])->group(function () {
    Route::resource('categories', CategoriesController::class);
});
