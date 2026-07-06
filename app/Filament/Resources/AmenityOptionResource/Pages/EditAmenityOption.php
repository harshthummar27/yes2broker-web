<?php

namespace App\Filament\Resources\AmenityOptionResource\Pages;

use App\Filament\Resources\AmenityOptionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAmenityOption extends EditRecord
{
    protected static string $resource = AmenityOptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
