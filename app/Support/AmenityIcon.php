<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\AmenityOption;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class AmenityIcon
{
    private const CACHE_KEY = 'amenity.icon_map';

    private const CACHE_TTL = 3600;

    /**
     * @return list<string>
     */
    public static function availableIcons(): array
    {
        return [
            'security',
            'cctv',
            'fire',
            'intercom',
            'gate',
            'gate-security',
            'car-parking',
            'parking',
            'elevator',
            'power',
            'water',
            'ev-charge',
            'bicycle',
            'clubhouse',
            'gym',
            'playground',
            'garden',
            'walk',
            'seating',
            'seniors',
            'hall',
            'games',
            'wifi',
            'rain',
            'solar',
            'waste',
            'default',
        ];
    }

    public static function resolve(?string $name): string
    {
        if (blank($name)) {
            return 'default';
        }

        $map = self::iconMap();

        if (isset($map[$name])) {
            return $map[$name];
        }

        $normalized = self::normalize($name);

        foreach ($map as $optionName => $icon) {
            if (self::normalize($optionName) === $normalized) {
                return $icon;
            }
        }

        return self::guessIcon($normalized);
    }

    /**
     * @return array<string, string> name => icon
     */
    public static function iconMap(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function (): array {
            return AmenityOption::query()
                ->active()
                ->ordered()
                ->pluck('icon', 'name')
                ->all();
        });
    }

    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    private static function normalize(string $value): string
    {
        $value = str_replace(['×', '’', "'"], ['x', '', ''], strtolower($value));

        return preg_replace('/[^a-z0-9]+/', ' ', $value) ?? $value;
    }

    private static function guessIcon(string $normalized): string
    {
        return match (true) {
            str_contains($normalized, 'security') || str_contains($normalized, 'gated') => 'security',
            str_contains($normalized, 'cctv') || str_contains($normalized, 'camera') => 'cctv',
            str_contains($normalized, 'fire') => 'fire',
            str_contains($normalized, 'intercom') => 'intercom',
            str_contains($normalized, 'gate') => 'gate',
            str_contains($normalized, 'car parking') => 'car-parking',
            str_contains($normalized, 'parking') || str_contains($normalized, 'garage') => 'parking',
            str_contains($normalized, 'lift') || str_contains($normalized, 'elevator') => 'elevator',
            str_contains($normalized, 'power') || str_contains($normalized, 'backup') => 'power',
            str_contains($normalized, 'water') => 'water',
            str_contains($normalized, 'ev') || str_contains($normalized, 'charging') => 'ev-charge',
            str_contains($normalized, 'bicycle') || str_contains($normalized, 'cycle') => 'bicycle',
            str_contains($normalized, 'club') => 'clubhouse',
            str_contains($normalized, 'gym') => 'gym',
            str_contains($normalized, 'play') || str_contains($normalized, 'kids') || str_contains($normalized, 'children') => 'playground',
            str_contains($normalized, 'garden') || str_contains($normalized, 'landscape') => 'garden',
            str_contains($normalized, 'walk') || str_contains($normalized, 'jog') || str_contains($normalized, 'track') => 'walk',
            str_contains($normalized, 'senior') => 'seniors',
            str_contains($normalized, 'seat') || str_contains($normalized, 'sit') => 'seating',
            str_contains($normalized, 'hall') || str_contains($normalized, 'banquet') => 'hall',
            str_contains($normalized, 'game') => 'games',
            str_contains($normalized, 'wifi') || str_contains($normalized, 'wi fi') => 'wifi',
            str_contains($normalized, 'rain') => 'rain',
            str_contains($normalized, 'solar') => 'solar',
            str_contains($normalized, 'waste') => 'waste',
            default => 'default',
        };
    }
}
