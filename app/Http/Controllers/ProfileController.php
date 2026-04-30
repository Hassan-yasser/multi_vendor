<?php

namespace App\Http\Controllers;

use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Models\Store;
use App\Support\Catalog\CatalogImageStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user()->loadMissing('store'),
        ]);
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        /** @var array<string, mixed> $validated */
        $validated = $request->validated();
        unset($validated['logo'], $validated['cover_image']);

        $user = $request->user();

        $user->fill($validated)->save();

        if ($user->store_id !== null && ($request->hasFile('logo') || $request->hasFile('cover_image'))) {
            /** @var Store|null $store */
            $store = Store::query()->find($user->store_id);

            if ($store !== null) {
                $storeAttrs = [];

                if ($logo = $request->file('logo')) {
                    CatalogImageStorage::delete($store->logo);
                    $storeAttrs['logo'] = CatalogImageStorage::store($logo, CatalogImageStorage::PROFILE_DIRECTORY);
                }

                if ($cover = $request->file('cover_image')) {
                    CatalogImageStorage::delete($store->cover_image);
                    $storeAttrs['cover_image'] = CatalogImageStorage::store($cover, CatalogImageStorage::PROFILE_DIRECTORY);
                }

                if ($storeAttrs !== []) {
                    $store->fill($storeAttrs)->save();
                }
            }
        }

        return redirect()->route('profile.edit')
            ->with('status', 'profile-updated');
    }
}
