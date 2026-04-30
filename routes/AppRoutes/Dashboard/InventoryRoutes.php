<?php

use App\Http\Controllers\Dashboard\Store\InventoryController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'store.staff'])->group(function () {
    Route::resource('inventory', InventoryController::class);
    Route::get('inventory/{inventory}/products', [InventoryController::class, 'getProducts'])->name('inventory.products');
    Route::post('inventory/{inventory}/products', [InventoryController::class, 'addProducts'])->name('inventory.products.add');
    Route::post('inventory/{inventory}/restock', [InventoryController::class, 'restock'])->name('inventory.restock');
    Route::post('inventory/{inventory}/restock/{product}', [InventoryController::class, 'restockProduct'])->name('inventory.restock.product');
    Route::post('inventory/multi-restock', [InventoryController::class, 'multiRestock'])->name('inventory.multi-restock');
});
