<?php

declare(strict_types=1);

namespace App\Support;

class MapEmbed
{
    public static function mapUrl(string $location): string
    {
        return 'https://maps.google.com/maps?q='.rawurlencode($location).'&t=m&z=12&output=embed&iwloc=near';
    }

    public static function streetViewUrl(string $location): string
    {
        return 'https://maps.google.com/maps?ie=UTF8&q='.rawurlencode($location).'&layer=c&cbp=11,,,,&output=svembed';
    }

    public static function streetViewUrlFromCoordinates(float $latitude, float $longitude): string
    {
        return sprintf(
            'https://maps.google.com/maps?layer=c&cbll=%s,%s&cbp=11,0,0,0,0&output=svembed',
            $latitude,
            $longitude
        );
    }
}
