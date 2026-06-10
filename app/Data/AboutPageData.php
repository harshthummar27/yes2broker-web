<?php

namespace App\Data;

class AboutPageData
{
    public static function highlights(): array
    {
        return [
            'Personalized property guidance for every client',
            'Get early access to exclusive listings',
            '24/7 support for urgent property needs',
        ];
    }

    public static function services(): array
    {
        return [
            [
                'title' => 'Property Buying Assistance',
                'description' => 'We help you find the right property at the right price. Whether it\'s a home or investment, our experts guide you every step of the way — from site visits to final paperwork.',
                'icon' => 'buy',
            ],
            [
                'title' => 'Property Selling Services',
                'description' => 'Want to sell your property quickly and profitably? We provide valuation, marketing, and verified buyer connections to ensure a smooth and secure sale.',
                'icon' => 'sell',
            ],
            [
                'title' => 'Real Estate Consultation',
                'description' => 'Confused about buying, selling, or investing? Book a consultation to get expert insights on current trends, legal processes, and the best areas to explore in Ahmedabad.',
                'icon' => 'consult',
            ],
        ];
    }

    public static function howItWorks(): array
    {
        return [
            [
                'title' => 'Consultation',
                'description' => 'Our real estate experts start with a detailed consultation to understand your requirements, budget, and preferred locations in Ahmedabad. We ensure every option aligns with your lifestyle and investment goals.',
            ],
            [
                'title' => 'Property Search and Viewing',
                'description' => 'Based on your needs, we shortlist the best properties and arrange viewings—virtual or in-person. From gated communities to luxury apartments, we bring you the most relevant listings.',
            ],
            [
                'title' => 'Closing and Support',
                'description' => 'Once you\'ve selected your property, we handle everything—from negotiations and documentation to legal formalities—ensuring a smooth and stress-free closing experience.',
            ],
        ];
    }

    public static function team(): array
    {
        return [
            ['name' => 'Michael Anderson', 'role' => 'Senior Property Consultant'],
            ['name' => 'Sarah Thompson', 'role' => 'Lead Real Estate Agent'],
            ['name' => 'David Patel', 'role' => 'Commercial Property Advisor'],
        ];
    }
}
