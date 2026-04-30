<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Indexes + (MySQL/MariaDB) FULLTEXT so filters can hit indexes.
 * Guards avoid duplicate failures if a previous migrate run crashed mid-flight.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (! $this->productsIndexExists('products_store_id_category_id_index')) {
                $table->index(['store_id', 'category_id'], 'products_store_id_category_id_index');
            }
            if (! $this->productsIndexExists('products_category_id_index')) {
                $table->index('category_id', 'products_category_id_index');
            }
            if (! $this->productsIndexExists('products_status_index')) {
                $table->index('status', 'products_status_index');
            }
        });

        $driver = DB::connection()->getDriverName();
        if (
            in_array($driver, ['mysql', 'mariadb'], true)
            && ! $this->productsIndexExists('products_name_description_fulltext')
        ) {
            DB::statement(
                'ALTER TABLE products ADD FULLTEXT products_name_description_fulltext (name, description)',
            );
        }
    }

    public function down(): void
    {
        $driver = DB::connection()->getDriverName();
        if (in_array($driver, ['mysql', 'mariadb'], true) && $this->productsIndexExists('products_name_description_fulltext')) {
            DB::statement('ALTER TABLE products DROP INDEX products_name_description_fulltext');
        }

        Schema::table('products', function (Blueprint $table) {
            foreach (['products_store_id_category_id_index', 'products_category_id_index', 'products_status_index'] as $name) {
                if ($this->productsIndexExists($name)) {
                    $table->dropIndex($name);
                }
            }
        });
    }

    private function productsIndexExists(string $indexName): bool
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'sqlite') {
            foreach (DB::select('PRAGMA index_list(products)') as $row) {
                if (($row->name ?? $row->{'name'} ?? '') === $indexName) {
                    return true;
                }
            }

            return false;
        }

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            $db = DB::connection()->getDatabaseName();
            $row = DB::selectOne(
                'SELECT COUNT(1) AS c FROM information_schema.statistics WHERE table_schema = ? AND table_name = ? AND index_name = ?',
                [$db, 'products', $indexName],
            );

            return ($row->c ?? 0) >= 1;
        }

        return false;
    }
};
