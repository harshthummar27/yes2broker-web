<?php

declare(strict_types=1);

namespace App\Support;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class PossessionFilter
{
    public static function options(): array
    {
        return [
            'all' => 'All',
            'ready_to_move' => 'Ready to move',
            'under_construction' => 'Under construction',
            'near_possession' => 'Near possession',
        ];
    }

    public static function apply(Builder $query, string $filter): Builder
    {
        return match ($filter) {
            'ready_to_move' => $query->whereRaw("LOWER(REPLACE(possession, ' ', '')) LIKE '%ready%'"),
            'near_possession' => self::applyNearPossession($query),
            'under_construction' => self::applyUnderConstruction($query),
            default => $query,
        };
    }

    public static function possessionDateExpression(): string
    {
        $normalized = "TRIM(REGEXP_REPLACE(REPLACE(possession, ',', ''), '[[:space:]]+', ' '))";

        return "COALESCE(
            STR_TO_DATE(CONCAT('1 ', {$normalized}), '%d %M %Y'),
            STR_TO_DATE(CONCAT('1 ', {$normalized}), '%d %b %Y')
        )";
    }

    public static function nearPossessionStart(): Carbon
    {
        return Carbon::now()->startOfMonth();
    }

    public static function nearPossessionEnd(): Carbon
    {
        return Carbon::now()->addYear()->endOfMonth();
    }

    private static function applyNearPossession(Builder $query): Builder
    {
        $dateExpr = self::possessionDateExpression();
        $start = self::nearPossessionStart()->format('Y-m-d');
        $end = self::nearPossessionEnd()->format('Y-m-d');

        return $query
            ->whereRaw("LOWER(REPLACE(possession, ' ', '')) NOT LIKE '%ready%'")
            ->whereRaw("{$dateExpr} IS NOT NULL")
            ->whereRaw("{$dateExpr} >= ?", [$start])
            ->whereRaw("{$dateExpr} <= ?", [$end])
            ->orderByRaw("{$dateExpr} ASC");
    }

    private static function applyUnderConstruction(Builder $query): Builder
    {
        $dateExpr = self::possessionDateExpression();
        $afterNearPossession = self::nearPossessionEnd()->format('Y-m-d');

        return $query->where(function (Builder $builder) use ($dateExpr, $afterNearPossession): void {
            $builder
                ->whereRaw("LOWER(possession) LIKE '%under%construction%'")
                ->orWhereRaw("LOWER(possession) LIKE '%under-construction%'")
                ->orWhere(function (Builder $dated) use ($dateExpr, $afterNearPossession): void {
                    $dated
                        ->whereRaw("LOWER(REPLACE(possession, ' ', '')) NOT LIKE '%ready%'")
                        ->whereRaw("{$dateExpr} IS NOT NULL")
                        ->whereRaw("{$dateExpr} > ?", [$afterNearPossession]);
                });
        });
    }
}
