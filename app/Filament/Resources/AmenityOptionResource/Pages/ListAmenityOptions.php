<?php

namespace App\Filament\Resources\AmenityOptionResource\Pages;

use App\Filament\Resources\AmenityOptionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAmenityOptions extends ListRecords
{
    protected static string $resource = AmenityOptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
