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

    public static function normalizeEmbedInput(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim(html_entity_decode($value, ENT_QUOTES | ENT_HTML5));

        if ($value === '') {
            return null;
        }

        if (preg_match('/<iframe[^>]+src=["\']([^"\']+)["\']/i', $value, $matches)) {
            return trim($matches[1]);
        }

        if (preg_match('/src=["\']([^"\']+)["\']/i', $value, $matches)) {
            return trim($matches[1]);
        }

        return $value;
    }
}
