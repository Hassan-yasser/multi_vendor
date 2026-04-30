<?php

namespace Database\Seeders;

use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'is_admin' => true,
        ]);

        User::factory()->create([
            'name' => 'Store user',
            'email' => 'store@example.com',
            'is_admin' => false,
        ]);

        $this->call(DemoCatalogSeeder::class);

        $store = Store::query()->first();
        $storeUser = User::query()->where('email', 'store@example.com')->first();
        if ($store !== null && $storeUser !== null) {
            $storeUser->forceFill(['store_id' => $store->getKey()])->save();
        }
    }
}
