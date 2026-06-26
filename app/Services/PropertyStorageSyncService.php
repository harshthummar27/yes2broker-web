<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Property;
use Illuminate\Support\Facades\Storage;

class PropertyStorageSyncService
{
    /** @var list<string> */
    private const IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'avif', 'bmp'];

    /**
     * @return array{updated: int, unchanged: int, missing_folder: int, empty_folder: int}
     */
    public function syncFromStorage(): array
    {
        $stats = [
            'updated' => 0,
            'unchanged' => 0,
            'missing_folder' => 0,
            'empty_folder' => 0,
        ];

        Property::query()
            ->orderBy('id')
            ->each(function (Property $property) use (&$stats): void {
                $directory = 'properties/'.$property->slug;

                if (! Storage::disk('public')->exists($directory)) {
                    $stats['missing_folder']++;

                    return;
                }

                $paths = $this->imagePathsInDirectory($directory);

                if ($paths === []) {
                    $stats['empty_folder']++;

                    return;
                }

                $featured = $this->resolveFeaturedImage($property->image, $paths);
                $gallery = $this->buildGallery($featured, $paths);

                if ($property->image === $featured && $property->gallery === $gallery) {
                    $stats['unchanged']++;

                    return;
                }

                $property->image = $featured;
                $property->gallery = $gallery;
                $property->saveQuietly();
                $stats['updated']++;
            });

        return $stats;
    }

    /**
     * @return list<string>
     */
    private function imagePathsInDirectory(string $directory): array
    {
        $paths = [];

        foreach (Storage::disk('public')->files($directory) as $file) {
            $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));

            if (in_array($extension, self::IMAGE_EXTENSIONS, true)) {
                $paths[] = $file;
            }
        }

        sort($paths, SORT_NATURAL | SORT_FLAG_CASE);

        return $paths;
    }

    /**
     * @param  list<string>  $paths
     */
    private function resolveFeaturedImage(?string $current, array $paths): string
    {
        $normalized = ltrim((string) $current, '/');

        if (
            filled($normalized)
            && in_array($normalized, $paths, true)
            && Storage::disk('public')->exists($normalized)
        ) {
            return $normalized;
        }

        return $paths[0];
    }

    /**
     * @param  list<string>  $paths
     * @return list<string>
     */
    private function buildGallery(string $featured, array $paths): array
    {
        $gallery = array_values(array_unique(array_merge([$featured], $paths)));

        return $gallery;
    }
}
