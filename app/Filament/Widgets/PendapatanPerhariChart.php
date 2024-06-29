<?php

namespace App\Filament\Widgets;

use App\Models\Transaksi;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class PendapatanPerhariChart extends ChartWidget
{
    protected static ?string $heading = 'Pendapatan Perhari';

    protected function getData(): array
    {
        $transactions = Transaksi::whereYear('tanggal', now()->year)
            ->whereMonth('tanggal', now()->month)
            ->get();

        $totalPendapatan = [];
        $startDate = now()->startOfMonth();
        $endDate = now()->endOfMonth();
        $currentDate = $startDate;
        while ($currentDate <= $endDate) {
            $totalPendapatan[$currentDate->format('d-M')] = 0;
            $currentDate->addDay();
        }
        foreach ($transactions as $transaction) {
            $transactionDate = Carbon::parse($transaction->tanggal)->format('d-M');
            $totalPendapatan[$transactionDate] += $transaction->total_harga;
        }
        $dataForChart = [
            'labels' => array_keys($totalPendapatan),
            'datasets' => [
                [
                    'label' => 'Pendapatan Perhari',
                    'data' => array_values($totalPendapatan),
                ],
            ],
        ];

        return $dataForChart;
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
