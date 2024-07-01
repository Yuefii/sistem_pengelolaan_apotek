<?php

namespace App\Filament\Resources\TransaksiResource\Pages;

use App\Filament\Resources\TransaksiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTransaksis extends ListRecords
{
    protected static string $resource = TransaksiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Membuat Transaksi')
                ->icon('heroicon-o-plus-circle'),
            Actions\Action::make('exportPdf')
                ->label('Export to PDF')
                ->color('danger')
                ->icon('heroicon-o-arrow-down-tray')
                ->url(fn () => route('transaksi.export-pdf'))
                ->openUrlInNewTab()
        ];
    }
}
