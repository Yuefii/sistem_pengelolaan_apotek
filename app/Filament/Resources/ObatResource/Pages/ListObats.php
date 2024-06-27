<?php

namespace App\Filament\Resources\ObatResource\Pages;

use App\Filament\Resources\ObatResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListObats extends ListRecords
{
    protected static string $resource = ObatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
