<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Contracts\View\View;

final class AdminStoreController extends Controller
{
    public function index(): View
    {
        return view('dashboard.admin.stores.index', [
            'stores' => Store::query()->withCount(['products', 'orders'])->latest('id')->paginate(12),
        ]);
    }

    public function show(Store $store): View
    {
        $store->loadCount(['products', 'orders']);

        $stats = [
            'orders_count' => $store->orders()->count(),
            'revenue' => (float) $store->orders()->sum('total'),
            'products_count' => $store->products()->count(),
            'avg_rating' => round((float) ($store->products()->avg('rating') ?? 0), 2),
        ];

        return view('dashboard.admin.stores.show', compact('store', 'stats'));
    }
}
