<?php

namespace App\Filament\Guest\Widgets;

use App\Models\Transaction;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class MonthlyFishPriceTrendChart extends ChartWidget
{
    protected static ?string $heading = 'Tren Harga Rata-rata Kerapu Bulanan (Rp/Kg)';

    protected function getData(): array
    {
        // Ambil data untuk 12 bulan terakhir
        $months = collect();
        for ($i = 11; $i >= 0; $i--) { // Loop dari 11 bulan lalu sampai bulan ini
            $months->push(Carbon::now()->subMonths($i));
        }

        $data = Transaction::select(
            DB::raw('DATE_FORMAT(entry_datetime, "%Y-%m") as period'),
            DB::raw('AVG(price_per_unit) as average_price') // Menghitung rata-rata harga
        )
            // Filter transaksi hanya untuk 12 bulan terakhir
            ->whereBetween('entry_datetime', [$months->first()->startOfMonth(), Carbon::now()->endOfMonth()])
            ->groupBy('period')
            ->orderBy('period')
            ->get();

        $pricesByPeriod = $data->pluck('average_price', 'period')->toArray();
        $labels = [];
        $prices = [];

        // Isi data untuk setiap bulan, jika tidak ada transaksi, nilainya 0
        foreach ($months as $month) {
            $periodKey = $month->format('Y-m');
            $labels[] = $month->format('M Y'); // Format label: "Jul 2025"
            $prices[] = $pricesByPeriod[$periodKey] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Harga Rata-rata (Rp/Kg)',
                    'data' => $prices,
                    'backgroundColor' => '#FF6384', // Warna untuk tren harga
                    'borderColor' => '#FF6384',
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
                        'text' => 'Harga (Rp)',
                    ],
                ],
                'x' => [
                    'title' => [
                        'display' => true,
                        'text' => 'Bulan',
                    ],
                ],
            ],
            'plugins' => [
                'tooltip' => [
                    'callbacks' => [
                        // Memformat nilai tooltip agar ada "Rp " dan format mata uang Indonesia
                        'label' => 'function(context) { return context.dataset.label + ": Rp " + context.parsed.y.toLocaleString("id-ID"); }'
                    ]
                ]
            ]
        ];
    }


    protected function getType(): string
    {
        return 'line';
    }
}
