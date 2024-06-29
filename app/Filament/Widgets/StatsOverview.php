<?php

namespace App\Filament\Widgets;

use App\Models\Transaksi;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function calculateTotalPemasukan()
    {
        return Transaksi::sum('total_harga');
    }

    protected function getTotalPendapatan(): Stat
    {
        $totalPemasukan = $this->calculateTotalPemasukan();

        return Stat::make('Total Pendapatan', 'Rp.' . number_format($totalPemasukan, 0, ',', '.'));
    }

    protected function getPendapatanMingguIni(): Stat
    {
        $startDateThisWeek = Carbon::now()->startOfWeek();
        $endDateThisWeek = Carbon::now()->endOfWeek();
        $totalPemasukanThisWeek = Transaksi::whereBetween('tanggal', [$startDateThisWeek, $endDateThisWeek])->sum('total_harga');

        $lastWeekStats = $this->getPendapatanMingguLalu();
        $totalPemasukanLastWeek = (float) str_replace(['Rp.', '.', ','], '', $lastWeekStats->getValue());

        $percentageChange = 0;
        $descriptionIcon = 'heroicon-m-arrow-right';
        $color = 'success';

        if ($totalPemasukanLastWeek > 0) {
            $percentageChange = (($totalPemasukanThisWeek - $totalPemasukanLastWeek) / $totalPemasukanLastWeek) * 100;
        }

        $description = number_format(abs($percentageChange), 2) . '% ' . ($percentageChange >= 0 ? 'pendapatan minggu ini naik' : 'pendapatan minggu ini menurun');
        $descriptionIcon = $percentageChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down';
        $color = $percentageChange >= 0 ? 'success' : 'danger';

        return Stat::make('Pendapatan Minggu Ini', 'Rp.' . number_format($totalPemasukanThisWeek, 0, ',', '.'))
            ->description($description)
            ->descriptionIcon($descriptionIcon)
            ->color($color);
    }

    protected function getPendapatanMingguLalu(): Stat
    {
        $startDateLastWeek = Carbon::now()->subWeek()->startOfWeek();
        $endDateLastWeek = Carbon::now()->subWeek()->endOfWeek();
        $totalPemasukanLastWeek = Transaksi::whereBetween('tanggal', [$startDateLastWeek, $endDateLastWeek])->sum('total_harga');

        return Stat::make('Pendapatan Minggu Lalu', 'Rp.' . number_format($totalPemasukanLastWeek, 0, ',', '.'));
    }

    protected function getSelisihPendapatan(): Stat
    {
        $lastWeekStats = $this->getPendapatanMingguLalu();
        $totalPemasukanLastWeek = (float) str_replace(['Rp.', '.', ','], '', $lastWeekStats->getValue());

        $startDateThisWeek = Carbon::now()->startOfWeek();
        $endDateThisWeek = Carbon::now()->endOfWeek();
        $totalPemasukanThisWeek = Transaksi::whereBetween('tanggal', [$startDateThisWeek, $endDateThisWeek])->sum('total_harga');

        $selisihPemasukan = $totalPemasukanThisWeek - $totalPemasukanLastWeek;

        return Stat::make('Selisih Pendapatan', 'Rp.' . number_format($selisihPemasukan, 0, ',', '.'))
            ->description('Selisih dengan minggu lalu')
            ->descriptionIcon('heroicon-m-arrow-right')
            ->color($selisihPemasukan >= 0 ? 'success' : 'danger');
    }

    protected function getStats(): array
    {
        return [
            $this->getTotalPendapatan(),
            $this->getPendapatanMingguLalu(),
            $this->getPendapatanMingguIni(),
            $this->getSelisihPendapatan(),
        ];
    }
}
