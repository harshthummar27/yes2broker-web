<?php

namespace App\Data;

class ListPropertyPageData
{
    public static function propertyTypes(): array
    {
        return [
            'Apartments / Flats',
            'Villas',
            'Independent Homes',
            'Bungalows',
            'Office Spaces',
            'Showrooms',
            'Farmhouses',
            'Residential & Commercial Land',
        ];
    }

    public static function listingStatuses(): array
    {
        return [
            '' => 'Select...',
            'for_rent' => 'For Rent',
            'for_sell' => 'For Sell',
        ];
    }

    public static function ownerTypes(): array
    {
        return [
            'consultant' => 'Are You Property Consultant',
            'individual' => 'Are You An Individuals Own',
        ];
    }

    public static function listableTypes(): array
    {
        return [
            'Residential: Apartments, Villas, Bungalows',
            'Commercial: Shops, Offices, Warehouses',
            'Builder/Developer Projects',
            'Plots & Land',
            'Rental Properties',
        ];
    }
}
