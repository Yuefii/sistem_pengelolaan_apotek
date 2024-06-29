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
            Actions\Action::make('download_pdf')
                ->label('Download PDF')
                ->color('danger')
                ->url(fn () => route('transaksi.download-pdf', request()->query()))
                ->icon('heroicon-o-arrow-down-tray'),
        ];
    }
}
