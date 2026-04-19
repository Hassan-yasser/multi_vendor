<?php

use App\Http\Controllers\Dashboard\Catalog\CategoriesController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::resource('categories', CategoriesController::class);
});
