<?php

declare(strict_types=1);

namespace App\Support;

class ProjectAreaUnit
{
    public const DEFAULT_UNIT = 'Sq. Yard.';

    public static function convert(float $value, ?string $fromUnit, ?string $toUnit): float
    {
        if ($fromUnit === null || $toUnit === null || $fromUnit === $toUnit) {
            return $value;
        }

        $fromFactor = self::toSquareMetersFactor($fromUnit);
        $toFactor = self::toSquareMetersFactor($toUnit);

        if ($fromFactor === null || $toFactor === null || $toFactor <= 0) {
            return $value;
        }

        return ($value * $fromFactor) / $toFactor;
    }

    public static function formatValue(float $value): string|int|float
    {
        $rounded = round($value, 4);

        if (fmod($rounded, 1.0) === 0.0) {
            return (int) $rounded;
        }

        return (float) rtrim(rtrim(number_format($rounded, 4, '.', ''), '0'), '.');
    }

    public static function toSquareMetersFactor(string $unit): ?float
    {
        $normalized = strtolower(trim($unit));

        return match (true) {
            str_contains($normalized, 'yard') => 0.83612736,
            str_contains($normalized, 'ft') || str_contains($normalized, 'feet') => 0.09290304,
            str_contains($normalized, 'mtr') || str_contains($normalized, 'meter') => 1.0,
            str_contains($normalized, 'acre') => 4046.8564224,
            str_contains($normalized, 'hectare') => 10000.0,
            str_contains($normalized, 'bigha') => 2090.3184,
            default => null,
        };
    }
}
