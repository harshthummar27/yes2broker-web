<?php

namespace App\Services\Legacy;

use App\Models\Property;
use App\Services\PropertyPageHtmlParser;
use Illuminate\Support\Str;

class LegacyPropertyImporter
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
        private readonly LegacySqlReader $sqlReader,
        private readonly WordPressPostsReader $postsReader,
        private readonly WordPressExportReader $exportReader,
        private readonly PropertyPageHtmlParser $htmlParser,
    ) {}

    /**
     * @return array{imported: int, with_detail_page: int, skipped: int, failed: int}
     */
    public function import(?callable $progress = null): array
    {
        $baseRecords = $this->sqlReader->records();
        $slugs = $baseRecords->pluck('slug')->all();
        $wpIndex = $this->postsReader->index($slugs);
        $exportIndex = $this->exportReader->indexBySlug();

        Property::query()->update(['is_trending' => false]);

        $imported = 0;
        $withDetailPage = 0;
        $skipped = 0;
        $failed = 0;

        foreach ($baseRecords as $base) {
            try {
                $slug = (string) $base->slug;
            $export = $exportIndex[$slug] ?? null;
            $page = $wpIndex['pages'][$slug] ?? null;
            $parsed = $page ? $this->htmlParser->parse($page['content'], $slug, $page['title']) : [];
            $excerpt = $export ? $this->htmlParser->parseListingExcerpt((string) ($export['Excerpt'] ?? '')) : [];

            $gallery = $this->resolveGallery($base, $page, $parsed, $wpIndex['attachments']);
            $overview = $this->normalizeJsonField($base->overview);
            $amenities = $this->normalizeJsonField($base->amenities);
            $faqs = $this->normalizeJsonField($base->faqs);

            if (! empty($parsed['overview'])) {
                $overview = array_merge($overview, $parsed['overview']);
            }

            if (! empty($parsed['amenities'])) {
                $amenities = $parsed['amenities'];
            }

            if (! empty($parsed['faqs'])) {
                $faqs = $parsed['faqs'];
            }

            $location = $this->firstFilled(
                $parsed['location'] ?? null,
                $base->location ?? null,
                $excerpt['location'] ?? null,
            );

            $bhk = $this->firstFilled(
                $parsed['bhk'] ?? null,
                $this->decodeText($base->bhk ?? null),
                $excerpt['bhk'] ?? null,
            );

            $area = $this->firstFilled(
                $overview['project_area'] ?? null,
                $base->area ?? null,
                $excerpt['area'] ?? null,
            );

            $possession = $this->firstFilled(
                $overview['possession'] ?? null,
                $base->possession ?? null,
                $excerpt['possession'] ?? null,
            );

            $price = $this->sanitizePrice($this->firstFilled(
                $overview['price_range'] ?? null,
                $base->price ?? null,
                $excerpt['price'] ?? null,
                $parsed['price'] ?? null,
            ));

            $description = $this->firstFilled(
                $parsed['description'] ?? null,
                $this->cleanDescription((string) ($base->description ?? '')),
            );

            $image = $this->firstFilled(
                $gallery[0] ?? null,
                $base->image ?? null,
                $export['Image URL'] ?? null,
            );

            $mapEmbed = $this->firstFilled(
                $parsed['map_embed_url'] ?? null,
                $base->map_embed_url ?? null,
            );

            if (blank($mapEmbed) && filled($location)) {
                $mapEmbed = 'https://maps.google.com/maps?q='.rawurlencode($location).'&t=m&z=14&output=embed&iwloc=near';
            }

            if (blank($slug) || blank($base->title)) {
                $skipped++;

                continue;
            }

            Property::withoutEvents(function () use (
                $base,
                $slug,
                $parsed,
                $gallery,
                $image,
                $location,
                $bhk,
                $area,
                $possession,
                $price,
                $description,
                $overview,
                $amenities,
                $faqs,
                $mapEmbed,
            ): void {
                Property::query()->updateOrCreate(
                    ['slug' => $slug],
                    [
                        'title' => $this->decodeText((string) $base->title),
                        'location' => $location ?? 'Ahmedabad, Gujarat',
                        'bhk' => $bhk ?? 'Residential Property',
                        'area' => $area ?? '—',
                        'possession' => $possession ?? 'Contact for details',
                        'price' => $price ?? 'Price on request',
                        'price_min_lakhs' => Property::parsePriceMinLakhs($price ?? ''),
                        'image' => $image,
                        'gallery' => $gallery,
                        'description' => $description ?? '',
                        'overview' => $overview,
                        'amenities' => $amenities,
                        'faqs' => $faqs,
                        'map_embed_url' => $mapEmbed,
                        'street_view_embed_url' => $parsed['street_view_embed_url'] ?? null,
                        'brochure_url' => $parsed['brochure_url'] ?? null,
                        'city' => $this->resolveCity($location ?? (string) ($base->city ?? '')),
                        'property_type' => $base->property_type ?: 'Apartment',
                        'is_new' => (bool) ($base->is_new ?? true),
                        'is_trending' => in_array($slug, self::TRENDING_SLUGS, true),
                        'is_active' => (bool) ($base->is_active ?? true),
                    ]
                );
            });

            $imported++;

            if ($page !== null) {
                $withDetailPage++;
            }

            if ($progress) {
                $progress($slug, $page !== null);
            }
            } catch (\Throwable $exception) {
                $failed++;
                report($exception);
            }
        }

        $this->sqlReader->cleanup();

        return [
            'imported' => $imported,
            'with_detail_page' => $withDetailPage,
            'skipped' => $skipped,
            'failed' => $failed,
        ];
    }

    /**
     * @param  array{id: int, title: string, content: string}|null  $page
     * @param  array<string, mixed>  $parsed
     * @param  array<int, list<string>>  $attachments
     * @return list<string>
     */
    private function resolveGallery(object $base, ?array $page, array $parsed, array $attachments): array
    {
        $gallery = [];

        if ($page !== null && isset($attachments[$page['id']])) {
            $gallery = array_values(array_unique(array_map(
                fn (string $url) => strtok($url, '?') ?: $url,
                $attachments[$page['id']]
            )));
        }

        if ($gallery === [] && ! empty($parsed['gallery'])) {
            $gallery = $parsed['gallery'];
        }

        if ($gallery === []) {
            $gallery = $this->normalizeJsonField($base->gallery);
        }

        if ($gallery === [] && filled($base->image)) {
            $gallery = [(string) $base->image];
        }

        return array_values(array_filter($gallery));
    }

    /**
     * @return array<int|string, mixed>
     */
    private function normalizeJsonField(mixed $value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if (! is_string($value) || trim($value) === '' || trim($value) === '[]') {
            return [];
        }

        $decoded = json_decode($value, true);

        return is_array($decoded) ? $decoded : [];
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

    private function cleanDescription(string $description): ?string
    {
        $description = trim($description);

        if ($description === '') {
            return null;
        }

        if (str_starts_with($description, 'city:') || str_contains($description, '📍')) {
            return null;
        }

        return $description;
    }

    private function decodeText(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return trim(html_entity_decode($value));
    }

    private function resolveCity(string $location): string
    {
        return str_contains(strtolower($location), 'gandhinagar') ? 'Gandhinagar' : 'Ahmedabad';
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
