<?php

namespace App\Filament\Resources\PropertyResource\Pages;

use App\Filament\Resources\PropertyResource;
use App\Models\Property;
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

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return CreateProperty::normalizeMediaFields($data);
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Property updated in database')
            ->body('Changes are visible on the public website.');
    }
}
