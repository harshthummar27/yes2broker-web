<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Property;
use Illuminate\Console\Command;

class MigrateConfigurationsCommand extends Command
{
    protected $signature = 'properties:migrate-configurations {--force : Force migration even if unit configurations exist}';

    protected $description = 'Migrate existing flat property data fields into structured unit configurations';

    public function handle(): int
    {
        $this->info('Starting property unit configurations migration...');

        $properties = Property::all();
        $count = 0;

        foreach ($properties as $property) {
            $overview = $property->overview ?? [];
            $existingConfigs = $overview['unit_configurations'] ?? [];

            if ($existingConfigs !== [] && !$this->option('force')) {
                continue;
            }

            $detail = \App\Data\PropertyDetailData::findBySlug($property->slug);
            $configsStr = $detail['overview']['configurations'] ?? $overview['configurations'] ?? '';
            $parsedConfigs = [];
            if (filled($configsStr)) {
                $parts = preg_split('/\r\n|\n|\r|\|/u', (string) $configsStr);
                foreach ($parts as $part) {
                    $part = trim($part);
                    if ($part === '') {
                        continue;
                    }
                    
                    if (preg_match('/^([^\d]*\d+\s*BHK|[a-zA-Z\s]+)(?:\s*[:–-]\s*|\s+)([\d,.]+)\s*(sq\.\s*ft\.|sq\s*ft|square\s*feet|acres?|sq\.\s*yd\.|sq\s*yd|square\s*yards)/iu', $part, $matches)) {
                        $bhkVal = trim($matches[1]);
                        $sizeVal = (float) str_replace(',', '', $matches[2]);
                        $unitName = strtolower($matches[3]);
                        $sizeUnit = 'Sq. Ft.';
                        if (str_contains($unitName, 'acre')) {
                            $sizeUnit = 'Acres';
                        } elseif (str_contains($unitName, 'yd') || str_contains($unitName, 'yard')) {
                            $sizeUnit = 'Sq. Yd.';
                        }
                        
                        $parsedConfigs[] = [
                            'configuration' => $bhkVal,
                            'size_value' => $sizeVal,
                            'size_unit' => $sizeUnit,
                        ];
                    }
                }
            }

            $minPrice = $property->price_min_amount;
            $maxPrice = $property->price_max_amount;
            $configs = [];

            if ($parsedConfigs !== []) {
                $parsedCount = count($parsedConfigs);
                for ($i = 0; $i < $parsedCount; $i++) {
                    $pConfig = $parsedConfigs[$i];
                    if ($i === 0 && $minPrice) {
                        $configPrice = $minPrice;
                    } elseif ($i === $parsedCount - 1 && $maxPrice) {
                        $configPrice = $maxPrice;
                    } else {
                        if ($minPrice && $maxPrice) {
                            $configPrice = $minPrice + (($maxPrice - $minPrice) / ($parsedCount - 1)) * $i;
                        } else {
                            $configPrice = $minPrice ?: $maxPrice;
                        }
                    }
                    
                    $configs[] = [
                        'configuration' => $pConfig['configuration'],
                        'size_value' => $pConfig['size_value'],
                        'size_unit' => $pConfig['size_unit'],
                        'total_units' => 10,
                        'available_units' => 8,
                        'price' => $configPrice ? (int) $configPrice : null,
                    ];
                }
            } else {
                $bhkOptions = Property::parseBhkSelections($property->bhk);
                if (empty($bhkOptions)) {
                    $bhkOptions = [$property->bhk ?: 'Property'];
                }

                preg_match_all('/[\d,.]+/', $property->area ?: '', $sizeMatches);
                $sizes = array_values(array_filter(array_map(fn($m) => (float) str_replace(',', '', $m), $sizeMatches[0] ?? [])));
                
                $unit = 'Sq. Ft.';
                if (preg_match('/(sq\.\s*ft\.|sq\s*ft|square\s*feet|acres?|sq\.\s*yd\.|sq\s*yd|square\s*yards)/i', $property->area ?: '', $unitMatch)) {
                    $unitName = strtolower($unitMatch[1]);
                    if (str_contains($unitName, 'acre')) {
                        $unit = 'Acres';
                    } elseif (str_contains($unitName, 'yd') || str_contains($unitName, 'yard')) {
                        $unit = 'Sq. Yd.';
                    }
                }

                $bhkCount = count($bhkOptions);

                if ($bhkCount > 1) {
                    for ($i = 0; $i < $bhkCount; $i++) {
                        $configBhk = $bhkOptions[$i];
                        $configSize = $sizes[$i] ?? ($sizes[0] ?? 1000);
                        
                        if ($i === 0 && $minPrice) {
                            $configPrice = $minPrice;
                        } elseif ($i === $bhkCount - 1 && $maxPrice) {
                            $configPrice = $maxPrice;
                        } else {
                            if ($minPrice && $maxPrice) {
                                $configPrice = $minPrice + (($maxPrice - $minPrice) / ($bhkCount - 1)) * $i;
                            } else {
                                $configPrice = $minPrice ?: $maxPrice;
                            }
                        }

                        $configs[] = [
                            'configuration' => $configBhk,
                            'size_value' => $configSize,
                            'size_unit' => $unit,
                            'total_units' => 10,
                            'available_units' => 8,
                            'price' => $configPrice ? (int) $configPrice : null,
                        ];
                    }
                } else {
                    $configBhk = $bhkOptions[0];
                    $sizeCount = count($sizes);
                    if ($sizeCount > 1) {
                        for ($i = 0; $i < $sizeCount; $i++) {
                            $configSize = $sizes[$i];
                            if ($i === 0 && $minPrice) {
                                $configPrice = $minPrice;
                            } elseif ($i === $sizeCount - 1 && $maxPrice) {
                                $configPrice = $maxPrice;
                            } else {
                                if ($minPrice && $maxPrice) {
                                    $configPrice = $minPrice + (($maxPrice - $minPrice) / ($sizeCount - 1)) * $i;
                                } else {
                                    $configPrice = $minPrice ?: $maxPrice;
                                }
                            }
                            $configs[] = [
                                'configuration' => $configBhk,
                                'size_value' => $configSize,
                                'size_unit' => $unit,
                                'total_units' => 10,
                                'available_units' => 8,
                                'price' => $configPrice ? (int) $configPrice : null,
                            ];
                        }
                    } else {
                        $configs[] = [
                            'configuration' => $configBhk,
                            'size_value' => $sizes[0] ?? 1000,
                            'size_unit' => $unit,
                            'total_units' => 10,
                            'available_units' => 8,
                            'price' => $minPrice ? (int) $minPrice : null,
                        ];
                    }
                }
            }

            $overview['unit_configurations'] = $configs;
            $property->overview = $overview;
            $property->save();
            $count++;
        }

        $this->info("Successfully migrated configurations for {$count} properties.");

        return self::SUCCESS;
    }
}
