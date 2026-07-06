<?php

namespace App\Filament\Resources\PropertyResource\Pages;

use App\Filament\Resources\PropertyResource;
use App\Models\Property;
use App\Services\PropertyListingUnitService;
use App\Support\IndianPrice;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditProperty extends EditRecord
{
    protected static string $resource = PropertyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\Action::make('view')
                ->label('View on site')
                ->url(fn (Property $record): string => route('properties.show', $record->slug))
                ->openUrlInNewTab()
                ->icon('heroicon-o-arrow-top-right-on-square'),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (blank($data['project_area_value'] ?? null) && filled($data['area'] ?? null)) {
            $parsed = Property::parseProjectArea($data['area']);
            $data['project_area_value'] = $parsed['value'];
            $data['project_area_unit'] = $parsed['unit']
                ?? app(\App\Services\LookupOptionService::class)->defaultProjectUnitName();
        }

        if (blank($data['project_area_unit'] ?? null)) {
            $data['project_area_unit'] = app(\App\Services\LookupOptionService::class)->defaultProjectUnitName();
        }

        if (Property::isReadyToMove($data['possession'] ?? null)) {
            $data['possession_is_ready'] = true;
            $data['possession_date'] = null;
        } elseif (blank($data['possession_date'] ?? null) && filled($data['possession'] ?? null)) {
            $data['possession_date'] = Property::parsePossessionDate($data['possession']);
        }

        if (blank($data['price_min_amount'] ?? null) && filled($data['price'] ?? null)) {
            $parsed = IndianPrice::parseRange($data['price']);
            $data['price_min_amount'] = $parsed['min'] !== null ? (int) round($parsed['min']) : null;
            $data['price_max_amount'] = $parsed['max'] !== null ? (int) round($parsed['max']) : null;
        }

        $launchDate = $data['overview']['launch_date'] ?? null;

        if (filled($launchDate) && ! preg_match('/^\d{4}-\d{2}-\d{2}/', (string) $launchDate)) {
            $parsedLaunchDate = Property::parseLaunchDate((string) $launchDate);

            if ($parsedLaunchDate !== null) {
                $data['overview']['launch_date'] = $parsedLaunchDate;
            }
        }

        if (! array_key_exists('show_street_view', $data) || $data['show_street_view'] === null) {
            $data['show_street_view'] = true;
        }

        $image = $data['image'] ?? null;

        if (filled($image) && ! str_starts_with((string) $image, 'http')) {
            $data['image_upload'] = $image;
        }

        $gallery = $data['gallery'] ?? [];
        $urlGallery = [];
        $uploadGallery = [];

        foreach ($gallery as $item) {
            $path = is_array($item) ? ($item['url'] ?? null) : $item;

            if (blank($path)) {
                continue;
            }

            if (str_starts_with((string) $path, 'http')) {
                $urlGallery[] = ['url' => $path];
            } else {
                $uploadGallery[] = $path;
            }
        }

        $data['gallery'] = $urlGallery;
        $data['gallery_uploads'] = $uploadGallery;

        $brochure = $data['brochure_url'] ?? null;

        if (filled($brochure) && ! str_starts_with((string) $brochure, 'http')) {
            $data['brochure_upload'] = $brochure;
        }

        $listingUnits = app(PropertyListingUnitService::class)->normalizedListForProperty($this->getRecord());

        if ($listingUnits !== []) {
            $data['overview'] = is_array($data['overview'] ?? null) ? $data['overview'] : [];
            $data['overview']['unit_configurations'] = $listingUnits;
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data = CreateProperty::normalizeMediaFields($data);

        CreateProperty::validateUnitConfigurations($data);

        return CreateProperty::normalizeListingFields($data);
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Property updated in database')
            ->body('Changes are visible on the public website.');
    }
}
