<?php

namespace App\Filament\Resources\ObatResource\Widgets;

use App\Models\Obat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ObatOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $startDate = $this->filters['startDate'] ?? null;
        $endDate = $this->filters['endDate'] ?? null;
        return [
            //
            StatsOverviewWidget\Stat::make(
                label: 'Jumlah Obat',
                value: Obat::query()
                    ->when($startDate, fn (ObatOverview $query) => $query->whereDate('created_at', '>=', $startDate))
                    ->when($endDate, fn (ObatOverview $query) => $query->whereDate('created_at', '<=', $endDate))
                    ->count(),
            ),
        ];
    }
}
