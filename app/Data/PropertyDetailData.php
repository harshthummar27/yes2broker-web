<?php

namespace App\Data;

class PropertyDetailData
{
    public static function findBySlug(string $slug): ?array
    {
        $listing = PropertiesPageData::findBySlug($slug);

        if ($listing === null) {
            return null;
        }

        $overrides = self::detailOverrides()[$slug] ?? [];

        return self::merge($listing, $overrides);
    }

    public static function related(string $slug, int $limit = 3): array
    {
        $current = self::findBySlug($slug);

        if ($current === null) {
            return [];
        }

        $locationNeedle = strtolower(explode(',', $current['location'])[0]);

        $related = array_values(array_filter(
            PropertiesPageData::properties(),
            fn (array $property) => $property['slug'] !== $slug
                && str_contains(strtolower($property['location']), $locationNeedle)
        ));

        if (count($related) < $limit) {
            $existingSlugs = array_column($related, 'slug');
            $existingSlugs[] = $slug;

            foreach (PropertiesPageData::properties() as $property) {
                if (in_array($property['slug'], $existingSlugs, true)) {
                    continue;
                }

                $related[] = $property;
                $existingSlugs[] = $property['slug'];

                if (count($related) >= $limit) {
                    break;
                }
            }
        }

        return array_slice($related, 0, $limit);
    }

    private static function merge(array $listing, array $overrides): array
    {
        $detail = array_merge([
            'slug' => $listing['slug'],
            'title' => $listing['title'],
            'location' => $listing['location'],
            'bhk' => $listing['bhk'],
            'area' => $listing['area'],
            'possession' => $listing['possession'],
            'price' => $listing['price'],
            'image' => $listing['image'],
            'gallery' => [$listing['image']],
            'description' => self::defaultDescription($listing),
            'overview' => self::defaultOverview($listing),
            'amenities' => self::defaultAmenities($listing),
            'faqs' => self::defaultFaqs($listing),
            'map_embed_url' => self::mapEmbedUrl($listing['location']),
            'street_view_embed_url' => null,
            'brochure_url' => null,
            'is_new' => false,
        ], $overrides);

        if (empty($detail['gallery'])) {
            $detail['gallery'] = [$listing['image']];
        }

        return $detail;
    }

    private static function detailOverrides(): array
    {
        return [
            '108-yards' => [
                'gallery' => [
                    'properties/108-yards/img889.jpg',
                    'properties/108-yards/img771.jpg',
                    'properties/108-yards/img737.jpg',
                    'properties/108-yards/img694.jpg',
                    'properties/108-yards/img646.jpg',
                    'properties/108-yards/img596.jpg',
                    'properties/108-yards/img553.jpg',
                    'properties/108-yards/img516.jpg',
                    'properties/108-yards/img433.jpg',
                ],
                'description' => '108 Yards by 9Yards is an under-construction residential project strategically located on the SP Ring Road in Shilaj, Ahmedabad North West. Launched in October 2022 with an expected possession in October 2025, this development comprises 5 buildings and a total of 244 units of premium 3 BHK Apartments. The project is designed for both safety and luxury, featuring earthquake-resistant floors, granite kitchen platforms, and 24×7 surveillance. It provides a vibrant community lifestyle with amenities like a Gymnasium, Kid\'s Pool, Banquet Hall, and an Open Air Theatre. The project is RERA-registered, and the starting price for a 3 BHK apartment is ₹ 98.60 Lakhs Onwards',
                'overview' => [
                    'project_area' => '1.44 Acres',
                    'configurations' => '3 BHK Apartments',
                    'project_size' => '5 Buildings - 244 units',
                    'launch_date' => 'Oct, 2022',
                    'price_range' => '₹ 98.60 Lakhs Onwards',
                    'possession' => 'Oct, 2025',
                    'rera_id' => 'PR/GJ/AHMEDABAD/AHMEDABAD CITY/Ahmedabad Municipal Corporation/ CAA13539/A1C/280525/300626.',
                ],
                'amenities' => [
                    'Gymnasium',
                    'Kid\'s Pool and Children\'s Play Area',
                    'Open Air Theatre',
                    'Banquet Hall and Multipurpose Hall',
                    'Cafeteria',
                    'Yoga / Meditation Area and Senior Citizen Siteout',
                    'EV Charging Point',
                    'Indoor Games and Gazebo',
                ],
                'faqs' => [
                    [
                        'question' => 'Is 108 Yards by 9Yards a RERA-registered project?',
                        'answer' => 'Yes, it is RERA-registered with the ID PR/GJ/AHMEDABAD/AHMEDABAD CITY/Ahmedabad Municipal Corporation/CAA13539/A1C/280525/300626.',
                    ],
                    [
                        'question' => 'What is the starting price for a 3 BHK apartment in this project?',
                        'answer' => 'The starting price for a 3 BHK apartment is ₹ 98.60 Lakhs Onwards.',
                    ],
                    [
                        'question' => 'When is the expected possession date for the project?',
                        'answer' => 'The project is scheduled for possession in October 2025.',
                    ],
                    [
                        'question' => 'What primary safety features does the society offer?',
                        'answer' => 'The construction includes earthquake-resistant floors and the society is equipped with 24×7 surveillance.',
                    ],
                    [
                        'question' => 'Are there facilities for large community gatherings?',
                        'answer' => 'Yes, the amenities include a Banquet Hall, Multipurpose Hall, and an Open Air Theatre.',
                    ],
                    [
                        'question' => 'Does the project support electric vehicles?',
                        'answer' => 'Yes, the project has an EV Charging Point.',
                    ],
                ],
                'map_embed_url' => 'https://maps.google.com/maps?q=108%20YARDS%203F5J%2BJPH%2C%20Shilaj%2C%20Ahmedabad%2C%20Gujarat%20380059&t=m&z=10&output=embed&iwloc=near',
            ],
            'anand-paramount' => [
                'gallery' => [
                    'properties/anand-paramount/paramount-elevation-178981100-1.webp',
                ],
                'description' => 'Anand Paramount by Anand Infinium is a luxury residential project located in Motera, Ahmedabad, just 5 minutes from Narendra Modi Stadium and metro connectivity. Spread across 1.43 acres, it offers 142 apartments in 3 towers with 3 and 4 BHK layouts. Sizes range from 277 sq. yd. to 385 sq. yd., priced between ₹1.44 Cr – ₹1.98 Cr. With possession due by December 2026, Anand Paramount features spacious layouts, large balconies, and a unique butterfly design, ensuring privacy and modern living.',
                'overview' => [
                    'project_area' => '1.43 Acres',
                    'configurations' => '3 BHK: 277 sq. yd. | 4 BHK: 385 sq. yd.',
                    'project_size' => '3 Buildings – 142 Units',
                    'launch_date' => 'October 2023',
                    'price_range' => '₹1.23 Cr – ₹1.98 Cr',
                    'possession' => 'December 2026',
                    'rera_id' => 'PR/GJ/AHMEDABAD/AHMEDABAD CITY/AUDA/MAA10827/201022',
                ],
                'amenities' => [
                    'Gymnasium',
                    'Jogging Track',
                    'Children\'s Play Area',
                    'Multipurpose Hall',
                    'Water Conservation, Rain water Harvesting',
                    '24×7 Water Supply & Power Backup',
                    'Solar Energy Provision',
                    '2/3 Car Parking with EV Charging Points',
                    'Landscaped Gardens',
                    'Corner Plot with 4-Side Open Views',
                ],
                'faqs' => [
                    [
                        'question' => 'Where is Anand Paramount located?',
                        'answer' => 'Opp. S Mall, Near Uma Party Plot, Motera, Ahmedabad.',
                    ],
                    [
                        'question' => 'What are the available configurations?',
                        'answer' => '3 BHK (277 sq. yd.) & 4 BHK (385 sq. yd.).',
                    ],
                    [
                        'question' => 'What\'s the price range?',
                        'answer' => '₹1.44 Cr – ₹1.98 Cr',
                    ],
                    [
                        'question' => 'How many units & towers are there?',
                        'answer' => '142 apartments across 3 towers.',
                    ],
                    [
                        'question' => 'When is possession scheduled?',
                        'answer' => 'December 2026.',
                    ],
                    [
                        'question' => 'What amenities are available?',
                        'answer' => 'Gym, jogging track, multipurpose hall, landscaped gardens, EV charging, children\'s play area.',
                    ],
                    [
                        'question' => 'Is the project RERA approved?',
                        'answer' => 'Yes, RERA ID PR/GJ/AHMEDABAD/AHMEDABAD CITY/AUDA/MAA10827/201022.',
                    ],
                ],
                'map_embed_url' => 'https://maps.google.com/maps?q=Anand%20ParamountBefore%20Uma%20Party%20Plot%2C%20Anand%20Paramount%2C%20opp.%20S-Mall%2C%20Motera%2C%20Ahmedabad%2C%20Gujarat%20380005&t=m&z=10&output=embed&iwloc=near',
            ],
            'elenza-gradient' => [
                'location' => 'Elenza Street, Opp. Elenza Greenwood, VIP Road, Shela, South West, Ahmedabad.',
                'bhk' => 'Shop, 3 & 4 BHK Apartments',
                'area' => '3.84 Acres',
                'possession' => 'December 2029',
                'price' => '₹82 Lakhs – ₹1.70 Cr',
                'gallery' => [
                    'properties/elenza-gradient/elenza_gradient-shela-ahmedabad-elenza_green_projets_llp.avif',
                    'properties/elenza-gradient/1920_950.jpg',
                    'properties/elenza-gradient/fs-19.avif',
                    'properties/elenza-gradient/737_6881.jpg',
                    'properties/elenza-gradient/fs-2-11.avif',
                    'properties/elenza-gradient/fs-4-9.avif',
                ],
                'description' => 'Elenza Gradient is a mixed-use project located on VIP Road, Bopal, offering both retail and residential spaces. The project includes a shop of 305 sq.ft., a 3 BHK residential unit (1725 sq.ft. priced at ₹82 L) and a 4 BHK (3500 sq.ft. priced at ₹1.70 Cr. Designed to cater to both investment and family needs, it aims to provide modern amenities and good connectivity in Bopal\'s growing neighborhood.',
                'overview' => [
                    'project_area' => '3.84 Acres',
                    'configurations' => "Shop – 305 Sq.Ft\n3 BHK – 1725 Sq.Ft\n4 BHK – 3500 Sq.Ft",
                    'project_size' => '12 Buildings - 624 units',
                    'launch_date' => 'Aug, 2023',
                    'price_range' => '₹82 Lakhs – ₹1.70 Cr',
                    'possession' => 'Dec, 2029',
                    'rera_id' => 'PR/GJ/AHMEDABAD/ AHMEDABAD CITY/AUDA/MAA12147/170823',
                ],
                'amenities' => [
                    'Common lobby & corridors',
                    'Power backup & 24×7 water supply',
                ],
                'faqs' => [
                    [
                        'question' => 'Where is it located?',
                        'answer' => 'Elenza Street, Opp. Elenza Greenwood, VIP Road, Shela, South West, Ahmedabad.',
                    ],
                    [
                        'question' => 'What are the available configurations?',
                        'answer' => 'Shop (305 sq.ft.), 3 BHK (1725 sq.ft.), 4 BHK (3500 sq.ft.)',
                    ],
                    [
                        'question' => 'What\'s the price range?',
                        'answer' => '₹82 L – ₹1.70 Cr',
                    ],
                    [
                        'question' => 'How many units & towers?',
                        'answer' => '12 Buildings – 624 units',
                    ],
                ],
                'map_embed_url' => 'https://maps.google.com/maps?q=ELENZA%C2%AE%20-%20GRADIENT%20Elenza%20Street%2C%20VIP%20Rd%2C%20Shela%2C%20Gujarat%20380057&t=m&z=10&output=embed&iwloc=near',
                'street_view_embed_url' => 'https://www.google.com/maps/embed?pb=!4v1759653551854!6m8!1m7!1s0kEbh_DaQg3bvzxBw8Uppg!2m2!1d23.01536914923802!2d72.4510727326136!3f283.20966!4f0!5f0.7820865974627469',
            ],
        ];
    }

    private static function defaultDescription(array $listing): string
    {
        return sprintf(
            '%s is a premium real estate project located at %s. Offering %s across %s, this development is designed for modern living with thoughtfully planned layouts and essential lifestyle amenities. With possession expected by %s and prices starting at %s, it presents an excellent opportunity for homebuyers and investors in Ahmedabad & Gandhinagar.',
            $listing['title'],
            rtrim($listing['location'], '.'),
            $listing['bhk'],
            $listing['area'],
            $listing['possession'],
            $listing['price']
        );
    }

    private static function defaultOverview(array $listing): array
    {
        return [
            'project_area' => $listing['area'],
            'configurations' => $listing['bhk'],
            'project_size' => 'Contact for details',
            'launch_date' => 'Contact for details',
            'price_range' => $listing['price'],
            'possession' => $listing['possession'],
            'rera_id' => 'Available on request',
        ];
    }

    private static function defaultAmenities(array $listing): array
    {
        $type = strtolower($listing['bhk']);

        if (str_contains($type, 'commercial') || str_contains($type, 'shop') || str_contains($type, 'office')) {
            return [
                'High Street Frontage',
                'Ample Parking',
                '24×7 Security',
                'Power Backup',
                'Wide Frontage & Visibility',
            ];
        }

        if (str_contains($type, 'plot') || str_contains($type, 'land')) {
            return [
                'Gated Community',
                'Internal Roads',
                'Drainage & Water Supply',
                'Street Lighting',
                'Green Open Spaces',
            ];
        }

        return [
            'Gymnasium',
            'Children\'s Play Area',
            '24×7 Security',
            'Power Backup',
            'Landscaped Gardens',
            'Clubhouse / Multipurpose Hall',
            'Parking',
            'EV Charging Point',
        ];
    }

    private static function defaultFaqs(array $listing): array
    {
        return [
            [
                'question' => 'Where is '.$listing['title'].' located?',
                'answer' => $listing['location'],
            ],
            [
                'question' => 'What configurations are available?',
                'answer' => $listing['bhk'].' with a project area of '.$listing['area'].'.',
            ],
            [
                'question' => 'What is the price range?',
                'answer' => $listing['price'],
            ],
            [
                'question' => 'When is possession expected?',
                'answer' => $listing['possession'],
            ],
            [
                'question' => 'How can I schedule a site visit?',
                'answer' => 'Fill out the inquiry form on this page or call '.config('site.phone').' and our team will arrange a visit.',
            ],
        ];
    }

    private static function mapEmbedUrl(string $location): string
    {
        return 'https://maps.google.com/maps?q='.rawurlencode($location).'&t=m&z=12&output=embed&iwloc=near';
    }
}
