<?php

namespace App\Data;

use App\Support\SiteAsset;

class HomeLoanPageData
{
    public static function howItWorks(): array
    {
        return [
            [
                'icon' => 'form',
                'description' => 'Submit a quick online form to get started with our services and explore your best-fit solutions.',
            ],
            [
                'icon' => 'experts',
                'description' => 'Our experts will contact you, understand your needs, and help you choose the right service plan.',
            ],
            [
                'icon' => 'documents',
                'description' => 'We collect required details/documents from you and manage all the backend work efficiently.',
            ],
            [
                'icon' => 'updates',
                'description' => 'We keep you informed at every stage and share updates until the process is successfully completed.',
            ],
        ];
    }

    public static function bankPartners(): array
    {
        return [
            [
                'name' => 'ICICI Bank',
                'rate' => 'From 7.90% p.a.',
                'logo' => SiteAsset::url('images/media/2025/11/icici-bank.png'),
            ],
            [
                'name' => 'Union Bank of India',
                'rate' => 'From 7.35% p.a.',
                'logo' => SiteAsset::url('images/media/2025/11/union-bank.png'),
            ],
            [
                'name' => 'Saraswat Bank',
                'rate' => 'From 7.50% p.a.',
                'logo' => SiteAsset::url('images/media/2025/11/saraswat-bank.png'),
            ],
            [
                'name' => 'Bajaj Housing Finance',
                'rate' => 'From 8.00% p.a.',
                'logo' => SiteAsset::url('images/media/2025/11/bajaj-housing.png'),
            ],
        ];
    }
}
