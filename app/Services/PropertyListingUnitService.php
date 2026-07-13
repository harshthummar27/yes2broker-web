<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Property;
use App\Models\PropertyListingUnit;
use App\Support\PropertyUnitConfiguration;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Schema;

class PropertyListingUnitService
{
    /**
     * @param  list<array<string, mixed>>  $items
     */
    public function syncForProperty(Property $property, array $items): void
    {
        if (! $this->tableExists()) {
            return;
        }

        $normalized = PropertyUnitConfiguration::normalizeList($items);

        $property->listingUnits()->delete();

        foreach ($normalized as $index => $item) {
            $property->listingUnits()->create([
                'sort_order' => $index,
                'configuration' => $item['configuration'],
                'size_value' => $item['size_value'],
                'size_unit' => $item['size_unit'],
                'total_units' => $item['total_units'],
                'available_units' => $item['available_units'],
                'price' => $item['price'],
            ]);
        }
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function normalizedListForProperty(Property $property): array
    {
        if ($this->tableExists()) {
            try {
                $rows = $property->relationLoaded('listingUnits')
                    ? $property->listingUnits
                    : $property->listingUnits()->orderBy('sort_order')->get();

                if ($rows->isNotEmpty()) {
                    return PropertyUnitConfiguration::normalizeList(
                        $rows->map(fn (PropertyListingUnit $unit): array => $unit->toOverviewRow())->all()
                    );
                }
            } catch (QueryException) {
                // Fall back to overview JSON if the table is unavailable.
            }
        }

        $overview = $property->overview ?? [];

        return PropertyUnitConfiguration::normalizeList(
            is_array($overview['unit_configurations'] ?? null) ? $overview['unit_configurations'] : []
        );
    }

    public function syncPropertyFromOverviewJson(Property $property): void
    {
        if (! $this->tableExists()) {
            return;
        }
        $overview = $property->overview ?? [];
        $items = is_array($overview['unit_configurations'] ?? null) ? $overview['unit_configurations'] : [];

        if ($items === []) {
            $property->listingUnits()->delete();

            return;
        }

        $this->syncForProperty($property, $items);
    }

    /**
     * @return array{synced: int, skipped: int}
     */
    public function syncAllFromOverviewJson(): array
    {
        $synced = 0;
        $skipped = 0;

        Property::query()->cursor()->each(function (Property $property) use (&$synced, &$skipped): void {
            $overview = $property->overview ?? [];
            $items = is_array($overview['unit_configurations'] ?? null) ? $overview['unit_configurations'] : [];

            if ($items === []) {
                $skipped++;

                return;
            }

            $this->syncForProperty($property, $items);
            $synced++;
        });

        return compact('synced', 'skipped');
    }

    private function tableExists(): bool
    {
        return Schema::hasTable('property_listing_units');
    }
}
