<?php

namespace Database\Seeders;

use App\Data\PropertiesPageData;
use App\Data\PropertyDetailData;
use App\Models\Property;
use Illuminate\Database\Seeder;

class PropertySeeder extends Seeder
{
    /** Slugs shown in homepage "Trending Properties" carousel */
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

    public function run(): void
    {
        Property::query()->update(['is_trending' => false]);

        $imported = 0;

        foreach (PropertiesPageData::properties() as $listing) {
            $detail = PropertyDetailData::findBySlug($listing['slug']);

            if ($detail === null) {
                continue;
            }

            Property::query()->updateOrCreate(
                ['slug' => $detail['slug']],
                [
                    'title' => $detail['title'],
                    'location' => $detail['location'],
                    'bhk' => $detail['bhk'],
                    'area' => $detail['area'],
                    'possession' => $detail['possession'],
                    'price' => $detail['price'],
                    'price_min_lakhs' => Property::parsePriceMinLakhs($detail['price']),
                    'image' => $detail['image'],
                    'gallery' => $detail['gallery'],
                    'description' => $detail['description'],
                    'overview' => $detail['overview'],
                    'amenities' => $detail['amenities'],
                    'faqs' => $detail['faqs'],
                    'map_embed_url' => $detail['map_embed_url'],
                    'street_view_embed_url' => $detail['street_view_embed_url'] ?? null,
                    'brochure_url' => $detail['brochure_url'] ?? null,
                    'city' => str_contains(strtolower($detail['location']), 'gandhinagar') ? 'Gandhinagar' : 'Ahmedabad',
                    'is_new' => false,
                    'is_trending' => in_array($detail['slug'], self::TRENDING_SLUGS, true),
                    'is_active' => true,
                ]
            );

            $imported++;
        }

        if ($this->command) {
            $this->command->info("Properties imported/updated: {$imported}");
        }

        // Clean up temporary/dummy test properties
        Property::query()->where('slug', 'adarsh-aster-demo')->delete();

        // Auto-run configurations migration to populate the new database tables
        $this->command?->call('properties:migrate-configurations');
    }
}
