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
            'near_possession' => self::applyPatternGroup($query, self::nearPossessionPatterns()),
            'under_construction' => $query->where(function (Builder $builder): void {
                $builder
                    ->whereRaw("LOWER(possession) LIKE '%under%construction%'")
                    ->orWhereRaw("LOWER(possession) LIKE '%under-construction%'")
                    ->orWhere(function (Builder $dated): void {
                        $dated
                            ->whereRaw("LOWER(REPLACE(possession, ' ', '')) NOT LIKE '%ready%'")
                            ->where(function (Builder $patterns): void {
                                self::applyPatternGroup($patterns, self::underConstructionPatterns());
                            });
                    });
            }),
            default => $query,
        };
    }

    /**
     * @param  list<string>  $patterns
     */
    private static function applyPatternGroup(Builder $query, array $patterns): Builder
    {
        if ($patterns === []) {
            return $query->whereRaw('0 = 1');
        }

        return $query->where(function (Builder $builder) use ($patterns): void {
            foreach ($patterns as $pattern) {
                $builder->orWhereRaw('LOWER(possession) LIKE ?', [$pattern]);
            }
        });
    }

    /**
     * @return list<string>
     */
    private static function nearPossessionPatterns(): array
    {
        return self::monthYearPatterns(Carbon::now(), 12);
    }

    /**
     * @return list<string>
     */
    private static function underConstructionPatterns(): array
    {
        $start = Carbon::now()->addMonths(13)->startOfMonth();
        $end = Carbon::now()->addYears(8)->endOfYear();

        return self::monthYearPatterns($start, (int) $start->diffInMonths($end));
    }

    /**
     * @return list<string>
     */
    private static function monthYearPatterns(Carbon $start, int $months): array
    {
        $patterns = [];
        $cursor = $start->copy()->startOfMonth();

        for ($index = 0; $index < $months; $index++) {
            $patterns = array_merge($patterns, self::patternsForMonth($cursor));
            $cursor->addMonth();
        }

        return array_values(array_unique($patterns));
    }

    /**
     * @return list<string>
     */
    private static function patternsForMonth(Carbon $date): array
    {
        $year = $date->format('Y');
        $fullMonth = strtolower($date->format('F'));
        $shortMonth = strtolower($date->format('M'));

        return [
            "%{$fullMonth} {$year}%",
            "%{$fullMonth}, {$year}%",
            "%{$shortMonth} {$year}%",
            "%{$shortMonth}, {$year}%",
            "%{$shortMonth},%{$year}%",
        ];
    }
}
