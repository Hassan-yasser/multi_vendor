<?php

namespace Database\Factories\Support;

use App\Support\Catalog\CatalogImageStorage;
use Illuminate\Support\Facades\Storage;

/**
 * Writes two tiny valid PNG files to the public disk (same bytes used as catalog fixtures),
 * plus copies under profile/ and products/ for factories.
 */
final class StorageDemoImages
{
    /** 1×1 PNG (dark). */
    private const PNG_A_BASE64 = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwAEhQGAhKmMIQAAAABJRU5ErkJggg==';

    /** Same valid 1×1 PNG stored under a second path (distinct file from demo-a). */
    private const PNG_B_BASE64 = self::PNG_A_BASE64;

    private static bool $primed = false;

    public static function ensure(): void
    {
        if (self::$primed) {
            return;
        }

        $disk = Storage::disk('public');
        $bytesA = base64_decode(self::PNG_A_BASE64, true) ?: '';
        $bytesB = base64_decode(self::PNG_B_BASE64, true) ?: '';

        $disk->put(CatalogImageStorage::CATEGORY_DIRECTORY.'/demo-a.png', $bytesA);
        $disk->put(CatalogImageStorage::SUB_CATEGORY_DIRECTORY.'/demo-b.png', $bytesB);
        $disk->put(CatalogImageStorage::SUB_SUB_CATEGORY_DIRECTORY.'/demo-a.png', $bytesA);

        $disk->put(CatalogImageStorage::PROFILE_DIRECTORY.'/demo-logo.png', $bytesA);
        $disk->put(CatalogImageStorage::PROFILE_DIRECTORY.'/demo-cover.png', $bytesB);

        $disk->put(CatalogImageStorage::PRODUCT_DIRECTORY.'/demo-a.png', $bytesA);
        $disk->put(CatalogImageStorage::PRODUCT_DIRECTORY.'/demo-b.png', $bytesB);

        self::$primed = true;
    }

    public static function categoryImagePath(): string
    {
        self::ensure();

        return CatalogImageStorage::CATEGORY_DIRECTORY.'/demo-a.png';
    }

    public static function subCategoryImagePath(): string
    {
        self::ensure();

        return CatalogImageStorage::SUB_CATEGORY_DIRECTORY.'/demo-b.png';
    }

    public static function subSubCategoryImagePath(): string
    {
        self::ensure();

        return CatalogImageStorage::SUB_SUB_CATEGORY_DIRECTORY.'/demo-a.png';
    }

    public static function storeLogoPath(): string
    {
        self::ensure();

        return CatalogImageStorage::PROFILE_DIRECTORY.'/demo-logo.png';
    }

    public static function storeCoverPath(): string
    {
        self::ensure();

        return CatalogImageStorage::PROFILE_DIRECTORY.'/demo-cover.png';
    }

    public static function productImagePath(bool $alternate = false): string
    {
        self::ensure();

        return $alternate
            ? CatalogImageStorage::PRODUCT_DIRECTORY.'/demo-b.png'
            : CatalogImageStorage::PRODUCT_DIRECTORY.'/demo-a.png';
    }
}
