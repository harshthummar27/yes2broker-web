<?php

namespace App\Filament\Resources\HomePromoItemResource\Pages;

use App\Filament\Resources\HomePromoItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHomePromoItems extends ListRecords
{
    protected static string $resource = HomePromoItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
