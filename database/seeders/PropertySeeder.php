<?php

namespace Database\Seeders;

use App\Data\PropertiesPageData;
use App\Data\PropertyDetailData;
use App\Models\Property;
use Illuminate\Database\Seeder;

class PropertySeeder extends Seeder
{
    public function run(): void
    {
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
                    'city' => str_contains(strtolower($detail['location']), 'gandhinagar') ? 'Gandhinagar' : 'Ahmedabad',
                    'is_new' => $detail['is_new'] ?? true,
                    'is_active' => true,
                ]
            );
        }
    }
}
