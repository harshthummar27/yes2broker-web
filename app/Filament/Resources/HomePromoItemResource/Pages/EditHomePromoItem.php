<?php

namespace App\Filament\Resources\HomePromoItemResource\Pages;

use App\Filament\Resources\HomePromoItemResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHomePromoItem extends EditRecord
{
    protected static string $resource = HomePromoItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
