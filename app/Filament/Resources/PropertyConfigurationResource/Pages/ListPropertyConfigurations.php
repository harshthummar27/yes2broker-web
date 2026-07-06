<?php

namespace App\Filament\Resources\PropertyConfigurationResource\Pages;

use App\Filament\Resources\PropertyConfigurationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPropertyConfigurations extends ListRecords
{
    protected static string $resource = PropertyConfigurationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
