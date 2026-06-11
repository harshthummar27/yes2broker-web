<?php

namespace App\Filament\Resources\PropertyResource\Pages;

use App\Filament\Resources\PropertyResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateProperty extends CreateRecord
{
    protected static string $resource = PropertyResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data = self::normalizeMediaFields($data);

        if (blank($data['image'] ?? null)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'image' => 'Please upload a featured image or provide an image URL.',
            ]);
        }

        return $data;
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

        return $data;
    }
}
