<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Property;
use App\Support\WordPressMediaResolver;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaMigrationService
{
    /**
     * @return array{downloaded: int, skipped: int, failed: int, errors: list<string>}
     */
    public function importSiteAssets(bool $dryRun = false): array
    {
        $stats = $this->emptyStats();

        foreach (['site', 'media'] as $group) {
            foreach (config("media-import.{$group}", []) as $sourceUrl => $publicPath) {
                $this->downloadToPublic($sourceUrl, $publicPath, $dryRun, $stats);
            }
        }

        return $stats;
    }

    /**
     * @return array{properties: int, downloaded: int, skipped: int, failed: int, errors: list<string>}
     */
    public function migrateProperties(?string $slug = null, bool $dryRun = false): array
    {
        $stats = [
            'properties' => 0,
            'downloaded' => 0,
            'skipped' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        $query = Property::query()->orderBy('id');

        if ($slug !== null) {
            $query->where('slug', $slug);
        }

        $query->each(function (Property $property) use ($dryRun, &$stats): void {
            $stats['properties']++;
            $directory = 'properties/'.$property->slug;
            $usedNames = [];

            $newImage = $this->migratePath(
                $property->image,
                $directory,
                $usedNames,
                $dryRun,
                $stats,
            );

            $newGallery = [];

            foreach ($property->gallery ?? [] as $item) {
                $path = is_array($item) ? ($item['url'] ?? null) : $item;
                $migrated = $this->migratePath($path, $directory, $usedNames, $dryRun, $stats);
                if (filled($migrated)) {
                    $newGallery[] = $migrated;
                }
            }

            if ($dryRun) {
                return;
            }

            $updates = [];

            if ($newImage !== null && $newImage !== $property->image) {
                $updates['image'] = $newImage;
            }

            if ($newGallery !== ($property->gallery ?? [])) {
                $updates['gallery'] = $newGallery;
            }

            if ($updates !== []) {
                $property->update($updates);
            }
        });

        return $stats;
    }

    /**
     * @param  array<string, true>  $usedNames
     * @param  array{downloaded: int, skipped: int, failed: int, errors: list<string>}|array{properties: int, downloaded: int, skipped: int, failed: int, errors: list<string>}  $stats
     */
    private function migratePath(
        ?string $path,
        string $directory,
        array &$usedNames,
        bool $dryRun,
        array &$stats,
    ): ?string {
        if (blank($path)) {
            return null;
        }

        if (! WordPressMediaResolver::isWordPressUploadUrl($path)) {
            $stats['skipped']++;

            return $path;
        }

        $filename = $this->uniqueFilename(WordPressMediaResolver::filenameFromUrl($path), $usedNames);
        $storagePath = $directory.'/'.$filename;

        if (Storage::disk('public')->exists($storagePath)) {
            $stats['skipped']++;

            return $storagePath;
        }

        if ($dryRun) {
            $stats['downloaded']++;

            return $storagePath;
        }

        try {
            $contents = $this->download(WordPressMediaResolver::productionCdnUrl($path));
            Storage::disk('public')->put($storagePath, $contents);
            $stats['downloaded']++;

            return $storagePath;
        } catch (\Throwable $exception) {
            $stats['failed']++;
            $stats['errors'][] = "{$path}: {$exception->getMessage()}";

            return $path;
        }
    }

    /**
     * @param  array{downloaded: int, skipped: int, failed: int, errors: list<string>}  $stats
     */
    private function downloadToPublic(string $sourceUrl, string $publicPath, bool $dryRun, array &$stats): void
    {
        $fullPath = public_path($publicPath);

        if (is_file($fullPath)) {
            $stats['skipped']++;

            return;
        }

        if ($dryRun) {
            $stats['downloaded']++;

            return;
        }

        try {
            $contents = $this->download($sourceUrl);
            $directory = dirname($fullPath);

            if (! is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            file_put_contents($fullPath, $contents);
            $stats['downloaded']++;
        } catch (\Throwable $exception) {
            $stats['failed']++;
            $stats['errors'][] = "{$sourceUrl}: {$exception->getMessage()}";
        }
    }

    private function download(string $url): string
    {
        $response = Http::timeout(60)
            ->retry(2, 500)
            ->withHeaders(['User-Agent' => 'Yes2BrokerMediaMigrator/1.0'])
            ->get($url);

        if (! $response->successful()) {
            throw new \RuntimeException("HTTP {$response->status()}");
        }

        $body = $response->body();

        if ($body === '') {
            throw new \RuntimeException('Empty response body');
        }

        return $body;
    }

    /**
     * @param  array<string, true>  $usedNames
     */
    private function uniqueFilename(string $filename, array &$usedNames): string
    {
        $base = pathinfo($filename, PATHINFO_FILENAME);
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $candidate = $filename;
        $index = 1;

        while (isset($usedNames[$candidate])) {
            $suffix = '-'.$index;
            $candidate = $extension !== ''
                ? Str::slug($base).$suffix.'.'.$extension
                : Str::slug($base).$suffix;
            $index++;
        }

        $usedNames[$candidate] = true;

        return $candidate;
    }

    /**
     * @return array{downloaded: int, skipped: int, failed: int, errors: list<string>}
     */
    private function emptyStats(): array
    {
        return [
            'downloaded' => 0,
            'skipped' => 0,
            'failed' => 0,
            'errors' => [],
        ];
    }
}
