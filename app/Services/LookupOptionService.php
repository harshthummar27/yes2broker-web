<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AmenityOption;
use App\Models\City;
use App\Models\Locality;
use App\Models\ProjectUnit;
use App\Models\PropertyConfiguration;
use App\Models\PropertyType;
use App\Models\State;
use App\Support\ProjectAreaUnit;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

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

    public function defaultCityName(): string
    {
        return 'Ahmedabad';
    }

    /**
     * @return array<string, string> name => name
     */
    public function statesForAdmin(): array
    {
        return Cache::remember('lookup.states.admin', self::CACHE_TTL_SECONDS, function (): array {
            return State::query()
                ->active()
                ->ordered()
                ->pluck('name', 'name')
                ->all();
        });
    }

    public function defaultStateName(): string
    {
        return 'Gujarat';
    }

    /**
     * @return array<string, string> name => name
     */
    public function localitiesForAdmin(?string $cityName): array
    {
        if (blank($cityName)) {
            return [];
        }

        $cacheKey = 'lookup.localities.admin.'.Str::slug($cityName);

        return Cache::remember($cacheKey, self::CACHE_TTL_SECONDS, function () use ($cityName): array {
            $city = City::query()
                ->active()
                ->where('name', $cityName)
                ->first();

            if ($city === null) {
                return [];
            }

            return Locality::query()
                ->active()
                ->where('city_id', $city->id)
                ->ordered()
                ->pluck('name', 'name')
                ->all();
        });
    }

    /**
     * @return array<string, string> name => name
     */
    public function configurationsForAdmin(): array
    {
        return Cache::remember('lookup.configurations.admin', self::CACHE_TTL_SECONDS, function (): array {
            return PropertyConfiguration::query()
                ->active()
                ->ordered()
                ->pluck('name', 'name')
                ->all();
        });
    }

    /**
     * @return array<string, string> name => name
     */
    public function projectUnitsForAdmin(): array
    {
        return Cache::remember('lookup.project_units.admin', self::CACHE_TTL_SECONDS, function (): array {
            return ProjectUnit::query()
                ->active()
                ->ordered()
                ->pluck('name', 'name')
                ->all();
        });
    }

    public function defaultProjectUnitName(): string
    {
        return ProjectAreaUnit::DEFAULT_UNIT;
    }

    /**
     * @return array<string, string> name => name
     */
    public function amenityOptionsForAdmin(): array
    {
        return Cache::remember('lookup.amenities.admin', self::CACHE_TTL_SECONDS, function (): array {
            return AmenityOption::query()
                ->active()
                ->ordered()
                ->pluck('name', 'name')
                ->all();
        });
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
        Cache::forget('lookup.states.admin');
        Cache::forget('lookup.configurations.admin');
        Cache::forget('lookup.project_units.admin');
        Cache::forget('lookup.amenities.admin');
        \App\Support\AmenityIcon::clearCache();
        Cache::forget('lookup.property_types.search');
        Cache::forget('lookup.property_types.names');
        Cache::forget('lookup.property_types.admin');

        City::query()->active()->pluck('name')->each(function (string $cityName): void {
            Cache::forget('lookup.localities.admin.'.Str::slug($cityName));
        });
    }
}
