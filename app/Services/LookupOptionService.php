<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\City;
use App\Models\PropertyType;
use Illuminate\Support\Facades\Cache;

class LookupOptionService
{
    private const CACHE_TTL_SECONDS = 3600;

    /**
     * @return array<string, string> slug => name
     */
    public function citiesForSearch(): array
    {
        return Cache::remember('lookup.cities.search', self::CACHE_TTL_SECONDS, function (): array {
            return City::query()
                ->active()
                ->ordered()
                ->pluck('name', 'slug')
                ->all();
        });
    }

    /**
     * @return array<string, string> name => name
     */
    public function citiesForAdmin(): array
    {
        return Cache::remember('lookup.cities.admin', self::CACHE_TTL_SECONDS, function (): array {
            return City::query()
                ->active()
                ->ordered()
                ->pluck('name', 'name')
                ->all();
        });
    }

    public function defaultCitySlug(): string
    {
        return array_key_first($this->citiesForSearch()) ?? 'ahmedabad';
    }

    /**
     * @return array<string, string> slug => name
     */
    public function propertyTypesForSearch(): array
    {
        return Cache::remember('lookup.property_types.search', self::CACHE_TTL_SECONDS, function (): array {
            $options = ['' => 'Select Type'];

            foreach (PropertyType::query()->active()->ordered()->get() as $type) {
                $options[$type->slug] = $type->name;
            }

            return $options;
        });
    }

    /**
     * @return array<string, string> name => name
     */
    public function propertyTypesForAdmin(): array
    {
        return Cache::remember('lookup.property_types.admin', self::CACHE_TTL_SECONDS, function (): array {
            return PropertyType::query()
                ->active()
                ->ordered()
                ->pluck('name', 'name')
                ->all();
        });
    }

    /**
     * @return list<string>
     */
    public function propertyTypeNames(): array
    {
        return Cache::remember('lookup.property_types.names', self::CACHE_TTL_SECONDS, function (): array {
            return PropertyType::query()
                ->active()
                ->ordered()
                ->pluck('name')
                ->all();
        });
    }

    public function clearCache(): void
    {
        Cache::forget('lookup.cities.search');
        Cache::forget('lookup.cities.admin');
        Cache::forget('lookup.property_types.search');
        Cache::forget('lookup.property_types.names');
        Cache::forget('lookup.property_types.admin');
    }
}
