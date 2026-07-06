<?php

namespace App\Filament\Resources\ProjectUnitResource\Pages;

use App\Filament\Resources\ProjectUnitResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProjectUnits extends ListRecords
{
    protected static string $resource = ProjectUnitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
