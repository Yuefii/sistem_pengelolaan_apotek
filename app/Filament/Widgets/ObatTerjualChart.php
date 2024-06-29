<?php

namespace App\Filament\Widgets;

use App\Models\Transaksi;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ObatTerjualChart extends ChartWidget
{
    protected static ?string $heading = 'Jumlah Obat Terjual';

    protected function getData(): array
    {
        // Query untuk menghitung jumlah obat terjual per bulan
        $data = Transaksi::select(
            DB::raw('SUM(jumlah) as jumlah_terjual'),
            DB::raw('EXTRACT(MONTH FROM tanggal) as bulan')
        )
            ->groupBy(DB::raw('EXTRACT(MONTH FROM tanggal)'))
            ->orderBy(DB::raw('EXTRACT(MONTH FROM tanggal)'))
            ->get();

        // Inisialisasi data untuk chart
        $jumlahObatTerjual = [];
        $labels = [];

        // Memproses data untuk dimasukkan ke dalam chart
        foreach ($data as $item) {
            $jumlahObatTerjual[] = $item->jumlah_terjual;
            $labels[] = date('M', mktime(0, 0, 0, $item->bulan, 1));
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Obat Terjual',
                    'data' => $jumlahObatTerjual,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
