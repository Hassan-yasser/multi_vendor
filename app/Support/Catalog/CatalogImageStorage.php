<?php

namespace App\Support\Catalog;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

final class CatalogImageStorage
{
    public const CATEGORY_DIRECTORY = 'categories';

    public const SUB_CATEGORY_DIRECTORY = 'sub_categories';

    public const SUB_SUB_CATEGORY_DIRECTORY = 'sub_sub_categories';

    public static function store(UploadedFile $file, string $directory): string
    {
        return $file->store($directory, 'public');
    }

    public static function delete(?string $relativePath): void
    {
        if ($relativePath === null || $relativePath === '') {
            return;
        }

        Storage::disk('public')->delete($relativePath);
    }


    public static function mergeStoredImage(
        array $validated,
        string $directory,
        ?string $previousRelativePath,
        bool $onlyWhenUploaded,
    ): array {
        $file = $validated['image'] ?? null;
        unset($validated['image']);

        if ($file instanceof UploadedFile) {
            if ($previousRelativePath !== null && $previousRelativePath !== '') {
                self::delete($previousRelativePath);
            }
            $validated['image'] = self::store($file, $directory);
        } elseif (! $onlyWhenUploaded) {
            $validated['image'] = null;
        }

        return $validated;
    }
}
