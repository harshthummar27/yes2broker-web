<?php

namespace App\Data;

class HomePageData
{
    public static function usps(): array
    {
        return [
            [
                'title' => 'test',
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

    public static function trendingProperties(): array
    {
        $base = config('site.media_url');

        return [
            [
                'slug' => 'anand-paramount',
                'title' => 'Anand Paramount',
                'image' => "{$base}/2025/11/paramount-elevation-178981100.webp",
                'location' => 'Opp. S Mall, Near Uma Party Plot, Motera, Ahmedabad – 380005',
                'bhk' => '3 & 4 BHK Apartments',
                'area' => '1.43 Acres',
                'possession' => 'December 2026',
                'price' => '₹1.23 Cr – ₹1.98 Cr',
            ],
            [
                'slug' => 'vivaan-essence',
                'title' => 'Vivaan Essence',
                'image' => "{$base}/2025/11/Untitled-design.jpg",
                'location' => 'Behind Sangath Prominence, Opp. CBD Mall, Between Divine Circle & Vaishnodevi Circle',
                'bhk' => '2 BHK Apartments',
                'area' => '0.76 Acres',
                'possession' => 'June 2026',
                'price' => '₹60.31 L – ₹74.98 L',
            ],
            [
                'slug' => 'adarsh-aster',
                'title' => 'Adarsh Aster',
                'image' => "{$base}/2025/09/img63-scaled.jpg",
                'location' => 'Zundal, North West Ahmedabad – 382421',
                'bhk' => '2 & 3 BHK Apartment',
                'area' => '0.73 Acres',
                'possession' => 'December 2027',
                'price' => '₹61.5 Lakhs Onwards',
            ],
            [
                'slug' => 'avirat-giriraj',
                'title' => 'Avirat Giriraj',
                'image' => "{$base}/2025/09/night-corner-low-res.webp",
                'location' => 'Nr Amrakunj Arastu, Opp Panchshlok Homes, Near Green Ora Cross Road, Zundal, North West Ahmedabad – 382421',
                'bhk' => '2 & 3 BHK Apartment',
                'area' => '1.69 Acres',
                'possession' => 'July 2027',
                'price' => '₹59 Lakhs ~ ₹79 Lakhs',
            ],
            [
                'slug' => 'amrut-orchid',
                'title' => 'Amrut Orchid',
                'image' => "{$base}/2025/09/2023-12-11.webp",
                'location' => 'Near H.B. Kapadiya School, TP-44, Chandkheda, Ahmedabad – 382424',
                'bhk' => '3 BHK Apartments',
                'area' => '1.1 Acres',
                'possession' => 'March 2026',
                'price' => '₹78.5 Lakhs Onwards',
            ],
            [
                'slug' => 'binori-aristella',
                'title' => 'Binori Aristella',
                'image' => "{$base}/2025/09/RS-PC.jpg",
                'location' => 'Science Park, Sardar Patel Ring Road, Science City, Ahmedabad – 380060',
                'bhk' => '4 & 5 BHK Apartments',
                'area' => '0.82 Acres',
                'possession' => 'Ready to Move',
                'price' => '₹4.32 Cr – ₹7 Cr',
            ],
            [
                'slug' => 'dev-amrakunj-platinum',
                'title' => 'Dev Amrakunj Platinum',
                'image' => "{$base}/2025/09/unnamed-6.webp",
                'location' => 'Nr. Tapovan Circle, Chandkheda, North West Ahmedabad – 382424',
                'bhk' => '3 & 4 BHK Apartments',
                'area' => '0.81 Acres',
                'possession' => 'March 2026',
                'price' => '₹1.35 Cr – ₹2.10 Cr',
            ],
            [
                'slug' => 'elenza-arista',
                'title' => 'Elenza Arista',
                'image' => "{$base}/2025/09/Project-Photo-2-Shaligram-Pride-Ahmedabad-5426175_1690_1600.jpg",
                'location' => 'Nr. Shypram Flat, Next To Joy Box, Sypram To Vip Road, South Bopal, Ahmedabad – 380058',
                'bhk' => '3 BHK Apartments',
                'area' => '0.8 Acres',
                'possession' => 'December 2029',
                'price' => '₹94 Lakhs Onwards',
            ],
            [
                'slug' => 'the-ganesh-legacy',
                'title' => 'The Ganesh Legacy',
                'image' => "{$base}/2025/09/img94-2.jpg",
                'location' => 'Near Auda Garden, Beside Tragad Underpass, Near Vaishno Devi Circle, SG Highway, Ahmedabad – 382421',
                'bhk' => '3 & 4 BHK Apartments',
                'area' => '1.63 Acres',
                'possession' => 'July 2027',
                'price' => '₹1.38 Cr – ₹1.75 Cr',
            ],
            [
                'slug' => 'hr-eliseo-ii',
                'title' => 'HR Eliseo II',
                'image' => "{$base}/2025/09/img63-scaled.jpg",
                'location' => 'Off Cliantha Road, Shela, South West Ahmedabad – 380058',
                'bhk' => '4 BHK Apartments',
                'area' => '0.91 Acres',
                'possession' => 'June 2027',
                'price' => '₹2.38 Cr Onwards',
            ],
            [
                'slug' => 'shaligram-prestige',
                'title' => 'Shaligram Prestige',
                'image' => "{$base}/2025/09/Project-Photo-2-Shaligram-Pride-Ahmedabad-5426175_1690_1600.jpg",
                'location' => 'Shela, Ahmedabad – 380058',
                'bhk' => '3 BHK Apartments',
                'area' => '3.95 Acres',
                'possession' => 'July 2030',
                'price' => '₹1.1 Cr Onwards',
            ],
            [
                'slug' => 'riviera-majestica',
                'title' => 'Riviera Majestica',
                'image' => "{$base}/2025/09/night-corner-low-res.webp",
                'location' => 'Opp. Skycity Township, Khadiya, Shela, South West Ahmedabad',
                'bhk' => '4 & 5 BHK Apartments',
                'area' => '4.02 Acres',
                'possession' => 'December 2027',
                'price' => '₹2.27 Cr – ₹7.41 Cr',
            ],
        ];
    }

    public static function partners(): array
    {
        $base = config('site.media_url');

        return [
            ['name' => 'Shivalik', 'logo' => "{$base}/2025/07/shivalik-1.webp"],
            ['name' => 'Shahasya Group', 'logo' => "{$base}/2025/07/Shahasya-group-1024x724-1-1.webp"],
            ['name' => 'Shree Siddhi Group', 'logo' => "{$base}/2025/07/shree-siddhi-group-1-1.webp"],
            ['name' => 'Parshwa', 'logo' => "{$base}/2025/07/parshwa-1-1.webp"],
            ['name' => 'Vanshikaa', 'logo' => "{$base}/2025/07/vanshikaa-1024x398-1-1.webp"],
        ];
    }

    public static function locations(): array
    {
        return [
            'Motera', 'Zundal', 'Science City', 'Shela', 'Chandkheda',
            'South Bopal', 'Gota', 'Shilaj', 'Thaltej', 'Gandhinagar',
        ];
    }

    public static function localities(): array
    {
        return [
            'Ambali' => [
                'Akshar Ocean Pearl', 'Oeuvre 3', 'Satyamev Luxor', 'The Bellagio', 'Westlands',
                'Ayaan', 'Oriental Viola', 'Shaligram Luxuria', 'The Kimana Tower',
            ],
            'Science City' => [
                'Binori Belmont', 'Palak Elina', 'Sheetal Gharana', 'The Waterfall',
                'Indraprasth Shivanta', 'Royce One', 'Skydeck Select', 'The Whitecraft',
                'Manor Ananda', 'Sanctum', 'Sun Sky Park', 'Tranquil',
            ],
        ];
    }

    public static function featuredCarousel(): array
    {
        $base = config('site.media_url');

        return [
            [
                'title' => 'Anand Paramount',
                'image' => "{$base}/2025/07/Screenshot_20-6-2025_17017_.webp",
                'address' => 'Opp S Mall, Near Uma Party Plot, Motera, North West, Ahmedabad',
                'postcode' => '380005',
            ],
            [
                'title' => 'Shubhang Shyam Zircon',
                'image' => "{$base}/2025/07/Screenshot_20-6-2025_17017_.webp",
                'address' => 'Opp. Cbd Mall, Near Pushkar Residency, S.p. Ring Road, Zundal, Ahmedabad',
                'postcode' => '382421',
            ],
            [
                'title' => 'Vivaan Essence',
                'image' => "{$base}/2025/11/Untitled-design.jpg",
                'address' => 'Opp S Mall, Near Uma Party Plot, Motera, North West, Ahmedabad',
                'postcode' => '380005',
            ],
        ];
    }

    public static function topProperties(): array
    {
        return [
            ['title' => 'Anand Paramount', 'slug' => 'anand-paramount'],
            ['title' => 'Shubhang Shyam Zircon Amora', 'slug' => 'shubhang-shyam-zircon-amora'],
            ['title' => 'Vivaan Essence', 'slug' => 'vivaan-essence'],
            ['title' => 'Avibhanam Panache', 'slug' => 'avibhanam-panache'],
            ['title' => 'Shilp Celestial', 'slug' => 'shilp-celestial'],
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
