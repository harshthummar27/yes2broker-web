<?php

declare(strict_types=1);

namespace App\Services\Legacy;

use App\Data\PropertyDetailData;
use App\Models\Property;
use App\Services\PropertyPageHtmlParser;
use App\Services\PropertyStorageSyncService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CsvPropertyImportService
{
    private const TRENDING_SLUGS = [
        'anand-paramount',
        'vivaan-essence',
        'adarsh-aster',
        'avirat-giriraj',
        'amrut-orchid',
        'binori-aristella',
        'dev-amrakunj-platinum',
        'elenza-arista',
        'ganesh-legacy',
        'hr-eliseo-ii',
        'shaligram-prestige',
        'riviera-majestica',
    ];

    public function __construct(
        private readonly WordPressExportReader $exportReader,
        private readonly PropertyPageHtmlParser $htmlParser,
        private readonly PropertyStorageSyncService $storageSync,
    ) {}

    /**
     * @return array{imported: int, images_preserved: int, skipped: int}
     */
    public function import(?callable $progress = null): array
    {
        $exportIndex = $this->exportReader->indexBySlug();

        Property::query()->update(['is_trending' => false]);

        $imported = 0;
        $imagesPreserved = 0;
        $skipped = 0;

        foreach ($exportIndex as $slug => $row) {
            $title = trim((string) ($row['Title'] ?? ''));

            if ($slug === '' || $title === '') {
                $skipped++;

                continue;
            }

            $detail = PropertyDetailData::findBySlug($slug);

            if ($detail === null) {
                $skipped++;

                continue;
            }

            $excerpt = $this->htmlParser->parseListingExcerpt((string) ($row['Excerpt'] ?? ''));
            $metadata = $this->parseContentMetadata((string) ($row['Content'] ?? ''));

            $location = $this->firstFilled(
                $excerpt['location'] ?? null,
                $detail['location'] ?? null,
            ) ?? 'Ahmedabad, Gujarat';

            $bhk = $this->firstFilled(
                $excerpt['bhk'] ?? null,
                $detail['bhk'] ?? null,
            ) ?? 'Residential Property';

            $area = $this->firstFilled(
                $excerpt['area'] ?? null,
                $detail['area'] ?? null,
            ) ?? '—';

            $possession = $this->firstFilled(
                $excerpt['possession'] ?? null,
                $detail['possession'] ?? null,
            ) ?? 'Contact for details';

            $price = $this->sanitizePrice($this->firstFilled(
                $excerpt['price'] ?? null,
                $metadata['budget'] ?? null,
                $detail['price'] ?? null,
            )) ?? 'Price on request';

            $existing = Property::query()->where('slug', $slug)->first();
            $preserveMedia = $existing !== null && $this->hasLocalMedia($existing);

            $image = $preserveMedia
                ? (string) $existing->image
                : ($detail['image'] ?? null);

            $gallery = $preserveMedia
                ? (array) ($existing->gallery ?? [])
                : (array) ($detail['gallery'] ?? []);

            if ($preserveMedia) {
                $imagesPreserved++;
            }

            $overview = is_array($detail['overview'] ?? null) ? $detail['overview'] : [];
            $overview['project_area'] = $area;
            $overview['configurations'] = $bhk;
            $overview['price_range'] = $price;
            $overview['possession'] = $possession;

            $mapEmbed = $detail['map_embed_url'] ?? null;

            if (blank($mapEmbed) && filled($location)) {
                $mapEmbed = 'https://maps.google.com/maps?q='.rawurlencode($location).'&t=m&z=14&output=embed&iwloc=near';
            }

            Property::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'title' => $title,
                    'location' => $location,
                    'bhk' => $bhk,
                    'area' => $area,
                    'possession' => $possession,
                    'price' => $price,
                    'price_min_lakhs' => Property::parsePriceMinLakhs($price),
                    'image' => $image,
                    'gallery' => $gallery,
                    'description' => (string) ($detail['description'] ?? ''),
                    'overview' => $overview,
                    'amenities' => is_array($detail['amenities'] ?? null) ? $detail['amenities'] : [],
                    'faqs' => is_array($detail['faqs'] ?? null) ? $detail['faqs'] : [],
                    'map_embed_url' => $mapEmbed,
                    'street_view_embed_url' => $detail['street_view_embed_url'] ?? null,
                    'brochure_url' => $detail['brochure_url'] ?? null,
                    'city' => $this->resolveCity($location, $metadata['city'] ?? null),
                    'property_type' => $this->resolvePropertyType($bhk, $metadata['type'] ?? null),
                    'is_new' => false,
                    'is_trending' => in_array($slug, self::TRENDING_SLUGS, true),
                    'is_active' => strtolower((string) ($row['Status_2'] ?? 'publish')) === 'publish',
                ]
            );

            $imported++;

            if ($progress) {
                $progress($slug);
            }
        }

        $this->storageSync->syncFromStorage();

        return [
            'imported' => $imported,
            'images_preserved' => $imagesPreserved,
            'skipped' => $skipped,
        ];
    }

    /**
     * @return array{city: ?string, type: ?string, budget: ?string}
     */
    private function parseContentMetadata(string $content): array
    {
        $metadata = [
            'city' => null,
            'type' => null,
            'budget' => null,
        ];

        foreach (preg_split('/\R+/', trim($content)) ?: [] as $line) {
            if (! str_contains($line, ':')) {
                continue;
            }

            [$key, $value] = array_map('trim', explode(':', $line, 2));
            $key = strtolower($key);

            if ($key === 'city') {
                $metadata['city'] = $value;
            } elseif ($key === 'type') {
                $metadata['type'] = $value;
            } elseif ($key === 'budget') {
                $metadata['budget'] = $value;
            }
        }

        return $metadata;
    }

    private function hasLocalMedia(Property $property): bool
    {
        $image = ltrim((string) $property->image, '/');

        if (
            filled($image)
            && ! str_starts_with($image, 'http')
            && Storage::disk('public')->exists($image)
        ) {
            return true;
        }

        foreach ((array) ($property->gallery ?? []) as $item) {
            $path = ltrim(is_array($item) ? (string) ($item['url'] ?? '') : (string) $item, '/');

            if (
                filled($path)
                && ! str_starts_with($path, 'http')
                && Storage::disk('public')->exists($path)
            ) {
                return true;
            }
        }

        return false;
    }

    private function firstFilled(mixed ...$values): ?string
    {
        foreach ($values as $value) {
            $text = is_string($value) ? trim(html_entity_decode($value)) : null;

            if (filled($text) && $text !== '—') {
                return $text;
            }
        }

        return null;
    }

    private function resolveCity(string $location, ?string $metadataCity): string
    {
        if (filled($metadataCity)) {
            return $metadataCity;
        }

        return str_contains(strtolower($location), 'gandhinagar') ? 'Gandhinagar' : 'Ahmedabad';
    }

    private function resolvePropertyType(string $bhk, ?string $metadataType): string
    {
        if (filled($metadataType)) {
            return $metadataType;
        }

        $type = strtolower($bhk);

        return match (true) {
            str_contains($type, 'villa') => 'Villa',
            str_contains($type, 'bungalow') => 'Bungalow',
            str_contains($type, 'plot'), str_contains($type, 'land') => 'Land',
            str_contains($type, 'shop'), str_contains($type, 'showroom'), str_contains($type, 'office'), str_contains($type, 'commercial') => 'Office',
            str_contains($type, 'farmhouse') => 'FarmHouse',
            default => 'Apartment',
        };
    }

    private function sanitizePrice(?string $price): ?string
    {
        if (blank($price)) {
            return null;
        }

        $price = trim(html_entity_decode($price));

        if (preg_match('/₹\s*[\d.,]+\s*(?:Lakhs?|L\b|Cr)(?:\s*[–-]\s*₹\s*[\d.,]+\s*(?:Lakhs?|L\b|Cr))?/iu', $price, $match)) {
            return trim($match[0]);
        }

        if (preg_match('/₹\s*[\d.,]+\s*(?:Lakhs?|L\b|Cr)\s*Onwards/iu', $price, $match)) {
            return trim($match[0]);
        }

        return Str::limit($price, 120, '…');
    }
}
