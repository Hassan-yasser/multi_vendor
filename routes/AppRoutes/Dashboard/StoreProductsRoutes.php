<?php

use App\Http\Controllers\Dashboard\Store\DiscountController;
use App\Http\Controllers\Dashboard\Store\StoreProductController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'store.staff'])->group(function () {
    Route::resource('products', StoreProductController::class);
    Route::post('products/bulk-archive', [StoreProductController::class, 'bulkArchive'])->name('products.bulk-archive');
});

// Discount routes - with auth middleware only
Route::middleware(['auth'])->group(function () {
    Route::post('products/{product}/discount', [DiscountController::class, 'store'])->name('products.discount.store');
    Route::put('discounts/{discount}', [DiscountController::class, 'update'])->name('discounts.update');
    Route::delete('discounts/{discount}', [DiscountController::class, 'destroy'])->name('discounts.destroy');
    Route::post('products/{product}/toggle-status', [DiscountController::class, 'toggleStatus'])->name('products.toggle-status');
});
