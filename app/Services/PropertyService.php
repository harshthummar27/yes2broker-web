<?php

namespace App\Services;

use App\Models\Property;
class PropertyService
{
    public const PER_PAGE = 30;

    public function paginate(array $filters = [], int $page = 1): array
    {
        $query = $this->baseQuery($filters);
        $totalCount = (clone $query)->count();
        $totalPages = max(1, (int) ceil($totalCount / self::PER_PAGE));

        $properties = $query
            ->offset(max(0, ($page - 1) * self::PER_PAGE))
            ->limit(self::PER_PAGE)
            ->get()
            ->map->toCardArray()
            ->all();

        return [
            'properties' => $properties,
            'totalCount' => $totalCount,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'hasMore' => $page < $totalPages,
        ];
    }

    public function findActiveBySlug(string $slug): ?Property
    {
        return Property::query()->active()->where('slug', $slug)->first();
    }

    public function related(Property $property, int $limit = 3): array
    {
        $collected = collect();

        $city = trim($property->city ?: '');
        $locality = trim($property->locality ?: '');
        $priceMinLakhs = (float) ($property->price_min_lakhs ?: 0);

        if ($priceMinLakhs <= 0 && ($property->price_min_amount ?? 0) > 0) {
            $priceMinLakhs = (float) ($property->price_min_amount / 100000);
        }

        // Extract area and location keywords (excluding common generic terms)
        $ignoredTerms = ['ahmedabad', 'gujarat', 'india', strtolower($property->title)];
        $locationParts = array_filter(
            array_map('trim', explode(',', $property->location ?: '')),
            fn ($part) => filled($part) && ! in_array(strtolower($part), $ignoredTerms, true)
        );

        $areaKeywords = array_values(array_unique(array_filter([
            $locality,
            ...$locationParts,
        ], fn ($k) => filled($k) && strlen($k) >= 3)));

        // Step 1: Match BOTH Target Location AND Target Price Range (±35%)
        if ($priceMinLakhs > 0 && $areaKeywords !== []) {
            $minPrice = max(0.1, $priceMinLakhs * 0.65);
            $maxPrice = $priceMinLakhs * 1.35;

            $matches = Property::query()
                ->active()
                ->where('id', '!=', $property->id)
                ->where(function ($q) use ($areaKeywords) {
                    foreach ($areaKeywords as $kw) {
                        $term = '%'.strtolower($kw).'%';
                        $q->orWhereRaw('LOWER(locality) LIKE ?', [$term])
                          ->orWhereRaw('LOWER(location) LIKE ?', [$term]);
                    }
                })
                ->whereBetween('price_min_lakhs', [$minPrice, $maxPrice])
                ->orderByRaw('ABS(price_min_lakhs - ?)', [$priceMinLakhs])
                ->limit($limit)
                ->get();

            $collected = $collected->concat($matches);
        }

        // Step 2: Match Target Location in the same locality/area regardless of price
        if ($collected->count() < $limit && $areaKeywords !== []) {
            $existingIds = $collected->pluck('id')->push($property->id)->all();

            $matches = Property::query()
                ->active()
                ->whereNotIn('id', $existingIds)
                ->where(function ($q) use ($areaKeywords) {
                    foreach ($areaKeywords as $kw) {
                        $term = '%'.strtolower($kw).'%';
                        $q->orWhereRaw('LOWER(locality) LIKE ?', [$term])
                          ->orWhereRaw('LOWER(location) LIKE ?', [$term]);
                    }
                })
                ->when($priceMinLakhs > 0, fn ($q) => $q->orderByRaw('ABS(price_min_lakhs - ?)', [$priceMinLakhs]))
                ->orderByDesc('updated_at')
                ->limit($limit - $collected->count())
                ->get();

            $collected = $collected->concat($matches);
        }

        // Step 3: Match Target Price Range in the same city
        if ($collected->count() < $limit && $priceMinLakhs > 0) {
            $existingIds = $collected->pluck('id')->push($property->id)->all();
            $minPrice = max(0.1, $priceMinLakhs * 0.50);
            $maxPrice = $priceMinLakhs * 1.50;

            $matches = Property::query()
                ->active()
                ->whereNotIn('id', $existingIds)
                ->when($city !== '', function ($q) use ($city) {
                    $term = '%'.strtolower($city).'%';
                    $q->where(function ($sq) use ($term) {
                        $sq->whereRaw('LOWER(city) LIKE ?', [$term])
                          ->orWhereRaw('LOWER(location) LIKE ?', [$term]);
                    });
                })
                ->whereBetween('price_min_lakhs', [$minPrice, $maxPrice])
                ->orderByRaw('ABS(price_min_lakhs - ?)', [$priceMinLakhs])
                ->limit($limit - $collected->count())
                ->get();

            $collected = $collected->concat($matches);
        }

        // Step 4: Match Property Type & City
        if ($collected->count() < $limit) {
            $existingIds = $collected->pluck('id')->push($property->id)->all();

            $matches = Property::query()
                ->active()
                ->whereNotIn('id', $existingIds)
                ->when(filled($property->property_type), fn ($q) => $q->where('property_type', $property->property_type))
                ->when($city !== '', function ($q) use ($city) {
                    $term = '%'.strtolower($city).'%';
                    $q->where(function ($sq) use ($term) {
                        $sq->whereRaw('LOWER(city) LIKE ?', [$term])
                          ->orWhereRaw('LOWER(location) LIKE ?', [$term]);
                    });
                })
                ->when($priceMinLakhs > 0, fn ($q) => $q->orderByRaw('ABS(price_min_lakhs - ?)', [$priceMinLakhs]))
                ->orderByDesc('is_trending')
                ->orderByDesc('updated_at')
                ->limit($limit - $collected->count())
                ->get();

            $collected = $collected->concat($matches);
        }

        // Step 5: Fallback if still needed
        if ($collected->count() < $limit) {
            $existingIds = $collected->pluck('id')->push($property->id)->all();

            $matches = Property::query()
                ->active()
                ->whereNotIn('id', $existingIds)
                ->orderByDesc('is_trending')
                ->orderByDesc('updated_at')
                ->limit($limit - $collected->count())
                ->get();

            $collected = $collected->concat($matches);
        }

        return $collected->map->toCardArray()->all();
    }

    public function trending(int $limit = 12): array
    {
        $trending = Property::query()
            ->active()
            ->where('is_trending', true)
            ->orderByDesc('updated_at')
            ->limit($limit)
            ->get();

        if ($trending->count() < $limit) {
            $existingIds = $trending->pluck('id')->all();
            $more = Property::query()
                ->active()
                ->when($existingIds !== [], fn ($q) => $q->whereNotIn('id', $existingIds))
                ->orderByDesc('updated_at')
                ->limit($limit - $trending->count())
                ->get();

            $trending = $trending->concat($more);
        }

        return $trending->map->toCardArray()->all();
    }

    public function featuredCarousel(int $limit = 3): array
    {
        $items = Property::query()
            ->active()
            ->where('is_trending', true)
            ->orderByDesc('updated_at')
            ->limit($limit)
            ->get();

        if ($items->isEmpty()) {
            $items = Property::query()
                ->active()
                ->orderByDesc('updated_at')
                ->limit($limit)
                ->get();
        }

        return $items->map(fn (Property $property) => [
            'title' => $property->title,
            'image' => $property->image_url,
            'address' => $property->displayLocation(),
            'postcode' => $property->postcode(),
            'slug' => $property->slug,
        ])->all();
    }

    public function topProperties(int $limit = 5): array
    {
        return Property::query()
            ->active()
            ->orderByDesc('is_trending')
            ->orderByDesc('updated_at')
            ->limit($limit)
            ->get(['title', 'slug'])
            ->map(fn (Property $property) => [
                'title' => $property->title,
                'slug' => $property->slug,
            ])
            ->all();
    }

    /**
     * @return array<string, array<int, array{title: string, slug: string}>>
     */
    public function localitiesByArea(): array
    {
        $areas = [
            'Ambali', 'Science City', 'Motera', 'Zundal', 'Shela',
            'Chandkheda', 'South Bopal', 'Gota', 'Shilaj', 'Thaltej', 'Gandhinagar',
        ];

        $result = [];

        foreach ($areas as $area) {
            $properties = Property::query()
                ->active()
                ->whereRaw('LOWER(location) LIKE ?', ['%'.strtolower($area).'%'])
                ->orderBy('title')
                ->limit(16)
                ->get(['title', 'slug']);

            if ($properties->isNotEmpty()) {
                $result[$area] = $properties->map(fn (Property $property) => [
                    'title' => $property->title,
                    'slug' => $property->slug,
                ])->all();
            }
        }

        return $result;
    }

    private function baseQuery(array $filters = [])
    {
        $query = Property::query()
            ->active()
            ->filtered($filters);

        if (($filters['possession_filter'] ?? null) === 'near_possession') {
            return $query;
        }

        return $this->applySort($query, $filters['sort'] ?? null);
    }

    private function applySort($query, ?string $sort)
    {
        return match ($sort) {
            'price_asc' => $query->orderByRaw('CASE WHEN price_min_lakhs > 0 THEN price_min_lakhs ELSE 999999 END ASC'),
            'price_desc' => $query->orderByDesc('price_min_lakhs'),
            'newest' => $query->orderByDesc('updated_at'),
            default => $query->orderByDesc('is_trending')->orderByDesc('is_new')->orderBy('title'),
        };
    }

    /**
     * @return array<int, array{title: string, location: string, image: string, slug: string, subtitle: string}>
     */
    public function listPromoBanners(int $limit = 6): array
    {
        $items = Property::query()
            ->active()
            ->where('is_trending', true)
            ->orderByDesc('updated_at')
            ->limit($limit)
            ->get();

        if ($items->isEmpty()) {
            $items = Property::query()
                ->active()
                ->orderByDesc('updated_at')
                ->limit($limit)
                ->get();
        }

        return $items->map(fn (Property $property) => [
            'title' => $property->title,
            'location' => trim(explode(',', $property->displayLocation())[0] ?? $property->displayLocation()),
            'image' => $property->image_url,
            'slug' => $property->slug,
            'subtitle' => 'Discover premium properties at '.$property->title,
        ])->all();
    }
}
