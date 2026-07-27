<?php

declare(strict_types=1);

namespace App\Support;

class PropertyUnitConfiguration
{
    /**
     * @param  list<array<string, mixed>>  $items
     */
    public static function normalizeList(array $items): array
    {
        $normalized = [];

        foreach ($items as $item) {
            if (! is_array($item) || blank($item['configuration'] ?? null)) {
                continue;
            }

            $totalUnits = self::toInt($item['total_units'] ?? null);
            $availableUnits = self::toInt($item['available_units'] ?? null);

            if ($totalUnits !== null && $availableUnits !== null && $availableUnits > $totalUnits) {
                $availableUnits = $totalUnits;
            }

            $normalized[] = [
                'configuration' => trim((string) $item['configuration']),
                'size_value' => filled($item['size_value'] ?? null) ? (float) $item['size_value'] : null,
                'size_unit' => filled($item['size_unit'] ?? null)
                    ? trim((string) $item['size_unit'])
                    : 'Sq. Ft.',
                'total_units' => $totalUnits,
                'available_units' => $availableUnits,
                'price' => filled($item['price'] ?? null) ? (int) $item['price'] : null,
            ];
        }

        return $normalized;
    }

    /**
     * @param  array<string, mixed>  $item
     */
    public static function formatSize(array $item): string
    {
        $value = $item['size_value'] ?? null;
        $unit = $item['size_unit'] ?? '';

        if ($value === null || $value === '') {
            return '';
        }

        return trim(ProjectAreaUnit::formatValue((float) $value).' '.$unit);
    }

    /**
     * @param  list<array<string, mixed>>  $items
     */
    public static function configurationsSummary(array $items): string
    {
        $lines = [];

        foreach ($items as $item) {
            $size = self::formatSize($item);

            if ($size === '') {
                $lines[] = $item['configuration'];

                continue;
            }

            $lines[] = $item['configuration'].' — '.$size;
        }

        return implode("\n", $lines);
    }

    /**
     * @param  list<array<string, mixed>>  $items
     */
    public static function projectSizeSummary(array $items): string
    {
        $lines = [];

        foreach ($items as $item) {
            $total = $item['total_units'] ?? null;
            $available = $item['available_units'] ?? null;

            if ($total === null && $available === null) {
                continue;
            }

            $lines[] = sprintf(
                '%s: %s / %s units available',
                $item['configuration'],
                $available ?? '—',
                $total ?? '—'
            );
        }

        return implode("\n", $lines);
    }

    /**
     * @param  list<array<string, mixed>>  $items
     * @return list<array{configuration: string, size: string, total_units: ?int, available_units: ?int}>
     */
    public static function presentationItems(array $items): array
    {
        return array_map(function (array $item): array {
            return [
                'configuration' => $item['configuration'],
                'size' => self::formatSize($item),
                'total_units' => $item['total_units'] ?? null,
                'available_units' => $item['available_units'] ?? null,
                'price' => $item['price'] ?? null,
            ];
        }, $items);
    }

    /**
     * @param  list<array<string, mixed>>  $items
     */
    public static function sumTotalUnits(array $items): ?int
    {
        $total = 0;
        $hasValue = false;

        foreach ($items as $item) {
            $units = $item['total_units'] ?? null;

            if ($units === null) {
                continue;
            }

            $hasValue = true;
            $total += (int) $units;
        }

        return $hasValue ? $total : null;
    }

    /**
     * @param  list<array<string, mixed>>  $items
     */
    public static function projectSizeConfigurationSummary(array $items): ?string
    {
        $lines = [];

        foreach ($items as $item) {
            $total = $item['total_units'] ?? null;

            if ($total === null) {
                continue;
            }

            $lines[] = $item['configuration'].' - '.self::formatUnitCountLabel($total);
        }

        return $lines === [] ? null : implode("\n", $lines);
    }

    public static function formatUnitCountLabel(?int $count): ?string
    {
        if ($count === null) {
            return null;
        }

        return $count.' '.($count === 1 ? 'unit' : 'units');
    }

    public static function formatProjectSize(?int $buildings, ?int $totalUnits): ?string
    {
        $parts = [];

        if ($buildings !== null && $buildings > 0) {
            $parts[] = $buildings.' '.($buildings === 1 ? 'Building' : 'Buildings');
        }

        if ($totalUnits !== null && $totalUnits > 0) {
            $parts[] = $totalUnits.' '.($totalUnits === 1 ? 'unit' : 'units');
        }

        if ($parts === []) {
            return null;
        }

        return implode(' – ', $parts);
    }

    public static function isUnitsAvailabilitySummary(?string $value): bool
    {
        if (blank($value)) {
            return false;
        }

        foreach (preg_split("/\r\n|\n|\r/", trim($value)) as $line) {
            if (preg_match('/^.+:\s*.+\s*\/\s*\d+\s+units available\s*$/iu', trim($line)) === 1) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array<string, mixed>  $overview
     * @param  list<array<string, mixed>>  $unitConfigurations
     */
    public static function resolveProjectSizeForDisplay(array $overview, array $unitConfigurations): ?string
    {
        if ($unitConfigurations !== []) {
            $byConfiguration = self::projectSizeConfigurationSummary($unitConfigurations);

            if (filled($byConfiguration)) {
                return $byConfiguration;
            }
        }

        $stored = $overview['project_size'] ?? null;

        if (blank($stored) || self::isUnitsAvailabilitySummary($stored)) {
            $buildings = self::toInt($overview['project_buildings'] ?? null);
            $manualTotal = self::toInt($overview['project_total_units'] ?? null);
            $summedTotal = self::sumTotalUnits($unitConfigurations);
            $structured = self::formatProjectSize($buildings, $manualTotal ?? $summedTotal);

            return filled($structured) ? $structured : null;
        }

        if ($unitConfigurations !== [] && $stored === self::projectSizeSummary($unitConfigurations)) {
            return null;
        }

        return (string) $stored;
    }

    /**
     * @param  array<string, mixed>  $overview
     * @param  list<array<string, mixed>>  $unitConfigurations
     */
    public static function syncProjectSizeFields(array &$overview, array $unitConfigurations): void
    {
        if ($unitConfigurations === []) {
            return;
        }

        $byConfiguration = self::projectSizeConfigurationSummary($unitConfigurations);

        if (filled($byConfiguration)) {
            $overview['project_size'] = $byConfiguration;

            return;
        }

        if (self::isUnitsAvailabilitySummary($overview['project_size'] ?? null)) {
            unset($overview['project_size']);
        }
    }

    /**
     * @param  array<string, mixed>  $overview
     * @return list<array{icon: string, label: string, value: string}>
     */
    public static function overviewGridItems(array $overview): array
    {
        $items = [];
        $unitConfigurations = self::normalizeList(
            is_array($overview['unit_configurations'] ?? null) ? $overview['unit_configurations'] : []
        );

        $projectArea = $overview['project_area'] ?? null;

        if (filled($projectArea)) {
            $items[] = [
                'icon' => 'project-area',
                'label' => 'Project Area',
                'value' => (string) $projectArea,
            ];
        }

        if ($unitConfigurations !== []) {
            $composedBhk = self::composeBhkLabel($unitConfigurations);
            if (filled($composedBhk)) {
                $items[] = [
                    'icon' => 'configuration',
                    'label' => 'Configurations',
                    'value' => $composedBhk,
                ];
            }

            $sizeRange = self::formatSizeRange($unitConfigurations);
            if (filled($sizeRange)) {
                $items[] = [
                    'icon' => 'project-area',
                    'label' => 'Sizes',
                    'value' => $sizeRange,
                ];
            }
        } elseif (filled($overview['configurations'] ?? null)) {
            $items[] = [
                'icon' => 'configuration',
                'label' => 'Configurations',
                'value' => (string) $overview['configurations'],
            ];
        }

        $launchDate = $overview['launch_date'] ?? null;

        if (filled($launchDate)) {
            $items[] = [
                'icon' => 'launch-date',
                'label' => 'Launch Date',
                'value' => (string) $launchDate,
            ];
        }

        $priceRange = $overview['price_range'] ?? null;

        if (filled($priceRange)) {
            $items[] = [
                'icon' => 'price-range',
                'label' => 'Price Range',
                'value' => (string) $priceRange,
            ];
        }

        $possession = $overview['possession'] ?? null;

        if (filled($possession)) {
            $items[] = [
                'icon' => 'possession',
                'label' => 'Possession Date',
                'value' => (string) $possession,
            ];
        }

        $reraId = $overview['rera_id'] ?? null;

        if (filled($reraId)) {
            $items[] = [
                'icon' => 'rera',
                'label' => 'RERA ID',
                'value' => (string) $reraId,
            ];
        }

        return $items;
    }

    public static function formatAvailableUnitsLabel(?int $available): ?string
    {
        if ($available === null) {
            return null;
        }

        $unitWord = $available === 1 ? 'unit' : 'units';

        return $available.' '.$unitWord.' available';
    }

    /**
     * @param  array{configuration: string, size: string, total_units: ?int, available_units: ?int}  $configuration
     */
    public static function configurationOverviewValue(array $configuration, bool $isPublic = false): string
    {
        $value = $configuration['configuration'];

        if (filled($configuration['size'])) {
            $value .= ' – '.$configuration['size'];
        }

        if (filled($configuration['price'])) {
            $value .= ' – '.IndianPrice::formatPart($configuration['price']);
        }

        if (! $isPublic) {
            $availableLabel = self::formatAvailableUnitsLabel($configuration['available_units'] ?? null);

            if (filled($availableLabel)) {
                $value .= "\n".$availableLabel;
            }
        }

        return $value;
    }

    /**
     * @param  list<array<string, mixed>>  $items
     */
    public static function overviewCardValue(array $items): string
    {
        $blocks = [];

        foreach (self::presentationItems($items) as $item) {
            $lines = [$item['configuration']];

            if (filled($item['size'])) {
                $lines[0] .= ' — '.$item['size'];
            }

            $availableLabel = self::formatAvailableUnitsLabel($item['available_units'] ?? null);

            if (filled($availableLabel)) {
                $lines[] = $availableLabel;
            }

            $blocks[] = implode("\n", $lines);
        }

        return implode("\n\n", $blocks);
    }

    /**
     * @param  list<array<string, mixed>>  $items
     */
    public static function composeBhkLabel(array $items): string
    {
        $configurations = [];

        foreach ($items as $item) {
            if (filled($item['configuration'] ?? null)) {
                $configurations[] = trim((string) $item['configuration']);
            }
        }

        $configurations = array_values(array_unique($configurations));

        if ($configurations === []) {
            return '';
        }

        if (count($configurations) === 1) {
            return $configurations[0];
        }

        // Recursively strip common suffixes and store them.
        $suffixes = [];
        $currentCores = $configurations;

        while (true) {
            $suffix = self::commonConfigurationSuffix($currentCores);
            if ($suffix === null) {
                break;
            }

            $suffixes[] = $suffix;
            $currentCores = array_map(function (string $core) use ($suffix): string {
                $pattern = '/\s+'.preg_quote($suffix, '/').'s?\s*$/iu';
                $stripped = preg_replace($pattern, '', $core);
                return trim($stripped) !== '' ? trim($stripped) : $core;
            }, $currentCores);
        }

        // Format the remaining cores with commas and ampersand
        $formattedCores = self::formatListWithAmpersand($currentCores);

        // Re-append the suffixes in reverse order
        $result = $formattedCores;
        foreach (array_reverse($suffixes) as $suf) {
            $result .= ' ' . $suf;
        }

        return $result;
    }

    public static function formatListWithAmpersand(array $items): string
    {
        if (count($items) === 0) {
            return '';
        }
        if (count($items) === 1) {
            return (string) $items[0];
        }
        $lastItem = array_pop($items);
        return implode(', ', $items) . ' & ' . $lastItem;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function syncBhkOnPropertyData(array &$data): void
    {
        $overview = is_array($data['overview'] ?? null) ? $data['overview'] : [];
        $unitConfigurations = self::normalizeList(
            is_array($overview['unit_configurations'] ?? null) ? $overview['unit_configurations'] : []
        );

        if ($unitConfigurations === []) {
            return;
        }

        $composed = self::composeBhkLabel($unitConfigurations);

        if (filled($composed)) {
            $data['bhk'] = $composed;
        }
    }

    /**
     * @param  list<string>  $configurations
     */
    private static function commonConfigurationSuffix(array $configurations): ?string
    {
        if (count($configurations) < 2) {
            return null;
        }

        if (! preg_match('/\s(\S+)$/u', $configurations[0], $match)) {
            return null;
        }

        $suffix = $match[1];
        $normalizedSuffix = preg_replace('/s$/i', '', $suffix);

        foreach ($configurations as $configuration) {
            if (! preg_match('/\s'.preg_quote($suffix, '/').'\s*$/iu', $configuration)
                && ! preg_match('/\s'.preg_quote($normalizedSuffix, '/').'\s*$/iu', $configuration)) {
                return null;
            }
        }

        return $normalizedSuffix;
    }

    public static function formatSizeRange(array $items): string
    {
        $sizes = [];

        foreach ($items as $item) {
            $value = $item['size_value'] ?? null;
            $unit = $item['size_unit'] ?? 'Sq. Ft.';

            if ($value !== null && $value !== '') {
                $sizes[] = [
                    'value' => (float) $value,
                    'unit' => trim($unit)
                ];
            }
        }

        if ($sizes === []) {
            return '';
        }

        if (count($sizes) === 1) {
            return ProjectAreaUnit::formatValue($sizes[0]['value']) . ' ' . $sizes[0]['unit'];
        }

        usort($sizes, fn($a, $b) => $a['value'] <=> $b['value']);

        $minItem = $sizes[0];
        $maxItem = $sizes[count($sizes) - 1];

        if ($minItem['value'] === $maxItem['value'] && $minItem['unit'] === $maxItem['unit']) {
            return ProjectAreaUnit::formatValue($minItem['value']) . ' ' . $minItem['unit'];
        }

        $minLabel = ProjectAreaUnit::formatValue($minItem['value']) . ($minItem['unit'] !== $maxItem['unit'] ? ' ' . $minItem['unit'] : '');
        $maxLabel = ProjectAreaUnit::formatValue($maxItem['value']) . ' ' . $maxItem['unit'];

        return $minLabel . ' - ' . $maxLabel;
    }

    private static function toInt(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return max(0, (int) $value);
    }
}
