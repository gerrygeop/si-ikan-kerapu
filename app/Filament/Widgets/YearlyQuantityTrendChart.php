<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class YearlyQuantityTrendChart extends ChartWidget
{
    protected static ?string $heading = 'Tren Kuantitas Pemasukan Tahunan (Kg)';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        // Ambil data untuk 5 tahun terakhir (atau sesuai kebutuhan)
        $years = range(Carbon::now()->subYears(4)->year, Carbon::now()->year); // Misal 5 tahun terakhir
        $data = Transaction::select(
            DB::raw('YEAR(entry_datetime) as year'),
            DB::raw('SUM(quantity) as total_quantity')
        )
            ->whereIn(DB::raw('YEAR(entry_datetime)'), $years)
            ->groupBy('year')
            ->orderBy('year')
            ->get();

        // Isi tahun yang tidak ada data dengan 0
        $quantitiesByYear = $data->pluck('total_quantity', 'year')->toArray();
        $chartData = [];
        foreach ($years as $year) {
            $chartData[] = $quantitiesByYear[$year] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Kuantitas (Kg)',
                    'data' => $chartData,
                    'backgroundColor' => '#36A2EB',
                    'borderColor' => '#36A2EB',
                    'fill' => false, // Garis saja
                ],
            ],
            'labels' => $years,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
