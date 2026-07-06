<?php

namespace App\Filament\Resources\PropertyConfigurationResource\Pages;

use App\Filament\Resources\PropertyConfigurationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPropertyConfiguration extends EditRecord
{
    protected static string $resource = PropertyConfigurationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
