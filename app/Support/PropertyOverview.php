<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\Property;
use Carbon\Carbon;

class PropertyOverview
{
    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function buildPayload(array $data): array
    {
        $overview = is_array($data['overview'] ?? null) ? $data['overview'] : [];

        $projectArea = filled($data['area'] ?? null)
            ? (string) $data['area']
            : Property::formatProjectArea($data['project_area_value'] ?? null, $data['project_area_unit'] ?? null);

        if (filled($projectArea)) {
            $overview['project_area'] = $projectArea;
        }

        $priceRange = $data['price'] ?? null;

        if (blank($priceRange) && filled($data['price_min_amount'] ?? null)) {
            $priceRange = IndianPrice::formatRange(
                (float) $data['price_min_amount'],
                filled($data['price_max_amount'] ?? null) ? (float) $data['price_max_amount'] : null
            );
        }

        if (filled($priceRange)) {
            $overview['price_range'] = $priceRange;
        }

        if (! empty($data['possession_is_ready'])) {
            $overview['possession'] = 'Ready to Move';
        } elseif (filled($data['possession_date'] ?? null)) {
            $overview['possession'] = Property::formatMonthYear((string) $data['possession_date'])
                ?? Carbon::parse((string) $data['possession_date'])->startOfMonth()->format('F Y');
        } elseif (filled($data['possession'] ?? null)) {
            $overview['possession'] = (string) $data['possession'];
        }

        $unitConfigurations = PropertyUnitConfiguration::normalizeList(
            is_array($overview['unit_configurations'] ?? null) ? $overview['unit_configurations'] : []
        );

        if ($unitConfigurations !== []) {
            $overview['unit_configurations'] = $unitConfigurations;
            $overview['configurations'] = PropertyUnitConfiguration::configurationsSummary($unitConfigurations);
            PropertyUnitConfiguration::syncProjectSizeFields($overview, $unitConfigurations);
        } elseif (filled($data['bhk'] ?? null) && blank($overview['configurations'] ?? null)) {
            $overview['configurations'] = is_array($data['bhk'])
                ? (string) Property::composeBhkSelections($data['bhk'])
                : (string) $data['bhk'];
        } elseif (PropertyUnitConfiguration::isUnitsAvailabilitySummary($overview['project_size'] ?? null)) {
            unset($overview['project_size']);
        }

        if (blank($overview['rera_id'] ?? null)) {
            $overview['rera_id'] = 'Available on request';
        }

        return $overview;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return list<array{icon: string, label: string, value: string, value_style?: string}>
     */
    public static function gridItemsFromFormData(array $data): array
    {
        return PropertyUnitConfiguration::overviewGridItems(self::buildPayload($data));
    }

    /**
     * @param  list<array<string, mixed>>  $unitConfigurations
     * @return list<array{
     *     configuration: string,
     *     size: string,
     *     total_units: ?int,
     *     available_units: ?int,
     *     website_configuration: string,
     *     website_project_size: ?string
     * }>
     */
    public static function configurationTableRows(array $unitConfigurations): array
    {
        $rows = [];

        foreach (PropertyUnitConfiguration::presentationItems($unitConfigurations) as $configuration) {
            $totalUnits = $configuration['total_units'] ?? null;

            $rows[] = [
                'configuration' => $configuration['configuration'],
                'size' => filled($configuration['size']) ? $configuration['size'] : '—',
                'total_units' => $totalUnits,
                'available_units' => $configuration['available_units'] ?? null,
                'price' => $configuration['price'] ?? null,
                'website_configuration' => PropertyUnitConfiguration::configurationOverviewValue($configuration),
                'website_project_size' => $totalUnits !== null
                    ? $configuration['configuration'].' - '.PropertyUnitConfiguration::formatUnitCountLabel($totalUnits)
                    : null,
            ];
        }

        return $rows;
    }
}
