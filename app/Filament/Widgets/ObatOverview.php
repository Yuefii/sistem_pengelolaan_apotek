<?php

namespace App\Filament\Widgets;

use App\Models\Obat;
use App\Models\Transaksi;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ObatOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $jumlahJenisObat = Obat::distinct()->count('nama_obat');
        $jumlahTransaksi = Transaksi::distinct()->count('id');
        $jumlah = Transaksi::sum('jumlah');
        $totalStok = Obat::sum('total_stok_obat');
        return [
            //
            Stat::make('Jumlah Stok Obat Saat Ini', $totalStok)
                ->description($jumlah . ' Obat telah terjual')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('Jumlah Jenis Obat', $jumlahJenisObat),
            Stat::make('Jumlah Total Transaksi', $jumlahTransaksi),
        ];
    }
}
