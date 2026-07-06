<?php

declare(strict_types=1);

namespace App\Support;

class IndianPrice
{
    public static function formatPart(int|float|null $rupees): string
    {
        if ($rupees === null || (float) $rupees <= 0) {
            return '';
        }

        $rupees = (float) $rupees;

        if ($rupees >= 10_000_000) {
            return '₹ '.self::trimDecimal($rupees / 10_000_000).' Cr';
        }

        if ($rupees >= 100_000) {
            return '₹ '.self::trimDecimal($rupees / 100_000).' Lakhs';
        }

        if ($rupees >= 1_000) {
            return '₹ '.self::trimDecimal($rupees / 1_000).' Thousand';
        }

        return '₹ '.number_format($rupees);
    }

    public static function formatRange(int|float|null $min, int|float|null $max = null): string
    {
        if ($min === null || (float) $min <= 0) {
            return '';
        }

        $minPart = self::formatPart($min);

        if ($max === null || (float) $max <= 0 || (float) $max <= (float) $min) {
            return $minPart.' Onwards';
        }

        return $minPart.' - '.self::formatPart($max);
    }

    public static function previewLine(int|float|null $amount): string
    {
        if ($amount === null || (float) $amount <= 0) {
            return '—';
        }

        return self::formatPart($amount).' ('.number_format((float) $amount).' rupees)';
    }

    public static function toMinLakhs(int|float|null $rupees): float
    {
        if ($rupees === null || (float) $rupees <= 0) {
            return 0;
        }

        return round((float) $rupees / 100_000, 2);
    }

    /**
     * @return array{min: ?float, max: ?float}
     */
    public static function parseRange(?string $price): array
    {
        if (blank($price)) {
            return ['min' => null, 'max' => null];
        }

        $price = trim(str_ireplace('Onwards', '', $price));

        if (str_contains($price, '-')) {
            [$minFragment, $maxFragment] = array_map('trim', explode('-', $price, 2));

            return [
                'min' => self::parsePart($minFragment),
                'max' => self::parsePart($maxFragment),
            ];
        }

        return [
            'min' => self::parsePart($price),
            'max' => null,
        ];
    }

    public static function parsePart(string $fragment): ?float
    {
        if (! preg_match('/₹?\s*([\d.]+)\s*(Cr(?:ore)?|Lakhs?|Lacs?|L\b|Thousand)?/iu', trim($fragment), $matches)) {
            return null;
        }

        $value = (float) $matches[1];
        $unit = strtolower($matches[2] ?? 'l');

        return match (true) {
            str_starts_with($unit, 'c') => $value * 10_000_000,
            str_starts_with($unit, 't') => $value * 1_000,
            default => $value * 100_000,
        };
    }

    private static function trimDecimal(float $value): string
    {
        return rtrim(rtrim(number_format($value, 2, '.', ''), '0'), '.');
    }
}
