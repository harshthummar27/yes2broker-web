<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\PropertiesPageData;
use App\Data\PropertyDetailData;
use App\Models\Property;
use App\Support\WordPressMediaResolver;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class SeedMediaSyncService
{
    /**
     * @return array{properties: int, details: int}
     */
    public function syncFromDatabase(): array
    {
        $propertiesUpdated = $this->syncPropertiesPageData();
        $detailsUpdated = $this->syncPropertyDetailData();

        return [
            'properties' => $propertiesUpdated,
            'details' => $detailsUpdated,
        ];
    }

    public function syncPropertiesPageData(): int
    {
        $path = app_path('Data/PropertiesPageData.php');
        $content = File::get($path);
        $updated = 0;

        foreach (PropertiesPageData::properties() as $listing) {
            $slug = $listing['slug'];
            $oldImage = $listing['image'] ?? '';

            if (! WordPressMediaResolver::isWordPressUploadUrl($oldImage)) {
                continue;
            }

            $property = Property::query()->where('slug', $slug)->first();
            $newImage = $property?->image;

            if (
                blank($newImage)
                || WordPressMediaResolver::isWordPressUploadUrl($newImage)
            ) {
                continue;
            }

            $replaced = str_replace($oldImage, $newImage, $content, $count);

            if ($count > 0) {
                $content = $replaced;
                $updated += $count;
            }
        }

        if ($updated > 0) {
            File::put($path, $content);
        }

        return $updated;
    }

    public function syncPropertyDetailData(): int
    {
        $path = app_path('Data/PropertyDetailData.php');
        $content = File::get($path);
        $updated = 0;

        $reflection = new \ReflectionClass(PropertyDetailData::class);
        $method = $reflection->getMethod('detailOverrides');
        $method->setAccessible(true);
        /** @var array<string, array<string, mixed>> $overrides */
        $overrides = $method->invoke(null);

        foreach ($overrides as $slug => $detail) {
            $gallery = $detail['gallery'] ?? [];

            if (! is_array($gallery) || $gallery === []) {
                continue;
            }

            $wordPressUrls = array_values(array_filter(
                $gallery,
                fn ($url) => is_string($url) && WordPressMediaResolver::isWordPressUploadUrl($url)
            ));

            if ($wordPressUrls === []) {
                continue;
            }

            $property = Property::query()->where('slug', $slug)->first();
            $localGallery = [];

            if ($property !== null && is_array($property->gallery)) {
                $localGallery = array_values(array_filter(
                    $property->gallery,
                    fn ($url) => is_string($url) && ! WordPressMediaResolver::isWordPressUploadUrl($url)
                ));
            }

            if ($localGallery === []) {
                foreach ($wordPressUrls as $url) {
                    $localPath = $this->downloadGalleryImage($slug, $url);
                    if ($localPath !== null) {
                        $localGallery[] = $localPath;
                    }
                }
            }

            if ($localGallery === []) {
                continue;
            }

            $oldBlock = $this->formatGalleryBlock($wordPressUrls);
            $newBlock = $this->formatGalleryBlock($localGallery);
            $replaced = str_replace($oldBlock, $newBlock, $content, $count);

            if ($count > 0) {
                $content = $replaced;
                $updated += $count;
            } else {
                foreach ($wordPressUrls as $index => $oldUrl) {
                    $replacement = $localGallery[$index] ?? $localGallery[0] ?? null;

                    if (! is_string($replacement)) {
                        continue;
                    }

                    $replaced = str_replace($oldUrl, $replacement, $content, $count);

                    if ($count > 0) {
                        $content = $replaced;
                        $updated += $count;
                    }
                }
            }
        }

        if ($updated > 0) {
            File::put($path, $content);
        }

        return $updated;
    }

    private function downloadGalleryImage(string $slug, string $url): ?string
    {
        $filename = WordPressMediaResolver::filenameFromUrl($url);
        $storagePath = 'properties/'.$slug.'/'.$filename;

        if (! Storage::disk('public')->exists($storagePath)) {
            try {
                $response = Http::timeout(60)
                    ->retry(2, 500)
                    ->withHeaders(['User-Agent' => 'Yes2BrokerMediaMigrator/1.0'])
                    ->get(WordPressMediaResolver::productionCdnUrl($url));

                if (! $response->successful() || $response->body() === '') {
                    return null;
                }

                Storage::disk('public')->put($storagePath, $response->body());
            } catch (\Throwable) {
                return null;
            }
        }

        return $storagePath;
    }

    /**
     * @param  list<string>  $urls
     */
    private function formatGalleryBlock(array $urls): string
    {
        $lines = array_map(
            fn (string $url): string => "                    '".$url."',",
            $urls
        );

        return "'gallery' => [\n".implode("\n", $lines)."\n                ],";
    }
}
