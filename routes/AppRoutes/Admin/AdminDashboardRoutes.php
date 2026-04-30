<?php

use App\Http\Controllers\Dashboard\Admin\AdminProductController;
use App\Http\Controllers\Dashboard\Admin\AdminStoreController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'administrator'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('products', [AdminProductController::class, 'index'])->name('products.index');
    Route::get('products/{product}', [AdminProductController::class, 'show'])->name('products.show');

    Route::get('stores', [AdminStoreController::class, 'index'])->name('stores.index');
    Route::get('stores/{store}', [AdminStoreController::class, 'show'])->name('stores.show');
});
