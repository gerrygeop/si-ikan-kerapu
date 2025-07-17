<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class MonthlyQuantityTrendChart extends ChartWidget
{
    protected static ?string $heading = 'Tren Kuantitas Pemasukan Bulanan (Kg)';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        // Ambil data untuk 12 bulan terakhir
        $months = collect();
        for ($i = 11; $i >= 0; $i--) { // Loop dari 11 bulan lalu sampai bulan ini
            $months->push(Carbon::now()->subMonths($i));
        }

        $data = Transaction::select(
            DB::raw('DATE_FORMAT(entry_datetime, "%Y-%m") as period'),
            DB::raw('SUM(quantity) as total_quantity')
        )
            // Filter transaksi hanya untuk 12 bulan terakhir
            ->whereBetween('entry_datetime', [$months->first()->startOfMonth(), Carbon::now()->endOfMonth()])
            ->groupBy('period')
            ->orderBy('period')
            ->get();

        $quantitiesByPeriod = $data->pluck('total_quantity', 'period')->toArray();
        $labels = [];
        $quantities = [];

        // Isi data untuk setiap bulan, jika tidak ada transaksi, nilainya 0
        foreach ($months as $month) {
            $periodKey = $month->format('Y-m');
            $labels[] = $month->format('M Y'); // Format label: "Jul 2024"
            $quantities[] = $quantitiesByPeriod[$periodKey] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Kuantitas (Kg)',
                    'data' => $quantities,
                    'backgroundColor' => '#36A2EB',
                    'borderColor' => '#36A2EB',
                    'fill' => false, // Untuk line chart tanpa area di bawahnya
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Kuantitas (Kg)',
                    ],
                ],
                'x' => [
                    'title' => [
                        'display' => true,
                        'text' => 'Bulan',
                    ],
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
