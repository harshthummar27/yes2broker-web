<?php

namespace App\Services;

use App\Models\Property;

class WordPressPropertyImporter
{
    private const WP_BASE = 'https://yes2broker.in';

    public function __construct(
        private readonly PropertyPageHtmlParser $htmlParser,
    ) {}

    public function import(string $slug): ?Property
    {
        $url = self::WP_BASE.'/'.$slug.'/';
        $html = @file_get_contents($url);

        if ($html === false) {
            return null;
        }

        $parsed = $this->htmlParser->parse($html, $slug);

        if (($parsed['gallery'] ?? []) === []) {
            return null;
        }

        $location = $parsed['location'] ?? null;
        $mapEmbed = $parsed['map_embed_url'] ?? null;

        if (blank($mapEmbed) && filled($location)) {
            $mapEmbed = 'https://maps.google.com/maps?q='.rawurlencode($location).'&t=m&z=14&output=embed&iwloc=near';
        }

        return Property::query()->updateOrCreate(
            ['slug' => $slug],
            [
                'title' => $parsed['title'],
                'location' => $location ?? 'Ahmedabad, Gujarat',
                'bhk' => $parsed['bhk'] ?? 'Residential Property',
                'area' => $parsed['overview']['project_area'] ?? '—',
                'possession' => $parsed['overview']['possession'] ?? 'Contact for details',
                'price' => $parsed['price'] ?? ($parsed['overview']['price_range'] ?? 'Price on request'),
                'price_min_lakhs' => Property::parsePriceMinLakhs($parsed['price'] ?? ($parsed['overview']['price_range'] ?? '')),
                'image' => $parsed['gallery'][0],
                'gallery' => $parsed['gallery'],
                'description' => $parsed['description'] ?? '',
                'overview' => $parsed['overview'],
                'amenities' => $parsed['amenities'],
                'faqs' => $parsed['faqs'],
                'map_embed_url' => $mapEmbed,
                'street_view_embed_url' => $parsed['street_view_embed_url'] ?? null,
                'brochure_url' => $parsed['brochure_url'] ?? null,
                'city' => str_contains(strtolower($location ?? ''), 'gandhinagar') ? 'Gandhinagar' : 'Ahmedabad',
                'is_active' => true,
            ]
        );
    }
}
