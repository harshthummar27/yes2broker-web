<?php

namespace App\Filament\Resources\PropertyResource\Pages;

use App\Filament\Resources\PropertyResource;
use App\Support\IndianPrice;
use App\Support\MapEmbed;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateProperty extends CreateRecord
{
    protected static string $resource = PropertyResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $unitConfigs = is_array($data['overview']['unit_configurations'] ?? null)
            ? $data['overview']['unit_configurations']
            : [];

        if ($unitConfigs !== []) {
            $prices = array_filter(
                array_map(fn($item) => isset($item['price']) && $item['price'] !== '' ? (float) $item['price'] : null, $unitConfigs),
                fn($p) => $p !== null && $p > 0
            );

            if (!empty($prices)) {
                $data['price_min_amount'] = (int) min($prices);
                if (count($prices) > 1 && max($prices) > min($prices)) {
                    $data['price_max_amount'] = (int) max($prices);
                } else {
                    $data['price_max_amount'] = null;
                }
            }
        }

        $data = self::normalizeMediaFields($data);

        if (blank($data['image'] ?? null)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'image' => 'Please upload a featured image or provide an image URL.',
            ]);
        }

        if (empty($data['possession_is_ready']) && blank($data['possession_date'] ?? null)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'possession_date' => 'Please select a possession month and year, or mark Ready to Move.',
            ]);
        }

        if (blank($data['price_min_amount'] ?? null)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'overview.unit_configurations' => 'Please enter a price for at least one configuration.',
            ]);
        }

        self::validateUnitConfigurations($data);

        return self::normalizeListingFields($data);
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Property saved to database')
            ->body('The property is now live on the website (if Published is enabled).');
    }

    public static function normalizeMediaFields(array $data): array
    {
        if (! empty($data['image_upload'])) {
            $data['image'] = is_array($data['image_upload'])
                ? (string) reset($data['image_upload'])
                : (string) $data['image_upload'];
        }

        $gallery = $data['gallery'] ?? [];

        if (! empty($data['gallery_uploads'])) {
            $uploads = is_array($data['gallery_uploads']) ? $data['gallery_uploads'] : [$data['gallery_uploads']];
            $gallery = array_merge($gallery, array_values($uploads));
        }

        if (! empty($gallery)) {
            $data['gallery'] = array_values(array_filter(array_map(
                fn ($item) => is_array($item) ? ($item['url'] ?? null) : $item,
                $gallery
            )));
        }

        unset($data['image_upload'], $data['gallery_uploads']);

        if (array_key_exists('brochure_upload', $data)) {
            $upload = $data['brochure_upload'];

            if (filled($upload)) {
                $data['brochure_url'] = is_array($upload)
                    ? (string) reset($upload)
                    : (string) $upload;
            } elseif (! str_starts_with((string) ($data['brochure_url'] ?? ''), 'http')) {
                $data['brochure_url'] = null;
            }

            unset($data['brochure_upload']);
        }

        $data['map_embed_url'] = MapEmbed::normalizeEmbedInput($data['map_embed_url'] ?? null);
        $data['street_view_embed_url'] = MapEmbed::normalizeEmbedInput($data['street_view_embed_url'] ?? null);

        return $data;
    }

    public static function normalizeListingFields(array $data): array
    {
        if (! empty($data['possession_is_ready'])) {
            $data['possession'] = 'Ready to Move';
            $data['possession_date'] = null;
        } elseif (filled($data['possession_date'] ?? null)) {
            $date = Carbon::parse($data['possession_date'])->startOfMonth();
            $data['possession_date'] = $date->format('Y-m-d');
            $data['possession'] = $date->format('F Y');
        }

        unset($data['possession_is_ready']);

        if (filled($data['price_min_amount'] ?? null)) {
            $min = (float) $data['price_min_amount'];
            $max = filled($data['price_max_amount'] ?? null) ? (float) $data['price_max_amount'] : null;
            $data['price'] = IndianPrice::formatRange($min, $max);
            $data['price_min_lakhs'] = IndianPrice::toMinLakhs($min);
            $data['price_min_amount'] = (int) round($min);
            $data['price_max_amount'] = ($max !== null && $max > $min) ? (int) round($max) : null;
        }

        $data['overview'] = \App\Support\PropertyOverview::buildPayload($data);
        \App\Support\PropertyUnitConfiguration::syncBhkOnPropertyData($data);

        return $data;
    }

    public static function validateUnitConfigurations(array $data): void
    {
        $overview = is_array($data['overview'] ?? null) ? $data['overview'] : [];
        $items = \App\Support\PropertyUnitConfiguration::normalizeList(
            is_array($overview['unit_configurations'] ?? null) ? $overview['unit_configurations'] : []
        );

        if ($items === []) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'overview.unit_configurations' => 'Add at least one configuration in Overview → Configurations, Sizes & Units.',
            ]);
        }
    }
}
