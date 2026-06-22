<?php

namespace App\Data;

use App\Support\SiteAsset;

class HomePageData
{
    public static function usps(): array
    {
        return [
            [
                'title' => '₹1,00,000 Cashback',
                'description' => 'Buy your new home through <strong>Yes2Broker</strong> and get an exclusive cashback when registering property under a <strong>woman\'s name</strong>.',
                'icon' => 'home',
            ],
            [
                'title' => 'Lowest Price',
                'description' => 'We bring you the most competitive prices in the market no hidden charges, no surprises, just honest value for your dream property.',
                'icon' => 'agreement',
            ],
            [
                'title' => 'Dedicated Manager',
                'description' => 'From your first inquiry to final paperwork, your Dedicated Manager ensures a smooth and guided buying journey.',
                'icon' => 'support',
            ],
        ];
    }

    public static function partners(): array
    {
        return [
            ['name' => 'Shivalik', 'logo' => SiteAsset::url('images/media/2025/07/shivalik-1.webp')],
            ['name' => 'Shahasya Group', 'logo' => SiteAsset::url('images/media/2025/07/shahasya-group.webp')],
            ['name' => 'Shree Siddhi Group', 'logo' => SiteAsset::url('images/media/2025/07/shree-siddhi-group.webp')],
            ['name' => 'Parshwa', 'logo' => SiteAsset::url('images/media/2025/07/parshwa.webp')],
            ['name' => 'Vanshikaa', 'logo' => SiteAsset::url('images/media/2025/07/vanshikaa.webp')],
        ];
    }

    public static function locations(): array
    {
        return [
            'Motera', 'Zundal', 'Science City', 'Shela', 'Chandkheda',
            'South Bopal', 'Gota', 'Shilaj', 'Thaltej', 'Gandhinagar',
        ];
    }

    public static function propertyTypes(): array
    {
        return [
            '' => 'Select Type',
            'apartment' => 'Apartment',
            'villa' => 'Villa',
            'home' => 'Home',
            'bungalow' => 'Bungalow',
            'office' => 'Office',
            'showroom' => 'Showroom',
            'shop' => 'Shop',
            'farmhouse' => 'FarmHouse',
            'land' => 'Land',
        ];
    }

    public static function budgets(): array
    {
        return [
            '' => 'Select Budget',
            '50l' => 'Up to ₹50 Lac',
            '60l' => 'Up to ₹60 Lac',
            '70l' => 'Up to ₹70 Lac',
            '80l' => 'Up to ₹80 Lac',
            '90l' => 'Up to ₹90 Lac',
            '1cr' => 'Up to ₹1 Cr',
            '2cr' => 'Up to ₹2 Cr',
            '5cr' => 'Up to ₹5 Cr',
            '10cr' => 'Up to ₹10 Cr',
        ];
    }

    public static function budgetMaxLakhs(string $budget): ?float
    {
        return match ($budget) {
            '50l' => 50,
            '60l' => 60,
            '70l' => 70,
            '80l' => 80,
            '90l' => 90,
            '1cr' => 100,
            '2cr' => 200,
            '5cr' => 500,
            '10cr' => 1000,
            default => null,
        };
    }

    public static function sortOptions(): array
    {
        return [
            'relevance' => 'Relevance',
            'price_asc' => 'Price: Low to High',
            'price_desc' => 'Price: High to Low',
            'newest' => 'Newest First',
        ];
    }

    public static function consultationOptions(): array
    {
        return [
            'Apartment - 1 BHK',
            'Apartment - 2 BHK',
            'Apartment - 3 BHK',
            'Commercial Space',
            'Plot',
            'Bungalow',
            'Other',
        ];
    }
}
